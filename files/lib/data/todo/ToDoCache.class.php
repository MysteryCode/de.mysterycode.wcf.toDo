<?php

namespace wcf\data\todo;

use wcf\system\cache\builder\TodoCacheBuilder;
use wcf\data\user\online\UsersOnlineList;
use wcf\system\SingletonFactory;

/**
 * 
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoCache extends SingletonFactory {
	/**
	 * cached todos
	 *
	 * @var array<\wcf\data\todo\ToDo>
	 */
	protected $cachedTodos = [];
	
	/**
	 * users online
	 *
	 * @var array<array>
	 */
	protected $usersOnline = null;

	/**
	 *
	 * @inheritDoc
	 */
	protected function init() {
		$this->cachedTodos = TodoCacheBuilder::getInstance()->getData();
	}

	/**
	 * Reads the users online.
	 */
	protected function initUsersOnline() {
		$this->usersOnline = [];
		
		$usersOnlineList = new UsersOnlineList();
		$usersOnlineList->getConditionBuilder()->add('(session.objectType = ? OR session.parentObjectType = ?)', [
			'de.mysterycode.wcf.toDo',
			'de.mysterycode.wcf.toDo.category'
		]);
		$usersOnlineList->getConditionBuilder()->add('session.userID IS NOT NULL');
		$usersOnlineList->readObjects();

		foreach ($usersOnlineList as $user) {
			$todoID = ($user->objectType == 'de.mysterycode.wcf.toDo' ? $user->objectID : $user->parentObjectID);
			if (! isset($this->usersOnline[$todoID]))
				$this->usersOnline[$todoID] = [];
			
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
		if (! isset($this->cachedTodos['todos'][$todoID]))
			return null;
		
		return $this->cachedTodos['todos'][$todoID];
	}

	/**
	 * Returns a list of all todos.
	 *
	 * @return array<\wcf\data\todo\ToDo>
	 */
	public function getTodos() {
		return $this->cachedTodos['todos'];
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
		return [];
	}
}
