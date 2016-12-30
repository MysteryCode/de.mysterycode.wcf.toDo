<?php

namespace wcf\data\todo;
use wcf\data\todo\category\TodoCategory;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\WCF;

/**
 * Represents a list of watched todos.
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class WatchedToDoList extends ViewableToDoList {
	/**
	 * Creates a new object.
	 */
	public function __construct() {
		parent::__construct();
		
		$categoryIDs = TodoCategory::getAccessibleCategoryIDs();
		if (empty($categoryIDs)) {
			$this->getConditionBuilder()->add('1=0');
		} else {
			$objectTypeID = UserObjectWatchHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo');
			
			// add conditions
			$this->sqlConditionJoins = "LEFT JOIN wcf".WCF_N."_todo todo ON (todo.todoID = user_object_watch.objectID)";
			
			$this->getConditionBuilder()->add('user_object_watch.objectTypeID = ?', array($objectTypeID));
			$this->getConditionBuilder()->add('user_object_watch.userID = ?', array(WCF::getUser()->userID));
			$this->getConditionBuilder()->add('todo.categoryID IN (?)', array($categoryIDs));
			$this->getConditionBuilder()->add('todo.isDeleted = 0 AND todo.isDisabled = 0');
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_user_object_watch user_object_watch
			".$this->sqlConditionJoins."
			".$this->getConditionBuilder()->__toString();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()->getParameters());
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * @inheritDoc
	 */
	public function readObjectIDs() {
		$this->objectIDs = array();
		$sql = "SELECT	user_object_watch.objectID AS objectID
			FROM	wcf".WCF_N."_user_object_watch user_object_watch
				".$this->sqlConditionJoins."
				".$this->getConditionBuilder()->__toString()."
				".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$statement = WCF::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
		$statement->execute($this->getConditionBuilder()->getParameters());
		
		while ($row = $statement->fetchArray()) {
			$this->objectIDs[] = $row['objectID'];
		}
	}
}
