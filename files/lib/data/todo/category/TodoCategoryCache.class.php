<?php

namespace wcf\data\todo\category;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\online\UsersOnlineList;
use wcf\system\cache\builder\TodoCategoryDataCacheBuilder;
use wcf\system\cache\builder\TodoCategoryLabelGroupCacheBuilder;
use wcf\system\category\CategoryHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\label\LabelHandler;
use wcf\system\label\object\TodoLabelObjectHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Manages the todo category cache.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryCache extends SingletonFactory {
	/**
	 * cached label groups
	 * @var	integer[][]
	 */
	protected $cachedLabelGroups = array();
	
	/**
	 * list of cached category objects
	 * @var	array<\wcf\data\todo\category\TodoCategory>
	 */
	protected $categories = null;
	
	/**
	 * object type object of todo category system
	 * @var	\wcf\data\object\type\ObjectType
	 */
	protected $objectType = null;
	
	/**
	 * list of cached stat objects
	 * @var	array<\wcf\data\todo\category\stat\TodoCategoryStat>
	 */
	protected $statObjects = array();
	
	/**
	 * list of users which are online in the categories
	 * @var	array<\wcf\data\user\User>
	 */
	protected $usersOnline = null;
	
	/**
	 * list of cached last todo ids
	 * @var	array<integer>
	 */
	protected $lastTodoIDs = array();
	
	/**
	 * list of cached last todos
	 * @var array<\wcf\data\todo\ToDo>
	 */
	protected $lastTodos = null;
	
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.category', 'de.mysterycode.wcf.toDo');
		$this->cachedLabelGroups = TodoCategoryLabelGroupCacheBuilder::getInstance()->getData(array(), 'labelGroups');
		
		$this->statObjects = TodoCategoryDataCacheBuilder::getInstance()->getData(array(), 'statObjects');
		$this->lastTodoIDs = TodoCategoryDataCacheBuilder::getInstance()->getData(array(), 'lastTodoIDs');
	}
	
	/**
	 * Returns a list of category objects.
	 *
	 * @return	array<\wcf\data\todo\category\TodoCategory>
	 */
	public function getCategories() {
		if ($this->categories === null) {
			$this->categories = array();
			
			$categories = CategoryHandler::getInstance()->getCategories('de.mysterycode.wcf.toDo');
			foreach ($categories as $categoryID => $category) {
				$this->categories[$categoryID] = new TodoCategory($category);
			}
		}
		
		return $this->categories;
	}
	
	/**
	 * Returns a list of category ids.
	 *
	 * @return	array<integer>
	 */
	public function getCategoryIDs() {
		$categoryIDs = array();
		
		$categories = $this->getCategories();
		foreach ($categories as $category) {
			$categoryIDs[] = $category->categoryID;
		}
		
		return $categoryIDs;
	}
	
	/**
	 * Returns the object of the category with the given id.
	 *
	 * @param	integer		$categoryID
	 * @return	\wcf\data\todo\category\TodoCategory
	 */
	public function getCategory($categoryID) {
		$categories = $this->getCategories();
		
		if (isset($categories[$categoryID])) {
			return $categories[$categoryID];
		}
		
		return null;
	}
	
	/**
	 * Returns a list of child objects of the category with the given id.
	 *
	 * @param	integer		$parentID
	 * @return	array<\wcf\data\todo\category\TodoCategory>
	 */
	public function getChildCategories($parentID = 0) {
		return CategoryHandler::getInstance()->getChildCategories($parentID, $this->objectType->objectTypeID);
	}
	
	/**
	 * Returns a list of child ids of the category with the given id.
	 *
	 * @return	array<integer>
	 */
	public function getChildCategoryIDs($parentID = 0) {
		$childCategories = $this->getChildCategories($parentID);
		return array_keys($childCategories);
	}
	
	/**
	 * Returns a list of users which are online in the category with the given id.
	 *
	 * @param	integer		$categoryID
	 * @return	array<\wcf\data\user\User>
	 */
	public function getUsersOnline($categoryID) {
		if ($this->usersOnline === null) {
			$this->initUsersOnline();
		}
		
		if (isset($this->usersOnline[$categoryID])) {
			return $this->usersOnline[$categoryID];
		}
		
		return array();
	}
	
	/**
	 * Reads the users which are online in the categories.
	 */
	protected function initUsersOnline() {
		$this->usersOnline = array();
		
		// init users online list
		$usersOnlineList = new UsersOnlineList();
		$usersOnlineList->getConditionBuilder()->add('(session.objectType = ? OR session.parentObjectType = ?)', array('de.mysterycode.wcf.toDo.category', 'de.mysterycode.wcf.toDo.category'));
		$usersOnlineList->getConditionBuilder()->add('session.userID IS NOT NULL');
		$usersOnlineList->readObjects();
		
		// map users to categories
		foreach ($usersOnlineList as $user) {
			$categoryID = ($user->objectType == 'de.mysterycode.wcf.toDo.category' ? $user->objectID : $user->parentObjectID);
			if (!isset($this->usersOnline[$categoryID])) {
				$this->usersOnline[$categoryID] = array();
			}
			
			$this->usersOnline[$categoryID][] = $user;
		}
	}
	
	/**
	 * Returns the stat object of the category with the given id.
	 *
	 * @param	integer		$categoryID
	 * @return	\wcf\data\todo\category\stat\TodoCategoryStat
	 */
	public function getStatObject($categoryID) {
		if (isset($this->statObjects[$categoryID])) {
			return $this->statObjects[$categoryID];
		}
		
		return null;
	}
	
	/**
	 * Returns a list of label groups for the category with the given id.
	 *
	 * @param	integer		$categoryID
	 * @return	\wcf\data\label\group\ViewableLabelGroup[]
	 */
	public function getLabelGroups($categoryID = null) {
		$groupIDs = $this->getLabelGroupIDs($categoryID);
		return LabelHandler::getInstance()->getLabelGroups($groupIDs);
	}
	
	/**
	 * @param integer $categoryID
	 * @return array|\integer[]|\integer[][]
	 */
	public function getLabelGroupIDs($categoryID = null) {
		if ($categoryID === null) {
			return $this->cachedLabelGroups;
		}
		
		if (isset($this->cachedLabelGroups[$categoryID])) {
			return $this->cachedLabelGroups[$categoryID];
		}
		
		return [];
	}
	
	/**
	 * Returns the last todo of the category with the given id.
	 *
	 * @param	integer		$categoryID
	 * @return	\wcf\data\todo\ToDo
	 */
	public function getLastTodo($categoryID) {
		// read last todos if necessary
		if ($this->lastTodos === null) {
			$this->initLastTodos();
		}
		
		// return last todo if available
		if (isset($this->lastTodos[$categoryID])) {
			return $this->lastTodos[$categoryID];
		}
		
		// nothing to get
		return null;
	}
	
	/**
	 * Loads the last todos.
	 */
	protected function initLastTodos() {
		$this->lastTodos = array();
		
		if (!empty($this->lastTodoIDs)) {
			// get labels
			$assignedLabels = TodoLabelObjectHandler::getInstance()->getAssignedLabels($this->lastTodoIDs);
			
			// prepare conditions
			$conditionBuilder = new PreparedStatementConditionBuilder();
			$conditionBuilder->add('todo.id IN (?)', array($this->lastTodoIDs));
			
			// read data
			$sql = "SELECT		todo.id, todo.categoryID,
						todo.title, todo.timestamp, todo.submitter, todo.username,
				FROM		wcf".WCF_N."_todo todo
				".$conditionBuilder."
				ORDER BY	todo.timestamp DESC";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($conditionBuilder->getParameters());
			while ($row = $statement->fetchArray()) {
				$todo = new Todo(null, $row);
				
				// add labels
				if (isset($assignedLabels[$todo->todoID])) {
					foreach ($assignedLabels[$todo->todoID] as $label) {
						$todo->addLabel($label);
					}
				}
				
				$this->lastTodos[$row['categoryID']] = $todo;
			}
		}
	}
}
