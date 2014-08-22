<?php
namespace wcf\data\todo;
use wcf\data\todo\ToDo;
use wcf\system\cache\builder\ToDoCacheBuilder;
use wcf\data\user\online\UsersOnlineList;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Represents the todo cache.
 *
 * @author Florian Gail
 * @copyright 2014 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
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
		$this->cachedTodos = ToDoCacheBuilder::getInstance()->getData( array(), 'todos');
	}
	
	/**
	 * Reads the users online.
	 */
	protected function initUsersOnline() {
		$this->usersOnline = array();
		
		$usersOnlineList = new UsersOnlineList();
		$usersOnlineList->getConditionBuilder()->add( '(session.objectType = ? OR session.parentObjectType = ?)', array(
				'de.mysterycode.wcf.toDo.toDo',
				'de.mysterycode.wcf.toDo.toDo' 
		));
		$usersOnlineList->getConditionBuilder()->add( 'session.userID IS NOT NULL');
		$usersOnlineList->readObjects();
		
		foreach($usersOnlineList as $user) {
			$todoID =($user->objectType == 'de.mysterycode.wcf.toDo.toDo' ? $user->objectID : $user->parentObjectID);
			if(!isset($this->usersOnline[$todoID]))
				$this->usersOnline[$todoID] = array();
			
			$this->usersOnline[$todoID][] = $user;
		}
	}
	
	/**
	 * Returns the todo with the given todo id from cache.
	 *
	 * @param integer $todoID        	
	 * @return \wcf\data\todo\ToDo
	 */
	public function getToDo($todoID) {
		if(!isset($this->cachedTodos[$todoID]))
			return null;
		
		return $this->cachedTodos[$todoID];
	}
	
	/**
	 * Returns a list of all todos.
	 *
	 * @return array<\wcf\data\todo\ToDo>
	 */
	public function getToDos() {
		return $this->cachedTodos;
	}
	
	/**
	 * Returns the users online list.
	 *
	 * @param integer $todoID        	
	 * @return array<\wcf\data\user\User>
	 */
	public function getUsersOnline($todoID) {
		if($this->usersOnline === null)
			$this->initUsersOnline();
		
		if(isset($this->usersOnline[$todoID]))
			return $this->usersOnline[$todoID];
		return array();
	}
}