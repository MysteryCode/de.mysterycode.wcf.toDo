<?php

namespace wcf\system\todo;
use wcf\data\todo\ToDo;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\SingletonFactory;
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
	protected $unsolvedTodoCount = array();
	
	/**
	 * number of overdue todos
	 * @var	array<integer>
	 */
	protected $overdueTodoCount = array();
	
	public function getUnsolovedTodoCount($userID = null) {
		if ($userID === null) $userID = WCF::getUser()->userID;
		
		if (!isset($this->unsolvedTodoCount[$userID])) {
			$this->unsolvedTodoCount[$userID] = 0;
			
			//UserStorageHandler::getInstance()->loadStorage(array($userID));
			
			//$data = UserStorageHandler::getInstance()->getStorage(array($userID), 'unsolvedTodoCount');
			
			//if ($data[$userID] === null) {
				$conditionBuilder = new PreparedStatementConditionBuilder();
				$conditionBuilder->add('todo.todoID = todo_to_user.todoID');
				$conditionBuilder->add('todo.status = ?', array(1));
				$conditionBuilder->add('todo.endTime < ?', array(TIME_NOW));
				$conditionBuilder->add('todo_to_user.userID = ?', array($userID));
				
				$sql = "SELECT	COUNT(*) AS count
					FROM	wcf".WCF_N."_todo_to_user todo_to_user,
						wcf".WCF_N."_todo todo
					".$conditionBuilder->__toString();
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute($conditionBuilder->getParameters());
				$row = $statement->fetchArray();
				$this->unsolvedTodoCount[$userID] = $row['count'];
				
				//UserStorageHandler::getInstance()->update($userID, 'unsolvedTodoCount', serialize($this->unsolvedTodoCount[$userID]));
			//}
			//else {
				//$this->unsolvedTodoCount[$userID] = unserialize($data[$userID]);
			//}
		}
		
		return $this->unsolvedTodoCount[$userID];
	}
	
	public function getOverdueTodoCount($userID = null) {
		if ($userID === null) $userID = WCF::getUser()->userID;
	
		if (!isset($this->overdueTodoCount[$userID])) {
			$this->overdueTodoCount[$userID] = 0;
				
			//UserStorageHandler::getInstance()->loadStorage(array($userID));
				
			//$data = UserStorageHandler::getInstance()->getStorage(array($userID), 'overdueTodoCount');
				
			//if ($data[$userID] === null) {
				$conditionBuilder = new PreparedStatementConditionBuilder();
				$conditionBuilder->add('todo.todoID = todo_to_user.todoID');
				$conditionBuilder->add('todo.status <> ?', array(3));
				$conditionBuilder->add('todo.status <> ?', array(4));
				$conditionBuilder->add('todo.endTime < ?', array(TIME_NOW));
				$conditionBuilder->add('todo.endTime <> ?', array(0));
				$conditionBuilder->add('todo_to_user.userID = ?', array($userID));
	
				$sql = "SELECT	COUNT(*) AS count
					FROM	wcf".WCF_N."_todo_to_user todo_to_user,
						wcf".WCF_N."_todo todo
					".$conditionBuilder->__toString();
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute($conditionBuilder->getParameters());
				$row = $statement->fetchArray();
				$this->overdueTodoCount[$userID] = $row['count'];
	
				//UserStorageHandler::getInstance()->update($userID, 'overdueTodoCount', serialize($this->overdueTodoCount[$userID]));
			//}
			//else {
				//$this->overdueTodoCount[$userID] = unserialize($data[$userID]);
			//}
		}
	
		return $this->overdueTodoCount[$userID];
	}
}
