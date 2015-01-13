<?php

namespace wcf\data\todo;
use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

/**
 * Represents the list of todos.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoList extends DatabaseObjectList {
	/**
	 *
	 * @see \wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\todo\ToDo';
	
	/**
	 *
	 * @see \wcf\data\DatabaseObjectList::__construct()
	 */
	public function __construct() {
		parent::__construct ();
		
		if (!empty($this->sqlSelects)) $this->sqlSelects .= ',';
		$this->sqlSelects .= "todo_category.title as categorytitle, todo_category.color as categorycolor";
		$this->sqlJoins = " LEFT JOIN wcf" . WCF_N . "_todo_category todo_category ON (todo_table.category = todo_category.id)";
		// visible status
		if (!WCF::getSession()->getPermission('mod.toDo.canViewDeleted')) {
			$this->getConditionBuilder()->add('todo_table.isDeleted = 0');
			$this->loadDeleteNote = false;
		}
		if (!WCF::getSession()->getPermission('mod.toDo.canEnable')) {
			$this->getConditionBuilder()->add('todo_table.isDisabled = 0');
		}
		if (!WCF::getSession()->getPermission('user.toDo.toDo.canViewPrivate')) {
			$this->getConditionBuilder()->add("(private = ? or submitter = ? or (SELECT assigns.toDoID FROM wcf" . WCF_N . "_todo_to_user assigns WHERE assigns.toDoID = todo_table.id AND assigns.userID = ?) = todo_table.id)", array (0, WCF::getUser()->userID, WCF::getUser()->userID));
		}
	}
}
