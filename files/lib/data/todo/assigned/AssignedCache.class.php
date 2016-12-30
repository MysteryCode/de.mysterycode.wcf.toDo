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
	protected $users = array();
	
	/**
	 * cached by groups
	 */
	protected $groups = array();
	
	/**
	 * cached by todos
	 */
	protected $todos = array();
	
	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->users = AssignCacheBuilder::getInstance()->getData(array(), 'users');
		$this->groups = AssignCacheBuilder::getInstance()->getData(array(), 'groups');
		$this->todos = AssignCacheBuilder::getInstance()->getData(array(), 'todos');
	}
	
	public function getTodosByUser($userID) {
		if (isset($this->users[$userID]) && !empty($this->users[$userID]))
			return $this->users[$userID];
		
		return array();
	}
	
	public function getTodoIDsByUser($userID) {
		$todoIDs = array();
		if (isset($this->users[$userID]) && !empty($this->users[$userID])) {
			foreach ($this->users[$userID] as $todo) {
				$todoIDs[] = $todo->todoID;
			}
		}
		
		return $todoIDs;
	}
	
	public function getTodosByGroup($groupID) {
		if (isset($this->groups[$groupID]) && !empty($this->groups[$groupID]))
			return $this->groups[$groupID];
		
		return array();
	}
	
	public function getTodoIDsByGroup($groupID) {
		$todoIDs = array();
		if (isset($this->groups[$groupID]) && !empty($this->groups[$groupID])) {
			foreach ($this->groups[$groupID] as $todo) {
				$todoIDs[] = $todo->todoID;
			}
		}
		
		return $todoIDs;
	}
	
	public function getUsersByTodo($todoID) {
		if (isset($this->todos[$todoID]['users']) && !empty($this->todos[$todoID]['users']))
			return $this->todos[$todoID]['users'];
		
		return array();
	}
	
	public function getUserIDsByTodo($todoID) {
		$userIDs = array();
		if (isset($this->todos[$todoID]['users']) && !empty($this->todos[$todoID]['users'])) {
			foreach ($this->todos[$todoID]['users'] as $user) {
				$userIDs[] = $user->userID;
			}
		}
		
		return $userIDs;
	}
	
	public function getGroupsByTodo($todoID) {
		if (isset($this->todos[$todoID]['groups']) && !empty($this->todos[$todoID]['groups']))
			return $this->todos[$todoID]['groups'];
		
		return array();
	}
	
	public function getGroupIDsByTodo($todoID) {
		$groupIDs = array();
		if (isset($this->todos[$todoID]['groups']) && !empty($this->todos[$todoID]['groups'])) {
			foreach ($this->todos[$todoID]['groups'] as $group) {
				$groupIDs[] = $group->groupID;
			}
		}
		
		return $groupIDs;
	}
}
