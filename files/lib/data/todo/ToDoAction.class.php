<?php
namespace wcf\data\todo;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoList;
use wcf\data\todo\ToDoEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\notification\event\UserNotificationEventList;
use wcf\data\user\notification\UserNotificationList;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\AbstractDatabaseObjectAction;
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
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
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
		$todo = parent::create();
		
		if(!$todo->isDisabled) {
			$todoAction = new ToDoAction(array($todo), 'publish');
			$todoAction->executeAction();
		} else {
			ModerationQueueActivationManager::getInstance()->addModeratedContent('de.mysterycode.wcf.toDo.toDo', $todo->id);
		}
		
		return $todo;
	}
	
	/**
	 * Publishes the given todos for the first time.
	 */
	public function publish() {
		$this->loadTodos();
		
		foreach($this->objects as $todo) {
			if($todo->submitter) {
				UserActivityEventHandler::getInstance()->fireEvent('de.mysterycode.wcf.toDo.toDo.recentActivityEvent', $todo->id, 1, $todo->submitter, $todo->timestamp);
				UserActivityPointHandler::getInstance()->fireEvent('de.mysterycode.wcf.toDo.toDo.activityPointEvent', $todo->id, $todo->submitter);
				ToDoEditor::updateUserToDoCounter(array($todo->submitter => 1));
			}
		}
	}
	
	/**
	 *
	 * @see \wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
		if(isset($this->parameters['data'])) {
			$todoIDs = array();
			foreach($this->objects as $todo) {
				$todoIDs[] = $todo->id;
				$users = array();
				if(WCF::getUser()->userID != $todo->submitter) {
					$users = array_unique($todo->getResponsibleIDs());
				} else {
					$users = array_unique(array_merge(array($todo->submitter), $todo->getResponsibleIDs()));
				}
				if(!empty($users)) {
					if(isset($this->parameters['data']['status']) && !isset($this->parameters['data']['title'])) {
						UserNotificationHandler::getInstance()->fireEvent('editStatus', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todo->id)), $users);
					} else {
						UserNotificationHandler::getInstance()->fireEvent('edit', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todo->id)), $users);
					}
				}
			}
		}
		
		parent::update();
	}
	
	/**
	 * Validating parameters for enabling todos.
	 */
	public function validateEnable() {
		$this->loadTodos();
		
		foreach($this->objects as $todo) {
			if(!$todo->isDisabled || $todo->isDeleted) {
				throw new UserInputException('objectIDs');
			}
			
			if (!$todo->canEnable()) {
				throw new PermissionDeniedException();
			}
		}
	}
	
	/**
	 * Enables given todos.
	 *
	 * @return array<array>
	 */
	public function enable() {
		if(empty($this->objects)) {
			$this->readObjects();
		}
		
		// update todos
		$todoIDs = array();
		foreach($this->objects as $todo) {
			$todo->update(array(
				'isDisabled' => 0 
			));
			
			$todoIDs[] = $todo->id;
			$this->addToDoData($todo->getDecoratedObject(), 'isDisabled', 0);
		}
		
		$todoAction = new ToDoAction($this->objects, 'publish');
		$todoAction->executeAction();
		
		$this->removeModeratedContent($todoIDs);
		
		$this->unmarkToDos();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for disabling todos.
	 */
	public function validateDisable() {
		$this->loadTodos();
		
		foreach($this->objects as $todo) {
			if($todo->isDisabled || $todo->isDeleted) {
				throw new UserInputException( 'objectIDs' );
			}
			
			if (!$todo->canEnable()) {
				throw new PermissionDeniedException();
			}
		}
	}
	
	/**
	 * Disables given todos.
	 *
	 * @return array<array>
	 */
	public function disable() {
		if(empty($this->objects)) {
			$this->readObjects();
		}
		
		$todoIDs = $categoryStats = array();
		foreach($this->objects as $todo) {
			$todo->update(array(
				'isDisabled' => 1 
			));
			
			$todoIDs[] = $todo->id;
			$this->addToDoData($todo->getDecoratedObject(), 'isDisabled', 1);
			
			ModerationQueueActivationManager::getInstance()->addModeratedContent('de.mysterycode.wcf.toDo.toDo', $todo->id);
		}
		
		$this->removeActivityEvents($todoIDs);
		
		$this->unmarkToDos();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for trashing todos.
	 */
	public function validateTrash() {
		$this->loadTodos();
		
		foreach($this->objects as $todo) {
			if($todo->isDeleted) {
				throw new UserInputException('objectIDs');
			}
			
			if (!$todo->canDelete()) {
				throw new PermissionDeniedException();
			}
		}
	}
	
	/**
	 * Trashes given todos.
	 *
	 * @return array<array>
	 */
	public function trash() {
		if(empty($this->objects)) {
			$this->readObjects();
		}
		
		$deleteReason = (isset($this->parameters['data']['reason']) ? StringUtil::trim($this->parameters['data']['reason']) : '');
		
		$todoIDs = array();
		foreach($this->objects as $todo) {
			$todo->update(array(
				'isDeleted' => 1,
				'deleteTime' => TIME_NOW,
				'deletedByID' => WCF::getUser()->userID,
				'deletedBy' => WCF::getUser()->username,
				'deleteReason' => $deleteReason 
			));
			
			$todoIDs[] = $todo->id;
			$this->addToDoData($todo->getDecoratedObject(), 'isDeleted', 1);
			$this->addToDoData($todo->getDecoratedObject(), 'deleteNote', WCF::getLanguage()->getDynamicVariable('wcf.todo.deleteNote', array(
				'todo' => new ToDo($todo->id) 
			)));
		}
		
		$this->unmarkToDos();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for deleting todos.
	 */
	public function validateDelete() {
		$this->loadTodos();
		
		foreach($this->objects as $todo) {
			if(!$todo->isDeleted) {
				throw new UserInputException('objectIDs');
			}
			
			if (!$todo->canDeleteyCompletely()) {
				throw new PermissionDeniedException();
			}
		}
	}
	
	/**
	 * Deletes given todos.
	 *
	 * @return array<array>
	 */
	public function delete() {
		if(empty($this->objects)) {
			$this->readObjects();
		}
		
		$todoIDs = $userCounters = array();
		foreach($this->objects as $todo) {
			$todoIDs[] = $todo->id;
			
			if(! isset($userCounters[$todo->submitter])) {
				$userCounters[$todo->submitter] = 0;
			}
			$userCounters[$todo->submitter] --;
		}
		
		$this->removeActivityEvents($todoIDs);
		
		ToDoEditor::updateUserToDoCounter($userCounters);
		
		UserActivityPointHandler::getInstance()->removeEvents('de.mysterycode.wcf.toDo.toDo.activityPointEvent', $todoIDs);
		
		CommentHandler::getInstance()->deleteObjects('de.mysterycode.wcf.toDo.toDo', $todoIDs);
		
		foreach($this->objects as $todo) {
			$todo->delete();
			
			$this->addToDoData($todo->getDecoratedObject(), 'deleted', LinkHandler::getInstance()->getLink('ToDoList', array()));
		}
		
		$this->unmarkToDos();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for restoring todos.
	 */
	public function validateRestore() {
		$this->loadTodos();
		
		foreach($this->objects as $todo) {
			if(!$todo->isDeleted) {
				throw new UserInputException('objectIDs');
			}
			
			if (!$todo->canRestore()) {
				throw new PermissionDeniedException();
			}
		}
	}
	
	/**
	 * Restores given todos.
	 *
	 * @return array<array>
	 */
	public function restore() {
		if(empty($this->objects)) {
			$this->readObjects();
		}
		
		$todoIDs = array();
		foreach($this->objects as $todo) {
			$todo->update(array(
				'isDeleted' => 0,
				'deleteTime' => 0,
				'deletedByID' => 0,
				'deletedBy' => '',
				'deleteReason' => 0 
			));
			
			$todoIDs[] = $todo->id;
			$this->addToDoData($todo->getDecoratedObject(), 'isDeleted', 0);
		}
		
		$this->unmarkToDos();
		
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
		ClipboardHandler::getInstance()->removeItems(ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo'));
	}
	
	/**
	 * Loads todos for given object ids.
	 */
	protected function loadTodos() {
		if(empty($this->objectIDs)) {
			throw new UserInputException('objectIDs');
		}
		
		if(empty($this->objects)) {
			$this->readObjects();
		}
		
		if(empty($this->objects)) {
			throw new UserInputException('objectIDs');
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
		if(!isset($this->todoData[$todo->id])) {
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
		ModerationQueueActivationManager::getInstance()->removeModeratedContent('de.mysterycode.wcf.toDo.toDo', $todoIDs);
	}
	
	/**
	 * Removes user activity events for the todos with the given todo ids.
	 *
	 * @param array<integer> $todoIDs        	
	 */
	protected function removeActivityEvents(array $todoIDs) {
		UserActivityEventHandler::getInstance()->removeEvents('de.mysterycode.wcf.toDo.toDo.recentActivityEvent', $todoIDs);
		UserActivityPointHandler::getInstance()->removeEvents('de.mysterycode.wcf.toDo.toDo.activityPointEvent', $todoIDs);
	}
	
	/**
	 * Unmarks the todos with the given todo ids.
	 *
	 * @param array<integer> $todoIDs        	
	 */
	protected function unmarkToDos(array $todoIDs = array()) {
		if(empty($todoIDs)) {
			foreach($this->objects as $todo) {
				$todoIDs[] = $todo->id;
			}
		}
		
		if(!empty($todoIDs)) {
			ClipboardHandler::getInstance()->unmark($todoIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo'));
		}
	}
	
	/**
	 * Handles the responsible users od a todo
	 * 
	 * 
	 */
	public function updateResponsibles() {
		foreach($this->objects as $todo) {
			// get responsibles
			$responsibleList = UserProfile::getUserProfilesByUsername(ArrayUtil::trim(explode(',', $this->parameters['userIDs'])));
			
			$userIDs = array();
			$checkArray = array();
			foreach($responsibleList as $user) {
				if($user && !in_array($user->userID, $todo->getResponsibleIDs())) {
					// add new users to list
					$userIDs[] = $user->userID;
					$sql = "INSERT INTO wcf" . WCF_N . "_todo_to_user
						(toDoID, userID, username)
						VAlUES(?, ?, ?);";
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute(array($todoID, $user->userID, $user->username));
			
				}
				// not obsolete users
				$checkArray[] = $user->userID;
			}
			
			// delete obsolete users from list
			foreach($todo->getResponsibleIDs() as $responsible) {
				if(!in_array($responsible, $checkArray)) {
					$sql = "DELETE FROM wcf" . WCF_N . "_todo_to_user
						WHERE toDoID = ?
							AND userID = ?";
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute(array($todoID, $responsible));
				}
			}
			
			// fire notification for new assigned users
			if(!empty($userIDs))
				UserNotificationHandler::getInstance()->fireEvent('assign', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todoID)), $userIDs);
		}
	}
}