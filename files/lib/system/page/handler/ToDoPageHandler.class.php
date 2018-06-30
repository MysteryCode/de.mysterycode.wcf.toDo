<?php

namespace wcf\system\page\handler;

use wcf\data\todo\AccessibleToDoList;
use wcf\data\todo\ToDoCache;

/**
 * {@inheritDoc}
 */
class ToDoPageHandler extends AbstractLookupPageHandler {
	/**
	 * @inheritdoc
	 */
	public function getLink($objectID) {
		return ToDoCache::getInstance()->getTodo($objectID)->getLink();
	}

	/**
	 * @inheritdoc
	 */
	public function isValid($objectID) {
		$todo = ToDoCache::getInstance()->getTodo($objectID);
		return $todo !== null && $todo->todoID;
	}

	/**
	 * @inheritdoc
	 */
	public function isVisible($objectID = null) {
		$todo = ToDoCache::getInstance()->getTodo($objectID);
		return $todo !== null && $todo->isVisible();
	}
	
	/**
	 * @inheritdoc
	 */
	public function lookup($searchString) {
		$todoList = new AccessibleToDoList();
		$todoList->getConditionBuilder()->add('todo_table.title LIKE ?', ['%' . $searchString . '%']);
		$todoList->readObjects();
		
		$todos = [];
		/** @var \wcf\data\todo\ToDo $todo */
		foreach ($todoList as $todo) {
			$todos[$todo->todoID] = $todo->getTitle();
		}
		
		return $todos;
	}
}
