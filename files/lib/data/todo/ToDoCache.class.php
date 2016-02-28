<?php

namespace wcf\data\todo;
use wcf\data\todo\ToDo;
use wcf\system\cache\builder\TodoCacheBuilder;
use wcf\data\user\online\UsersOnlineList;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * 
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	exclusive
 * @package	
 * @category	WCF
 */
class ToDoCache extends SingletonFactory {
	/**
	 * cached todos
	 *
	 * @var array<\wcf\data\todo\ToDo>
	 */
	protected $cachedTodos = array();
	
	/**
	 * users online
	 *
	 * @var array<array>
	 */
	protected $usersOnline = null;

	/**
	 *
	 * @see \wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->cachedTodos = TodoCacheBuilder::getInstance()->getData();
	}

	/**
	 * Reads the users online.
	 */
	protected function initUsersOnline() {
		$this->usersOnline = array();
		
		$usersOnlineList = new UsersOnlineList();
		$usersOnlineList->getConditionBuilder()->add('(session.objectType = ? OR session.parentObjectType = ?)', array(
			'de.mysterycode.wcf.toDo',
			'de.mysterycode.wcf.toDo.category'
		));
		$usersOnlineList->getConditionBuilder()->add('session.userID IS NOT NULL');
		$usersOnlineList->readObjects();
		
		foreach ($usersOnlineList as $user) {
			$todoID = ($user->objectType == 'de.mysterycode.wcf.toDo' ? $user->objectID : $user->parentObjectID);
			if (! isset($this->usersOnline[$todoID]))
				$this->usersOnline[$todoID] = array();
			
			$this->usersOnline[$todoID][] = $user;
		}
	}

	/**
	 * Returns the todo with the given id from cache.
	 *
	 * @param integer $todoID        	
	 * @return \wcf\data\todo\ToDo
	 */
	public function getTodo($todoID) {
		if (! isset($this->cachedTodos[$todoID]))
			return null;
		
		return $this->cachedTodos[$todoID];
	}

	/**
	 * Returns a list of all todos.
	 *
	 * @return array<\wcf\data\todo\ToDo>
	 */
	public function getTodoss() {
		return $this->cachedTodos;
	}

	/**
	 * Returns the users online list.
	 *
	 * @param integer $todoID        	
	 * @return array<\wcf\data\user\User>
	 */
	public function getUsersOnline($todoID) {
		if ($this->usersOnline === null)
			$this->initUsersOnline();
		
		if (isset($this->usersOnline[$todoID]))
			return $this->usersOnline[$todoID];
		return array();
	}
}
