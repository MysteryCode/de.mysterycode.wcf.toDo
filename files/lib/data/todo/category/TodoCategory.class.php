<?php

namespace wcf\data\todo\category;
use wcf\data\category\AbstractDecoratedCategory;

use wcf\data\ITitledLinkObject;
use wcf\data\user\User;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\todo\category\TodoCategoryPermissionHandler;
use wcf\system\WCF;

/**
 * Represents a todo-category.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategory extends AbstractDecoratedCategory implements ITitledLinkObject, IRouteController {
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
	 * Returns true if the active user has the permission to add new todos to this category without moderation.
	 *
	 * @return	boolean
	 */
	public function canAddTodoWithoutModeration() {
		return $this->getPermission('user.canAddTodoWithoutModeration');
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->title);
	}

	/**
	 * Returns the stat object of this category.
	 *
	 * @return TodoCategoryStat
	 * @throws \wcf\system\exception\SystemException
	 */
	public function getStatObject() {
		return TodoCategoryCache::getInstance()->getStatObject($this->categoryID);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('TodoCategory', array(
			'object' => $this
		));
	}

	/**
	 * Returns the last todo of this category.
	 *
	 * @param    integer $languageID
	 * @return \wcf\data\todo\ToDo
	 * @throws \wcf\system\exception\SystemException
	 */
	public function getLastTodo($languageID = null) {
		return TodoCategoryCache::getInstance()->getLastTodo($this->categoryID);
	}

	/**
	 * Returns true if the active user has the given permission within this category.
	 * Uses the gloqqbal permissions if no category-specific permission is set.
	 *
	 * @param    string $permission
	 * @param User      $user
	 * @return bool
	 * @throws \wcf\system\exception\SystemException
	 */
	public function getPermission($permission = 'user.canViewCategory', User $user = null) {
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
		if ($permission == 'user.canEditStatus') {
			return WCF::getSession()->getPermission('mod.toDo.status.canEdit');
		} else if ($permission == 'user.canAddTodo') {
			return WCF::getSession()->getPermission('user.toDo.toDo.canAdd');
		} else if ($permission == 'user.canAddTodoWithoutModeration') {
			return WCF::getSession()->getPermission('user.toDo.toDo.canAddWithoutModeration');
		} else if ($permission == 'user.canEditResponsibles') {
			return $this->getPermission('user.canEditTodos');
		} else if ($permission == 'user.canViewResponsibles') {
			return WCF::getSession()->getPermission('user.toDo.responsible.canView');
		} else if ($permission == 'user.canParticipate') {
			return WCF::getSession()->getPermission('user.toDo.responsible.canParticipate');
		} else if ($permission == 'user.canViewCategory') {
			return WCF::getSession()->getPermission('user.toDo.toDo.canView');
		} else if ($permission == 'user.canEnterCategory') {
			return WCF::getSession()->getPermission('user.toDo.toDo.canView');
		} else if ($permission == 'user.canLikeTodo') {
			return WCF::getSession()->getPermission('user.like.canLike');
		} else if (in_array($permission, array('user.canViewDeadline', 'user.canEditDeadline', 'user.canViewReminder', 'user.canEditReminder', 'user.canEditPriority'))) {
			return true;
		} else {
			$globalPermission = str_replace(array('user.', 'mod.'), array('user.toDo.toDo.', 'mod.toDo.'), $permission);
			$globalPermission = str_replace('Todos', '', $globalPermission);
			return WCF::getSession()->getPermission($globalPermission);
		}
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
			'mod.canEditTodos',
			'mod.canDeleteTodos',
			'mod.canEnableTodos',
			'mod.canMoveTodos'
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
	 * @param    array $permissions
	 * @return array
	 * @throws \wcf\system\exception\SystemException
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
	 * @param    integer $parentID
	 * @param    array   $permissions
	 * @throws \wcf\system\exception\SystemException
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
	
	public function canViewTodos() {
		$this->getPermission('user.canViewTodos');
	}
	
	public function canEnterTodos() {
		return $this->getPermission('user.canEnterTodos');
	}
	
	public function canEditStatus() {
		return $this->getPermission('user.canEditStatus');
	}
	
	public function canEditResponsibles() {
		return $this->getPermission('user.canEditStatus');
	}
	
	public function canViewResponsibleUsers() {
		return $this->getPermission('user.canViewResponsibles');
	}
	
	public function canViewResponsibleGroups() {
		return $this->getPermission('user.canViewResponsibles');
	}
	
	public function canViewReminder() {
		return $this->getPermission('user.canViewReminder');
	}
	
	public function canEditReminder() {
		return $this->getPermission('user.canEditReminder');
	}
	
	public function canViewDeadline() {
		return $this->getPermission('user.canViewDeadline');
	}
	
	public function canEditDeadline() {
		return $this->getPermission('user.canEditDeadline');
	}
	
	public function canEditPriority() {
		return $this->getPermission('user.canEditPriority');
	}
}
