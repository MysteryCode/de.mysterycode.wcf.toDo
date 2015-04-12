<?php

namespace wcf\system\moderation\queue;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoAction;
use wcf\data\todo\ToDoCache;
use wcf\data\todo\ToDoList;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\moderation\queue\AbstractModerationQueueHandler;
use wcf\system\moderation\queue\ModerationQueueManager;
use wcf\system\WCF;

/**
 * Provides functions for the moderation of todos
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
abstract class AbstractToDoModerationQueueHandler extends AbstractModerationQueueHandler {
	/**
	 * @see	\wcf\system\moderation\queue\AbstractModerationQueueHandler::$className
	 */
	protected $className = 'wcf\data\todo\ToDo';
	
	/**
	 * list of todo objects
	 * @var	array<\wcf\data\todo\ToDo>
	 */
	protected static $todos = array();

	/**
	 * @see	\wcf\system\moderation\queue\IModerationQueueHandler::assignQueues()
	 */
	public function assignQueues(array $queues) {
		$todoIDs = array();
		foreach ($queues as $queue) {
			$todoIDs[] = $queue->objectID;
		}
		
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('id IN (?)', array(
			$todoIDs
		));
		
		$sql = "SELECT	id
			FROM	wcf" . WCF_N . "_todo
			" . $conditionBuilder;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		
		$todos = array();
		while ($row = $statement->fetchArray()) {
			$todos[$row['id']] = new ToDo($row['id']);
		}
		
		$orphanedQueueIDs = $assignments = array();
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
	 * @see	\wcf\system\moderation\queue\IModerationQueueHandler::getContainerID()
	 */
	public function getContainerID($objectID) {
		$sql = "SELECT	category
			FROM	wcf" . WCF_N . "_todo
			WHERE	id = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->getTodo($objectID)->id
		));
		$row = $statement->fetchArray();
		
		return ($row['category'] ?  : 0);
	}

	/**
	 * @see	\wcf\system\moderation\queue\IModerationQueueHandler::isValid()
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
			if (!self::$todos[$objectID]->id) {
				self::$todos[$objectID] = null;
			}
		}
		
		return self::$todos[$objectID];
	}

	/**
	 * @see	\wcf\system\moderation\queue\IModerationQueueHandler::populate()
	 */
	public function populate(array $queues) {
		$todos = $objectIDs = array();
		foreach ($queues as $object) {
			$objectIDs[] = $object->objectID;
		}
		
		$todoList = new ToDoList();
		$todoList->sqlSelects .= ", user_avatar.*, user_table.*";
		$todoList->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_user user_table ON (user_table.userID = todo_table.submitter)";
		$todoList->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_user_avatar user_avatar ON (user_avatar.avatarID = user_table.avatarID)";
		$todoList->getConditionBuilder()->add("todo_table.id IN (?)", array(
			$objectIDs
		));
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
	 * @see	\wcf\system\moderation\queue\IModerationQueueHandler::removeContent()
	 */
	public function removeContent(ModerationQueue $queue, $message) {
<<<<<<< HEAD
		if ($this->isValid($queue->objectID) && !$this->getTodo($queue->objectID)->isDeleted) {
			$todoAction = new ToDoAction(array($this->getTodo($queue->objectID)), 'trash', array('reason' => $message));
=======
		if ($this->isValid($queue->objectID) && ! $this->getTodo($queue->objectID)->isDeleted) {
			$todoAction = new ToDoAction(array(
				$this->getTodo($queue->objectID)
			), 'trash', array(
				'reason' => $message
			));
>>>>>>> master
			$todoAction->executeAction();
		}
	}
}
