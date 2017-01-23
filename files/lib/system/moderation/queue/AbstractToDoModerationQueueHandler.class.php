<?php

namespace wcf\system\moderation\queue;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoAction;
use wcf\data\todo\ToDoList;

/**
 * Provides functions for the moderation of todos
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
abstract class AbstractToDoModerationQueueHandler extends AbstractModerationQueueHandler {
	/**
	 * @inheritDoc
	 */
	protected $className = 'wcf\data\todo\ToDo';
	
	/**
	 * list of todo objects
	 * @var	array<\wcf\data\todo\ToDo>
	 */
	protected static $todos = [];

	/**
	 * @inheritDoc
	 */
	public function assignQueues(array $queues) {
		$todoIDs = [];
		foreach ($queues as $queue) {
			$todoIDs[] = $queue->objectID;
		}
		
		$todoList = new ToDoList();
		$todoList->getConditionBuilder()->add('todo_table.todoID IN (?)', [$todoIDs]);
		$todoList->readObjects();
		$todoList = $todoList->getObjects();
		
		$todos = [];
		foreach ($todoList as $todo) {
			$todos[$todo->todoID] = $todo;
		}
		
		$orphanedQueueIDs = $assignments = [];
		foreach ($queues as $queue) {
			$assignUser = false;
			
			if (!isset($todos[$queue->objectID])) {
				$orphanedQueueIDs[] = $queue->queueID;
				continue;
			}
			
			$todo = $todos[$queue->objectID];
			
			if ($todo->canModerate())
				$assignUser = true;
			
			$assignments[$queue->queueID] = $assignUser;
		}
		
		ModerationQueueManager::getInstance()->removeOrphans($orphanedQueueIDs);
		ModerationQueueManager::getInstance()->setAssignment($assignments);
	}

	/**
	 * @inheritDoc
	 */
	public function getContainerID($objectID) {
		return $this->getTodo($objectID)->getCategory()->categoryID;
	}

	/**
	 * @inheritDoc
	 */
	public function isValid($objectID) {
		if ($this->getTodo($objectID) === null) {
			return false;
		}
		
		return true;
	}

	/**
	 * Returns a todo object by id or null if id is invalid.
	 * 
	 * @param	integer		$objectID
	 * @return	\wcf\data\todo\ToDo
	 */
	protected function getTodo($objectID) {
		if (!array_key_exists($objectID, self::$todos)) {
			self::$todos[$objectID] = new ToDo($objectID);
			if (!self::$todos[$objectID]->todoID) {
				self::$todos[$objectID] = null;
			}
		}
		
		return self::$todos[$objectID];
	}

	/**
	 * @inheritDoc
	 */
	public function populate(array $queues) {
		$todos = $objectIDs = [];
		foreach ($queues as $object) {
			$objectIDs[] = $object->objectID;
		}
		
		$todoList = new ToDoList();
		$todoList->sqlSelects .= "user_avatar.*, user_table.*";
		$todoList->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_user user_table ON (user_table.userID = todo_table.submitter)";
		$todoList->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_user_avatar user_avatar ON (user_avatar.avatarID = user_table.avatarID)";
		$todoList->getConditionBuilder()->add("todo_table.todoID IN (?)", [
			$objectIDs
		]);
		$todoList->readObjects();
		
		$todos = $todoList->getObjects();
		
		foreach ($queues as $object) {
			if (isset($todos[$object->objectID])) {
				$object->setAffectedObject($todos[$object->objectID]);
			} else {
				$object->setIsOrphaned();
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function removeContent(ModerationQueue $queue, $message) {
		if ($this->isValid($queue->objectID) && ! $this->getTodo($queue->objectID)->isDeleted) {
			$todoAction = new ToDoAction([
				$this->getTodo($queue->objectID)
			], 'trash', [
				'reason' => $message
			]);
			$todoAction->executeAction();
		}
	}
}
