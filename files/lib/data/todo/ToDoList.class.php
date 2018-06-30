<?php

namespace wcf\data\todo;
use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

/**
 * Represents the list of todos.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 *
 * @method ToDo[] getObjects()
 * @property ToDo[] $objects
 */
class ToDoList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = ToDo::class;

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		parent::__construct ();
		
		if (!empty($this->sqlSelects))
			$this->sqlSelects .= ',';
		
		// visible status
		if (!WCF::getSession()->getPermission('mod.toDo.canViewDeleted')) {
			$this->getConditionBuilder()->add('todo_table.isDeleted = 0');
		}
		
		if ($this->sqlOrderBy == 'statusID ASC' || $this->sqlOrderBy == 'statusID DESC') {
			$this->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_todo_status status ON (todo_table.statusID = status.statusID)";
			$this->sqlOrderBy .= ($this->sqlOrderBy == 'statusID ASC' ? 'status.showOrder ASC' : 'status.showOrder DESC');
		}
		
		if (!WCF::getSession()->getPermission('mod.toDo.canEnable'))
			$this->getConditionBuilder()->add('todo_table.isDisabled = 0');
		
		if (!WCF::getSession()->getPermission('user.toDo.toDo.canViewPrivate'))
			$this->getConditionBuilder()->add("(private = ? or submitter = ? or (SELECT assigns.todoID FROM wcf" . WCF_N . "_todo_to_user assigns WHERE assigns.toDoID = todo_table.todoID AND assigns.userID = ?) = todo_table.todoID)", [0, WCF::getUser()->userID, WCF::getUser()->userID]);
	}
}
