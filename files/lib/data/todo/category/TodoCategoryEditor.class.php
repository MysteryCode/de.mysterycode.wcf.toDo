<?php

namespace wcf\data\todo\category;
use wcf\data\category\CategoryEditor;
use wcf\data\todo\ToDo;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\TodoCategoryACLOptionCacheBuilder;
use wcf\system\cache\builder\TodoCategoryDataCacheBuilder;
use wcf\system\cache\builder\TodoCategoryLabelGroupCacheBuilder;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Provides functions to edit categories.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryEditor extends CategoryEditor implements IEditableCachedObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = TodoCategory::class;

	/**
	 * Sets the given todo as the last todos for this category.
	 *
	 * @param    \wcf\data\todo\ToDo $todo
	 * @throws \wcf\system\database\exception\DatabaseQueryException
	 * @throws \wcf\system\database\exception\DatabaseQueryExecutionException
	 */
	public function setLastTodo(ToDo $todo) {
		$currentLastTodo = $this->getDecoratedObject()->getLastTodo();
		if ($currentLastTodo != null && $todo->time <= $currentLastTodo->time) {
			return;
		}
		
		$sql = "DELETE FROM	wcf".WCF_N."_todo_category_last_todo
			WHERE		categoryID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->categoryID]);
		
		// update new
		$sql = "INSERT INTO	wcf".WCF_N."_todo_category_last_todo
					(categoryID, todoID)
			VALUES		(?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->categoryID, $todo->todoID]);
	}
	
	/**
	 * Refreshes the last todo of this category.
	 */
	public function refreshLastTodo() {
		$sql = "DELETE FROM	wcf".WCF_N."_todo_category_last_todo
			WHERE		categoryID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->categoryID]);
		
		$sql = "SELECT		id as todoID
			FROM 		wcf".WCF_N."_todo
			WHERE 		categoryID = ?
					AND isDeleted = ?
					AND isDisabled = ?
			ORDER BY 	time DESC";
		$statement = WCF::getDB()->prepareStatement($sql, 1);
		$statement->execute([$this->categoryID, 0, 0]);
		
		$row = $statement->fetchArray();
		if (!empty($row['todoID'])) {
			$sql = "INSERT INTO	wcf".WCF_N."_todo_category_last_todo
						(categoryID, todoID)
				VALUES		(?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$this->categoryID, $row['todoID']]);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public static function resetCache() {
		static::resetDataCache();
		static::resetACLOptionCache();
		static::resetLabelGroupCache();
		UserStorageHandler::getInstance()->resetAll('todoListAccessable');
	}
	
	/**
	 * Resets the category acl option cache.
	 */
	public static function resetACLOptionCache() {
		TodoCategoryACLOptionCacheBuilder::getInstance()->reset();
	}
	
	/**
	 * Resets the category data cache.
	 */
	public static function resetDataCache() {
		TodoCategoryDataCacheBuilder::getInstance()->reset();
	}
	
	/**
	 * Resets the category label group cache.
	 */
	public static function resetLabelGroupCache() {
		TodoCategoryLabelGroupCacheBuilder::getInstance()->reset();
	}
}
