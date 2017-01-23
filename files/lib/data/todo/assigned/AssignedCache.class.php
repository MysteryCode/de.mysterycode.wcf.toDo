<?php

namespace wcf\data\todo\assigned;
use wcf\system\cache\builder\AssignCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * 
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class AssignedCache extends SingletonFactory {
	/**
	 * cached by users
	 */
	protected $users = [];
	
	/**
	 * cached by groups
	 */
	protected $groups = [];
	
	/**
	 * cached by todos
	 */
	protected $todos = [];
	
	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->users = AssignCacheBuilder::getInstance()->getData([], 'users');
		$this->groups = AssignCacheBuilder::getInstance()->getData([], 'groups');
		$this->todos = AssignCacheBuilder::getInstance()->getData([], 'todos');
	}
	
	public function getTodosByUser($userID) {
		if (!empty($this->users[$userID]))
			return $this->users[$userID];
		
		return [];
	}
	
	public function getTodoIDsByUser($userID) {
		$todoIDs = [];
		if (!empty($this->users[$userID])) {
			foreach ($this->users[$userID] as $todo) {
				$todoIDs[] = $todo->todoID;
			}
		}
		
		return $todoIDs;
	}
	
	public function getTodosByGroup($groupID) {
		if (!empty($this->groups[$groupID]))
			return $this->groups[$groupID];
		
		return [];
	}
	
	public function getTodoIDsByGroup($groupID) {
		$todoIDs = [];
		if (!empty($this->groups[$groupID])) {
			foreach ($this->groups[$groupID] as $todo) {
				$todoIDs[] = $todo->todoID;
			}
		}
		
		return $todoIDs;
	}
	
	public function getUsersByTodo($todoID) {
		if (!empty($this->todos[$todoID]['users']))
			return $this->todos[$todoID]['users'];
		
		return [];
	}
	
	public function getUserIDsByTodo($todoID) {
		$userIDs = [];
		if (!empty($this->todos[$todoID]['users'])) {
			foreach ($this->todos[$todoID]['users'] as $user) {
				$userIDs[] = $user->userID;
			}
		}
		
		return $userIDs;
	}
	
	public function getGroupsByTodo($todoID) {
		if (!empty($this->todos[$todoID]['groups']))
			return $this->todos[$todoID]['groups'];
		
		return [];
	}
	
	public function getGroupIDsByTodo($todoID) {
		$groupIDs = [];
		if (!empty($this->todos[$todoID]['groups'])) {
			foreach ($this->todos[$todoID]['groups'] as $group) {
				$groupIDs[] = $group->groupID;
			}
		}
		
		return $groupIDs;
	}
}
