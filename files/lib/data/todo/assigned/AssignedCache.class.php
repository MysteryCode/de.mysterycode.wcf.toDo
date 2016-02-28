<?php

namespace wcf\data\todo\assigned;
use wcf\system\cache\builder\AssignCacheBuilder;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * 
 * 
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenpflichtige Produkte <http://downloads.mysterycode.de/index.php/License/4-Kostenpflichtige-Produkte/>
 * @contact	de.mysterycode.inventar
 * @category 	MCPS
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
	 * @see	\wcf\system\SingletonFactory::init()
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
	
	public function getTodosByGroup($groupID) {
		if (isset($this->groups[$groupID]) && !empty($this->groups[$groupID]))
			return $this->groups[$groupID];
		
		return array();
	}
	
	public function getUsersByTodo($todoID) {
		if (isset($this->todos[$todoID]['users']) && !empty($this->todos[$todoID]['users']))
			return $this->todos[$todoID]['users'];
		
		return array();
	}
	
	public function getGroupsByTodo($todoID) {
		if (isset($this->todos[$todoID]['groups']) && !empty($this->todos[$todoID]['groups']))
			return $this->todos[$todoID]['groups'];
		
		return array();
	}
}
