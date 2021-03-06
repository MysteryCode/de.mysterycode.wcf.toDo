<?php

namespace wcf\data\todo;
use wcf\data\todo\category\TodoCategory;

/**
 * Represents a list of accessible todos.
 * 
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class AccessibleToDoList extends ViewableToDoList {
	/**
	 * Creates a new object.
	 */
	public function __construct() {
		parent::__construct();
		
		// get category ids
		$categoryIDs = TodoCategory::getAccessibleCategoryIDs();
		
		if (!empty($categoryIDs)) {
			$this->getConditionBuilder()->add("todo_table.categoryID IN (?)", [$categoryIDs]);
		} else {
			$this->getConditionBuilder()->add("1=0");
		}
		
		// add default conditions
		$this->getConditionBuilder()->add("todo_table.isDeleted = 0");
		$this->getConditionBuilder()->add("todo_table.isDisabled = 0");
	}
}
