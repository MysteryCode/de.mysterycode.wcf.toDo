<?php

namespace wcf\data\todo;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Represents a list of todos for the dashboard box.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
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
