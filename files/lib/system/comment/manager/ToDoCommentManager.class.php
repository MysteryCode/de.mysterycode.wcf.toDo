<?php

namespace wcf\system\comment\manager;
use wcf\data\todo\ToDoCache;
use wcf\data\todo\ToDoEditor;
use wcf\system\WCF;

/**
 * Shows the todo comment manager.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoCommentManager extends AbstractCommentManager {
	/**
	 * @inheritDoc
	 */
	protected $permissionAdd = 'user.toDo.comment.canAdd';
	
	/**
	 * @inheritDoc
	 */
	protected $permissionEdit = 'user.toDo.comment.canEditOwn';
	
	/**
	 * @inheritDoc
	 */
	protected $permissionDelete = 'user.toDo.comment.canDeleteOwn';
	
	/**
	 * @inheritDoc
	 */
	protected $permissionModEdit = 'mod.toDo.comment.canEdit';
	
	/**
	 * @inheritDoc
	 */
	protected $permissionModDelete = 'mod.toDo.comment.canDelete';
	
	/**
	 * @inheritDoc
	 */
	protected $permissionCanModerate = 'mod.toDo.comment.canModerate';
	
	/**
	 * @inheritDoc
	 */
	public function isAccessible($objectID, $validateWritePermission = false) {
		$todo = ToDoCache::getInstance()->getTodo($objectID);
		
		if ($todo === null || !$todo->todoID)
			return false;
		
		// check view permission
		if (!$todo->canEnter())
			return false;
		
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getLink($objectTypeID, $objectID) {
		$todo = ToDoCache::getInstance()->getTodo($objectID);
		return $todo === null ? '' : $todo->getLink();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTitle($objectTypeID, $objectID, $isResponse = false) {
		if ($isResponse) {
			return WCF::getLanguage()->get('wcf.toDo.comment.response');
		}
		
		return WCF::getLanguage()->getDynamicVariable('wcf.toDo.comment');
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateCounter($objectID, $value) {
		$todo = ToDoCache::getInstance()->getTodo($objectID);
		
		$todoEditor = new ToDoEditor($todo);
		$todoEditor->updateCounters(['comments' => $value]);
	}
}
