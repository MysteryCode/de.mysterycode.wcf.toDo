<?php

namespace wcf\system\user\online\location;
use wcf\data\todo\ToDoList;
use wcf\data\user\online\UserOnline;
use wcf\system\user\online\location\IUserOnlineLocation;
use wcf\system\WCF;

/**
 * Provides the todo user online location
 *
 * @author Florian Gail
 * @copyright 2014 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoLocation implements IUserOnlineLocation {
	/**
	 * todo ids
	 *
	 * @var array<integer>
	 */
	protected $todoIDs = array();
	protected $todos = array();
	
	/**
	 *
	 * @see \wcf\system\user\online\location\IUserOnlineLocation::cache()
	 */
	public function cache(UserOnline $user) {
		if($user->objectID) {
			$this->todoIDs[] = $user->objectID;
		}
	}
	
	/**
	 *
	 * @see \wcf\system\user\online\location\IUserOnlineLocation::get()
	 */
	public function get(UserOnline $user, $languageVariable = '') {
		if($this->todos === null) {
			$this->readToDos();
		}
		
		if(!isset($this->todos[$user->objectID])) {
			return '';
		}
		
		return WCF::getLanguage()->getDynamicVariable($languageVariable, array('todo' => $this->todos[$user->objectID]));
	}
	
	/**
	 * Loads the toDos.
	 */
	protected function readToDos() {
		$this->todos = array();
		
		if(empty($this->todoIDs)) {
			return;
		}
		$this->todoIDs = array_unique($this->todoIDs);
		
		$todoList = new ToDoList();
		
		$todoList->getConditionBuilder()->add('todo_table.id IN (?)', array($this->todoIDs));
		$todoList->getConditionBuilder()->add('(todo_table.private = ? or todo_table.submitter = ?)', array(0, WCF::getUser()->userID));
		//$todoList->getConditionBuilder()->add('todo_table.isDisabled = ?', array(0));
		//$todoList->getConditionBuilder()->add('todo_table.isDeleted = ?', array(0));
		$todoList->readObjects();
		
		$this->todos = $todoList->getObjects();
	}
}