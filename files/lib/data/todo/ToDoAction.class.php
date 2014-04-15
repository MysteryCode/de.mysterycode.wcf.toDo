<?php

namespace wcf\data\todo;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoCache;
use wcf\data\todo\ToDoList;
use wcf\data\todo\ToDoEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\notification\event\UserNotificationEventList;
use wcf\data\user\notification\UserNotificationList;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\cache\builder\ToDoCacheBuilder;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\comment\CommentHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\like\LikeHandler;
use wcf\system\moderation\queue\ModerationQueueActivationManager;
use wcf\system\search\SearchIndexManager;
use wcf\system\user\activity\event\UserActivityEventHandler;
use wcf\system\user\activity\point\UserActivityPointHandler;
use wcf\system\user\notification\object\ToDoUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Executes todo-related actions.
 *
 * @author Florian Gail
 * @copyright 2014 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoAction extends AbstractDatabaseObjectAction {
	/**
	 *
	 * @see \wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'wcf\data\todo\ToDoEditor';
	
	/**
	 * list of todo data
	 *
	 * @var array<array>
	 */
	public $todoData = array();
	
	/**
	 *
	 * @see wcf\data\AbstractDatabaseObjectAction::create()
	 */
	public function create() {
		$languageID =((! isset( $this->parameters['data']['languageID'] ) || $this->parameters['data']['languageID'] === null) ? LanguageFactory::getInstance()->getDefaultLanguageID() : $this->parameters['data']['languageID']);
		
		$todo = parent::create();
		
		if(!$todo->isDisabled) {
			$todoAction = new ToDoAction(array($todo), 'publish');
			$todoAction->executeAction();
		} else {
			ModerationQueueActivationManager::getInstance()->addModeratedContent( 'de.mysterycode.wcf.toDo.toDo', $todo->id );
		}
		
		ToDoCacheBuilder::getInstance()->reset();
		
		return $todo;
	}
	
	/**
	 * Publishes the given todos for the first time.
	 */
	public function publish() {
		$this->loadToDos();
		
		foreach( $this->objects as $todo ) {
			if($todo->submitter) {
				UserActivityEventHandler::getInstance()->fireEvent( 'de.mysterycode.wcf.toDo.toDo.recentActivityEvent', $todo->id, 1, $todo->submitter, $todo->timestamp );
				UserActivityPointHandler::getInstance()->fireEvent( 'de.mysterycode.wcf.toDo.toDo.activityPointEvent', $todo->id, $todo->submitter );
			}
			
			if($todo->submitter) {
				ToDoEditor::updateUserToDoCounter(array($todo->submitter => 1));
			}
		}
		
		ToDoCacheBuilder::getInstance()->reset();
	}
	
	/**
	 *
	 * @see \wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
		if(isset( $this->parameters['data'] )) {
			$todoIDs = array();
			foreach( $this->objects as $todo ) {
				$todoIDs[] = $todo->id;
			}
		}
		
		parent::update();
	}
	
	/**
	 * Validating parameters for enabling todos.
	 */
	public function validateEnable() {
		$this->loadToDos();
		
		foreach( $this->objects as $todo ) {
			if(!$todo->isDisabled || $todo->isDeleted) {
				throw new UserInputException( 'objectIDs' );
			}
		}
	}
	
	/**
	 * Enables given todos.
	 *
	 * @return array<array>
	 */
	public function enable() {
		if(empty( $this->objects )) {
			$this->readObjects();
		}
		
		// update todos
		$todoIDs = array();
		foreach( $this->objects as $todo ) {
			$todo->update( array(
					'isDisabled' => 0 
			) );
			
			$todoIDs[] = $todo->id;
			$this->addToDoData( $todo->getDecoratedObject(), 'isDisabled', 0 );
		}
		
		$todoAction = new ToDoAction( $this->objects, 'publish' );
		$todoAction->executeAction();
		
		$this->removeModeratedContent( $todoIDs );
		
		$this->unmarkToDos();
		
		ToDoCacheBuilder::getInstance()->reset();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for disabling todos.
	 */
	public function validateDisable() {
		$this->loadToDos();
		
		foreach( $this->objects as $todo ) {
			if($todo->isDisabled || $todo->isDeleted) {
				throw new UserInputException( 'objectIDs' );
			}
		}
	}
	
	/**
	 * Disables given todos.
	 *
	 * @return array<array>
	 */
	public function disable() {
		if(empty( $this->objects )) {
			$this->readObjects();
		}
		
		$todoIDs = $categoryStats = array();
		foreach( $this->objects as $todo ) {
			$todo->update( array(
					'isDisabled' => 1 
			) );
			
			$todoIDs[] = $todo->id;
			$this->addToDoData( $todo->getDecoratedObject(), 'isDisabled', 1 );
		}
		
		$this->removeActivityEvents( $todoIDs );
		
		$this->unmarkToDos();
		
		ToDoCacheBuilder::getInstance()->reset();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for trashing todos.
	 */
	public function validateTrash() {
		$this->loadToDos();
		
		foreach( $this->objects as $todo ) {
			if($todo->isDeleted) {
				throw new UserInputException( 'objectIDs' );
			}
		}
	}
	
	/**
	 * Trashes given todos.
	 *
	 * @return array<array>
	 */
	public function trash() {
		if(empty( $this->objects )) {
			$this->readObjects();
		}
		
		$deleteReason =(isset( $this->parameters['data']['reason'] ) ? StringUtil::trim( $this->parameters['data']['reason'] ) : '');
		
		$todoIDs = array();
		foreach( $this->objects as $todo ) {
			$todo->update( array(
				'isDeleted' => 1,
				'deleteTime' => TIME_NOW,
				'deletedByID' => WCF::getUser()->userID,
				'deletedBy' => WCF::getUser()->username,
				'deleteReason' => $deleteReason 
			) );
			
			$todoIDs[] = $todo->id;
			$this->addToDoData( $todo->getDecoratedObject(), 'isDeleted', 1 );
			$this->addToDoData( $todo->getDecoratedObject(), 'deleteNote', WCF::getLanguage()->getDynamicVariable( 'wcf.todo.deleteNote', array(
				'todo' => new ToDo( $todo->id ) 
			) ) );
		}
		
		$this->unmarkToDos();
		
		ToDoCacheBuilder::getInstance()->reset();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for deleting todos.
	 */
	public function validateDelete() {
		$this->loadToDos();
		
		foreach( $this->objects as $todo ) {
			if(!$todo->isDeleted) {
				throw new UserInputException( 'objectIDs' );
			}
		}
	}
	
	/**
	 * Deletes given todos.
	 *
	 * @return array<array>
	 */
	public function delete() {
		if(empty( $this->objects )) {
			$this->readObjects();
		}
		
		$todoIDs = $userCounters = array();
		foreach( $this->objects as $todo ) {
			$todoIDs[] = $todo->id;
			
			if(! isset( $userCounters[$todo->submitter] )) {
				$userCounters[$todo->submitter] = 0;
			}
			$userCounters[$todo->submitter] --;
		}
		
		$this->removeActivityEvents( $todoIDs );
		
		ToDoEditor::updateUserToDoCounter( $userCounters );
		
		UserActivityPointHandler::getInstance()->removeEvents( 'com.wcfsolutions.wsif.activityPointEvent.todo', $todoIDs );
		
		CommentHandler::getInstance()->deleteObjects( 'de.mysterycode.wcf.toDo.toDo', $todoIDs );
		
		foreach( $this->objects as $todo ) {
			$todo->delete();
			
			$this->addToDoData( $todo->getDecoratedObject(), 'deleted', $todo->getCategory()->getLink() );
		}
		
		$this->unmarkToDos();
		
		ToDoCacheBuilder::getInstance()->reset();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for restoring todos.
	 */
	public function validateRestore() {
		$this->loadToDos();
		
		foreach( $this->objects as $todo ) {
			if(!$todo->isDeleted) {
				throw new UserInputException( 'objectIDs' );
			}
		}
	}
	
	/**
	 * Restores given todos.
	 *
	 * @return array<array>
	 */
	public function restore() {
		if(empty( $this->objects )) {
			$this->readObjects();
		}
		
		$todoIDs = array();
		foreach( $this->objects as $todo ) {
			$todo->update( array(
				'isDeleted' => 0,
				'deleteTime' => 0,
				'deletedByID' => 0,
				'deletedBy' => '',
				'deleteReason' => 0 
			) );
			
			$todoIDs[] = $todo->id;
			$this->addToDoData( $todo->getDecoratedObject(), 'isDeleted', 0 );
		}
		
		$this->unmarkToDos();
		
		ToDoCacheBuilder::getInstance()->reset();
		
		return $this->getToDoData();
	}
	
	/**
	 * Does nothing.
	 */
	public function validateUnmarkAll() { }
	
	/**
	 * Unmarks all todos.
	 */
	public function unmarkAll() {
		ClipboardHandler::getInstance()->removeItems( ClipboardHandler::getInstance()->getObjectTypeID( 'de.mysterycode.wcf.toDo.toDo' ) );
	}
	
	/**
	 * Loads todos for given object ids.
	 */
	protected function loadToDos() {
		if(empty( $this->objectIDs )) {
			throw new UserInputException( 'objectIDs' );
		}
		
		if(empty( $this->objects )) {
			$this->readObjects();
		}
		
		if(empty( $this->objects )) {
			throw new UserInputException( 'objectIDs' );
		}
	}
	
	/**
	 * Adds the given todo data.
	 *
	 * @param \wcf\data\todo\ToDo $todo        	
	 * @param string $key        	
	 * @param mixed $value        	
	 */
	protected function addToDoData(ToDo $todo, $key, $value) {
		if(! isset( $this->todoData[$todo->id] )) {
			$this->todoData[$todo->id] = array();
		}
		
		$this->todoData[$todo->id][$key] = $value;
	}
	
	/**
	 * Returns todo data.
	 *
	 * @return array<array>
	 */
	protected function getToDoData() {
		return array(
			'todoData' => $this->todoData 
		);
	}
	
	/**
	 * Removes moderated content todos for the todos with the given todo ids.
	 *
	 * @param array<integer> $todoIDs        	
	 */
	protected function removeModeratedContent(array $todoIDs) {
		ModerationQueueActivationManager::getInstance()->removeModeratedContent( 'de.mysterycode.wcf.toDo.toDo', $todoIDs );
	}
	
	/**
	 * Removes user activity events for the todos with the given todo ids.
	 *
	 * @param array<integer> $todoIDs        	
	 */
	protected function removeActivityEvents(array $todoIDs) {
		UserActivityEventHandler::getInstance()->removeEvents( 'com.wcfsolutions.wsif.recentActivityEvent.todo', $todoIDs );
		UserActivityPointHandler::getInstance()->removeEvents( 'com.wcfsolutions.wsif.activityPointEvent.todo', $todoIDs );
	}
	
	/**
	 * Unmarks the todos with the given todo ids.
	 *
	 * @param array<integer> $todoIDs        	
	 */
	protected function unmarkToDos(array $todoIDs = array()) {
		if(empty( $todoIDs )) {
			foreach( $this->objects as $todo ) {
				$todoIDs[] = $todo->id;
			}
		}
		
		if(! empty( $todoIDs )) {
			ClipboardHandler::getInstance()->unmark( $todoIDs, ClipboardHandler::getInstance()->getObjectTypeID( 'de.mysterycode.wcf.toDo.toDo' ) );
		}
	}
}