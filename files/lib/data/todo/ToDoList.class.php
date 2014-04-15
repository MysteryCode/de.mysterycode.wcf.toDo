<?php

namespace wcf\data\todo;
use wcf\data\DatabaseObjectList;

/**
 * Represents the list of todos.
 *
 * @author Florian Gail
 * @copyright 2014 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
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
		$this->sqlSelects .= "todo_table.*, todo_category.title as categorytitle, todo_category.color as categorycolor";
		$this->sqlJoins = " LEFT JOIN wcf" . WCF_N . "_todo_category todo_category ON (todo_table.category = todo_category.id)";
	}
}