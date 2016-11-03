<?php

namespace wcf\data\todo;

/**
 * Represents a list of todos for the dashboard box.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class DashboardBoxTodoList extends ToDoList {
	/**
	 * Creates a new object.
	 */
	public function __construct($maxItems = 5, $categoryID = 0) {
		parent::__construct();
		
		$this->sqlLimit = $maxItems;
		
		if ($categoryID)
			$this->getConditionBuilder()->add('todo_table.categoryID = ?', array($categoryID));
	}
}
