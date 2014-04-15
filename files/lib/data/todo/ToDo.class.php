<?php

namespace wcf\data\todo;
use wcf\data\user\User;
use wcf\data\DatabaseObject;
use wcf\data\ILinkableObject;
use wcf\system\comment\CommentHandler;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\cache\builder\UserOptionCacheBuilder;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\language\LanguageFactory;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a todo.
 *
 * @author Florian Gail
 * @copyright 2014 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
final class ToDo extends DatabaseObject implements IRouteController {
	/**
	 *
	 * @see \wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'todo';
	
	/**
	 *
	 * @see \wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'id';
	
	/**
	 * list of responsible ids
	 *
	 * @var array<integer>
	 */
	protected $responsibleIDs = null;
	
	/**
	 *
	 * @see \wcf\data\DatabaseObject::__construct()
	 */
	public function __construct($id, $row = null, DatabaseObject $object = null) {
		if ($id !== null) {
			$sql = "SELECT		todo_table.*, todo_category.title as categorytitle, todo_category.color as categorycolor
				FROM		wcf" . WCF_N . "_todo todo_table
				LEFT JOIN	wcf" . WCF_N . "_todo_category todo_category
				ON		(todo_table.category = todo_category.id)
				WHERE		todo_table.id = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($id));
			$row = $statement->fetchArray();
			
			// enforce data type 'array'
			if ($row === false)
				$row = array();
		} else if ($object !== null) {
			$row = $object->data;
		}
		
		$this->handleData($row);
	}
	
	public function getResponsibleIDs() {
		$userIDs = array();
		$sql = "SELECT		*
			FROM		wcf" . WCF_N . "_todo_to_user
			WHERE		toDoID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->id));
		
		while ($row = $statement->fetchArray()) {
			$userIDs[] = $row['userID'];
		}
		
		return $userIDs;
	}
	
	/**
	 *
	 * @see \wcf\data\DatabaseObject::__get()
	 */
	public function __get($name) {
		$value = parent::__get($name);
		return $value;
	}
	
	/**
	 * Returns a list of todos.
	 *
	 * @param array $todoIDs        	
	 * @return array<\wcf\data\todo\ToDo>
	 */
	public static function getToDos(array $todoIDs) {
		$todoList = new ToDoList();
		$todoList->getConditionBuilder()->add("todo_table.id IN (?)", array($todoIDs));
		$todoList->readObjects();
		
		return $todoList->getObjects();
	}
	
	/**
	 *
	 * @see \wcf\data\IStorableObject::getDatabaseTableAlias()
	 */
	public static function getDatabaseTableAlias() {
		return 'todo_table';
	}
	
	/**
	 *
	 * @see \wcf\system\request\IRouteController::getTitle()
	 */
	public function getTitle() {
		return $this->title;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getFormattedResponsibles() {
		$string = '';
		foreach($this->getResponsibleIDs() as $responsible) {
			$user = new User($responsible);
			if($user->username != '') {
				$string .= $user->username . ', ';
			}
		}
		return substr($string, 0, -2);
	}
	
	public function getHtmlFormattedResponsibles() {
		$string = '';
		foreach($this->getResponsibleIDs() as $responsible) {
			$user = new User($responsible);
			if($user) {
				$string .= '<a href="' . LinkHandler::getInstance()->getLink('User', array('application' => 'wcf', 'object' => $user)) . '" class="userlink" data-user-id="' . $user->userID . '">' . $user->username . '</a>, ';
			}
		}
		return substr($string, 0, -2);
	}
	
	public function getResponsibles() {
		$responsibleList = array();
		foreach($this->getResponsibleIDs() as $responsible) {
			$responsibleList[] = new User($responsible);
		}
		return $responsibleList;
	}
	
	public function getLink() {
		return LinkHandler::getInstance()->getLink('ToDo', array(
			'application' => 'wcf',
			'id' => $this->id
		));
	}
	
	public function checkPermissions() {
		if ($this->private == 1 && $this->submitter != WCF::getUser ()->userID)
			return false;
		
		return true;
	}
}
