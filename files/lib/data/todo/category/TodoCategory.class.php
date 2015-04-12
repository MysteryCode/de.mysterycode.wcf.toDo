<?php

namespace wcf\data\todo\category;
use wcf\data\category\AbstractDecoratedCategory;
use wcf\data\ILinkableObject;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\category\TodoCategoryPermissionHandler;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\style\StyleHandler;
use wcf\system\WCF;

/**
 * Represents a todo-category.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */
class TodoCategory extends AbstractDecoratedCategory implements IBreadcrumbProvider, IRouteController, ILinkableObject {
	/**
	 * acl permissions for the active user of this category
	 * @var	array<boolean>
	 */
	protected $permissions = null;
	
	/**
	 * Returns true if the active user has the permission to enter this category.
	 *
	 * @return	boolean
	 */
	public function canEnter() {
		return ($this->getPermission() && $this->getPermission('user.canEnterCategory'));
	}
	
	/**
	 * Returns true if the active user has the permission to add new todos to this category.
	 *
	 * @return	boolean
	 */
	public function canAddTodo() {
		return $this->getPermission('user.canAddTodo');
	}
	
	/**
	 * @see	\wcf\system\breadcrumb\IBreadcrumbProvider::getBreadcrumb()
	 */
	public function getBreadcrumb() {
		return new Breadcrumb($this->getTitle(), $this->getLink());
	}
	
	/**
	 * @see	\wcf\system\request\IRouteController::getTitle()
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->title);
	}
	
	/**
	 * Returns the stat object of this category.
	 *
	 * @return	\wcf\data\todo\category\stat\CategoryStat
	 */
	public function getStatObject() {
		return TodoCategoryCache::getInstance()->getStatObject($this->categoryID);
	}
	
	/**
	 * @see wcf\data\ILinkableObject::getLink()
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('Category', array(
			'object' => $this
		));
	}
	
	/**
	 * Returns the last todo of this category.
	 *
	 * @param	integer		$languageID
	 * @return	\wcf\data\todo\ToDo
	 */
	public function getLastTodo($languageID = null) {
		return TodoCategoryCache::getInstance()->getLastTodo($this->categoryID);
	}
	
	/**
	 * Returns true if the active user has the given permission within this category.
	 * Uses the global permissions if no category-specific permission is set.
	 *
	 * @param	string		$permission
	 * @return	boolean
	 */
	public function getPermission($permission = 'user.canViewCategory') {
		// load cache if necessary
		if ($this->permissions === null) {
			$this->permissions = TodoCategoryPermissionHandler::getInstance()->getPermissions($this->getDecoratedObject());
		}
		
		// try category specific permission
		if (isset($this->permissions[$permission])) {
			//return $this->permissions[$permission];
			// todo: remove intval() if WL fixes this issue
			return intval($this->permissions[$permission]);
		}
		
		// use global permission
		$globalPermission = str_replace(array('user.', 'mod.'), array('user.wcf.todo.', 'mod.wcf.todo.'), $permission);
		return WCF::getSession()->getPermission($globalPermission);
	}
	
	/**
	 * Returns true if the active user has the given permissions for this category.
	 *
	 * @param	array		$permissions
	 * @return	boolean
	 */
	public function getPermissions(array $permissions = array('user.canViewCategory', 'user.canEnterCategory')) {
		$result = true;
		
		foreach ($permissions as $permission) {
			$result = $result && $this->getPermission($permission);
		}
		
		return $result;
	}
	
	/**
	 * Returns true if the active user has the given permissions for this category.
	 *
	 * @param	array		$permissions
	 * @return	boolean
	 * @deprecated	use \wcf\data\todo\category\TodoCategory::getPermissions($permissions) instead
	 */
	public function checkPermission(array $permissions = array('user.canViewCategory', 'user.canEnterCategory')) {
		return $this->getPermissions($permissions);
	}
	
	/**
	 * Returns true if the active user is a moderator of this category.
	 *
	 * @return	boolean
	 */
	public function isModerator() {
		$validPermissions = array(
			'mod.canEditTodo',
			'mod.canDeleteTodo',
			'mod.canEnableTodo',
			'mod.canMoveTodo'
		);
		
		foreach ($validPermissions as $permission) {
			if ($this->getPermission($permission)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Returns true if the active user has the permission to edit todos.
	 *
	 * @return	boolean
	 */
	public function canEditTodos() {
		return $this->getPermission('mod.canEditTodo');
	}
	
	/**
	 * Returns a list of of accessible categories for the active user.
	 *
	 * @param	array		$permissions
	 * @return	array
	 */
	public static function getAccessibleCategoryIDs(array $permissions = array('user.canViewCategory', 'user.canEnterCategory')) {
		$categoryIDs = array();
		
		// loop through all categories
		foreach (TodoCategoryCache::getInstance()->getCategories() as $category) {
			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $category->getPermission($permission);
			}
			
			// add category if accessible
			if ($result) {
				$categoryIDs[] = $category->categoryID;
			}
		}
		
		return $categoryIDs;
	}
	
	/**
	 * Inherits the category permissions.
	 *
	 * @param	integer		$parentID
	 * @param	array		$permissions
	 */
	public static function inheritPermissions($parentID = 0, &$permissions) {
		foreach (TodoCategoryCache::getInstance()->getChildCategories($parentID) as $categoryID => $category) {
			// inherit permissions from parent category
			if ($category->parentCategoryID) {
				if (isset($permissions[$category->parentCategoryID])) {
					foreach ($permissions[$category->parentCategoryID] as $type => $optionData) {
						foreach ($optionData as $typeID => $optionValues) {
							foreach ($optionValues as $permissionName => $permissionValue) {
								if (!isset($permissions[$categoryID][$type][$typeID][$permissionName])) {
									$permissions[$categoryID][$type][$typeID][$permissionName] = $permissionValue;
								}
							}
						}
					}
				}
			}
			
			self::inheritPermissions($categoryID, $permissions);
		}
	}
}