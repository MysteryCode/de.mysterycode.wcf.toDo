<?php

namespace wcf\data\todo;

use wcf\system\clipboard\ClipboardHandler;

/**
 * Represents the list of todos.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class DeletedToDoList extends ToDoList {

	/**
	 * @inheritDoc
	 */
	public $sqlOrderBy = 'todo_table.time DESC';
	
	/**
	 * @inheritDoc
	 */
	public $className = ToDo::class;

	/**
	 * {@inheritDoc}
	 */
	public function __construct() {
		parent::__construct();
		
		// get only deleted items
		$this->getConditionBuilder()->add('todo_table.isDeleted = ?', [1]);
	}
	
	public function getMarkedItems() {
		$objectTypeID = ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo');
		return ClipboardHandler::getInstance()->hasMarkedItems($objectTypeID);
	}
}
