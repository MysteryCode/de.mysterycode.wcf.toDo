<?php

namespace wcf\data\todo;
use wcf\data\todo\category\TodoCategory;

/**
 * Represents a list of accessible todos.
 * 
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
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
			$this->getConditionBuilder()->add("todo.categoryID IN (?)", array($categoryIDs));
		} else {
			$this->getConditionBuilder()->add("1=0");
		}
		
		// add default conditions
		$this->getConditionBuilder()->add("todo.isDeleted = 0 AND todo.isDisabled = 0");
	}
}
