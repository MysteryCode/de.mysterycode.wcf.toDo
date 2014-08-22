<?php
namespace wcf\data\todo;
use wcf\data\DatabaseObjectList;
use wcf\system\clipboard\ClipboardHandler;

/**
 * Represents the list of todos.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class DeletedToDoList extends ToDoList {

	/**
	 * @see	\wcf\data\DatabaseObjectList::$sqlOrderBy
	 */
	public $sqlOrderBy = 'todo_table.timestamp DESC';
	
	/**
	 *
	 * @see \wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\todo\ToDo';
	
	public function __construct() {
		parent::__construct();
	
		// add condition
		$this->getConditionBuilder()->add('todo_table.isDeleted = ?', array(1));
	}
	
	public function getMarkedItems() {
		$objectTypeID = ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo');
		return ClipboardHandler::getInstance()->hasMarkedItems($objectTypeID);
	}
}