<?php

namespace wcf\system\todo;
use wcf\data\todo\ToDo;
use wcf\system\cache\builder\TodoGeneralStatsCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Handles todo data.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoHandler extends SingletonFactory {
	/**
	 * number of unsolved todos
	 * @var	array<integer>
	 */
	protected $unsolvedTodoCount = 0;

	/**
	 * number of overdue todos
	 * @var	array<integer>
	 */
	protected $overdueTodoCount = 0;
	/**
	 * number of waiting todos
	 * @var	array<integer>
	 */
	protected $waitingTodoCount = 0;
	
	protected $stats = array();
	
	public function getUnsolvedTodoCount($userID = null) {
		if ($userID === null) $userID = WCF::getUser()->userID;
		
		if ($userID) {
			// fetch user storage
			UserStorageHandler::getInstance()->loadStorage(array(WCF::getUser()->userID));
			
			// get package ids from user storage
			$data = UserStorageHandler::getInstance()->getStorage(array(WCF::getUser()->userID), 'unsolvedTodoCount');
			
			if ($data[WCF::getUser()->userID] === null) {
				$todoIDs = ToDo::getAccessibleTodoIDs();
				
				if (!empty($todoIDs)) {
					$conditionBuilder = new PreparedStatementConditionBuilder();
					$conditionBuilder->add('todo.todoID = todo_to_user.todoID');
					$conditionBuilder->add('todo_to_user.userID = ?', array($userID));
					$conditionBuilder->add('todo.todoID IN (?)', array($todoIDs));
					
					$conditionBuilder->add('todo.statusID = ?', array(2));
					$conditionBuilder->add('todo.endTime < ?', array(TIME_NOW));
					
					$sql = "SELECT	COUNT(*) AS count
							FROM	wcf".WCF_N."_todo_to_user todo_to_user,
								wcf".WCF_N."_todo todo
							".$conditionBuilder;
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute($conditionBuilder->getParameters());
					$row = $statement->fetchArray();
					$this->unsolvedTodoCount = $row['count'];
				}
					
				// update user storage
				UserStorageHandler::getInstance()->update(WCF::getUser()->userID, 'unsolvedTodoCount', $this->unsolvedTodoCount);
			} else {
				// read notifications from cache
				$this->unsolvedTodoCount = $data[WCF::getUser()->userID];
			}
		}
		
		return $this->unsolvedTodoCount;
	}
	
	public function getOverdueTodoCount($userID = null) {
		if ($userID === null) $userID = WCF::getUser()->userID;
		
		if ($userID) {
			// fetch user storage
			UserStorageHandler::getInstance()->loadStorage(array(WCF::getUser()->userID));
				
			// get package ids from user storage
			$data = UserStorageHandler::getInstance()->getStorage(array(WCF::getUser()->userID), 'overdueTodoCount');
				
			if ($data[WCF::getUser()->userID] === null) {
				$todoIDs = ToDo::getAccessibleTodoIDs();
		
				if (!empty($todoIDs)) {
					$conditionBuilder = new PreparedStatementConditionBuilder();
					$conditionBuilder->add('todo.todoID = todo_to_user.todoID');
					$conditionBuilder->add('todo_to_user.userID = ?', array($userID));
					$conditionBuilder->add('todo.todoID IN (?)', array($todoIDs));
					
					$conditionBuilder->add('todo.statusID <> ?', array(1));
					$conditionBuilder->add('todo.statusID <> ?', array(4));
					$conditionBuilder->add('todo.endTime < ?', array(TIME_NOW));
					$conditionBuilder->add('todo.endTime <> ?', array(0));
						
					$sql = "SELECT	COUNT(*) AS count
							FROM	wcf".WCF_N."_todo_to_user todo_to_user,
								wcf".WCF_N."_todo todo
							".$conditionBuilder;
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute($conditionBuilder->getParameters());
					$row = $statement->fetchArray();
					$this->overdueTodoCount = $row['count'];
				}
					
				// update user storage
				UserStorageHandler::getInstance()->update(WCF::getUser()->userID, 'overdueTodoCount', $this->overdueTodoCount);
			} else {
				// read notifications from cache
				$this->overdueTodoCount = $data[WCF::getUser()->userID];
			}
		}
		
	
		return $this->overdueTodoCount;
	}
	
	public function getWaitingTodoCount($userID = null) {
		if ($userID === null) $userID = WCF::getUser()->userID;
		
		if ($userID) {
			// fetch user storage
			UserStorageHandler::getInstance()->loadStorage(array(WCF::getUser()->userID));
		
			// get package ids from user storage
			$data = UserStorageHandler::getInstance()->getStorage(array(WCF::getUser()->userID), 'waitingTodoCount');
		
			if ($data[WCF::getUser()->userID] === null) {
				$todoIDs = ToDo::getAccessibleTodoIDs();
		
				if (!empty($todoIDs)) {
					$conditionBuilder = new PreparedStatementConditionBuilder();
					$conditionBuilder->add('todo.todoID = todo_to_user.todoID');
					$conditionBuilder->add('todo_to_user.userID = ?', array($userID));
					$conditionBuilder->add('todo.todoID IN (?)', array($todoIDs));
					
					$conditionBuilder->add('todo.statusID = ?', array(5));
					$conditionBuilder->add('todo.endTime < ?', array(TIME_NOW));
					$conditionBuilder->add('todo.endTime <> ?', array(0));
		
					$sql = "SELECT	COUNT(*) AS count
							FROM	wcf".WCF_N."_todo_to_user todo_to_user,
								wcf".WCF_N."_todo todo
							".$conditionBuilder;
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute($conditionBuilder->getParameters());
					$row = $statement->fetchArray();
					$this->waitingTodoCount = $row['count'];
				}
					
				// update user storage
				UserStorageHandler::getInstance()->update(WCF::getUser()->userID, 'waitingTodoCount', $this->waitingTodoCount);
			} else {
				// read notifications from cache
				$this->waitingTodoCount = $data[WCF::getUser()->userID];
			}
		}
	
		return $this->waitingTodoCount;
	}
	
	public function setStat($stats = array()) {
		$this->stats = array_merge($this->stats, $stats);
	}
	
	public function readTodoStats() {
		$this->stats = TodoGeneralStatsCacheBuilder::getInstance()->getData();
		
		EventHandler::getInstance()->fireAction($this, 'readTodoStats');
	}
	
	public function getStats($identifier = '') {
		if (empty($this->stats))
			$this->readTodoStats();
		
		if (empty($this->stats[$identifier]))
			return 0;
		else
			return $this->stats[$identifier];
	}
}
