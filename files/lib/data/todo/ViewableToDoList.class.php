<?php

namespace wcf\data\todo;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\label\object\TodoLabelObjectHandler;
use wcf\system\like\LikeHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * Represents a list of todos.
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ViewableToDoList extends ToDoList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$sqlOrderBy
	 */
	public $sqlOrderBy = 'todo_table.time DESC';
	
	/**
	 * @see	\wcf\data\DatabaseObjectList::$decoratorClassName
	 */
	public $decoratorClassName = 'wcf\data\todo\ViewableToDo';
	
	/**
	 * Creates a new ViewableToDoList object.
	 */
	public function __construct() {
		parent::__construct();
		
		if (WCF::getUser()->userID != 0) {
			// last visit time
			if (!empty($this->sqlSelects))
				$this->sqlSelects .= ',';
			$this->sqlSelects .= 'tracked_visit.visitTime';
			$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_tracked_visit tracked_visit ON (tracked_visit.objectTypeID = ".VisitTracker::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo')." AND tracked_visit.objectID = todo_table.todoID AND tracked_visit.userID = ".WCF::getUser()->userID.")";
			
			if (!empty($this->sqlSelects))
				$this->sqlSelects .= ',';
			$this->sqlSelects .= 'tracked_category_visit.visitTime AS categoryVisitTime';
			$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_tracked_visit tracked_category_visit ON (tracked_category_visit.objectTypeID = ".VisitTracker::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.category')." AND tracked_category_visit.objectID = todo_table.categoryID AND tracked_category_visit.userID = ".WCF::getUser()->userID.")";
			
// 			// subscriptions
// 			if (!empty($this->sqlSelects))
// 				$this->sqlSelects .= ',';
// 			$this->sqlSelects .= 'user_object_watch.watchID, user_object_watch.notification';
// 			$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_user_object_watch user_object_watch ON (user_object_watch.objectTypeID = ".ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.user.objectWatch', 'de.mysterycode.wcf.toDo')->objectTypeID." AND user_object_watch.userID = ".WCF::getUser()->userID." AND user_object_watch.objectID = todo_table.todoID)";
		}
		
		// get avatars
		if (!empty($this->sqlSelects))
			$this->sqlSelects .= ',';
		$this->sqlSelects .= "user_avatar.*, user_table.email, user_table.disableAvatar, user_table.enableGravatar, user_table.gravatarFileExtension";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_user user_table ON (user_table.userID = todo_table.submitter)";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_user_avatar user_avatar ON (user_avatar.avatarID = user_table.avatarID)";
		
		// get like status
		if (!empty($this->sqlSelects))
			$this->sqlSelects .= ',';
		$this->sqlSelects .= "like_object.likes, like_object.dislikes";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_like_object like_object ON (like_object.objectTypeID = ".LikeHandler::getInstance()->getObjectType('de.mysterycode.wcf.toDo.toDo')->objectTypeID." AND like_object.objectID = todo_table.todoID)";
		
		// get report status
		if (WCF::getSession()->getPermission('mod.general.canUseModeration')) {
			if (!empty($this->sqlSelects))
				$this->sqlSelects .= ',';
			$this->sqlSelects .= "moderation_queue.queueID AS reportQueueID";
			$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_moderation_queue moderation_queue ON (moderation_queue.objectTypeID = ".ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.moderation.report', 'de.mysterycode.wcf.toDo.toDo')." AND moderation_queue.objectID = todo_table.todoID AND moderation_queue.status <> ".ModerationQueue::STATUS_DONE." AND moderation_queue.status <> ".ModerationQueue::STATUS_REJECTED." AND moderation_queue.status <> ".ModerationQueue::STATUS_CONFIRMED.")";
		}
	}
	
	/**
	 * @see	\wcf\data\DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		if ($this->objectIDs === null)
			$this->readObjectIDs();
		
		parent::readObjects();
		
		// get assigned labels
		$todoIDs = array();
		foreach ($this->objects as $todo) {
			if ($todo->hasLabels) {
				$todoIDs[] = $todo->todoID;
			}
		}
		
		if (!empty($todoIDs)) {
			$assignedLabels = TodoLabelObjectHandler::getInstance()->getAssignedLabels($todoIDs);
			foreach ($assignedLabels as $todoID => $labels) {
				foreach ($labels as $label) {
					$this->objects[$todoID]->addLabel($label);
				}
			}
		}
	}
}
