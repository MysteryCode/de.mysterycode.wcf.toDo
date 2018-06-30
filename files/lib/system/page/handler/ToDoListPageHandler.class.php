<?php

namespace wcf\system\page\handler;

use wcf\data\todo\AccessibleToDoList;
use wcf\data\todo\category\RestrictedTodoCategoryNodeList;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * {@inheritDoc}
 */
class ToDoListPageHandler extends AbstractMenuPageHandler {
	/**
	 * @inheritdoc
	 */
	public function isVisible($objectID = null) {
		$test = UserStorageHandler::getInstance()->getField('todoListAccessable');
		
		if ($test === null) {
			$categoryList = new RestrictedTodoCategoryNodeList();
			$todoList = new AccessibleToDoList();
			$todoList->readObjectIDs();
			
			$test = empty($categoryList) && empty($todoList->getObjectIDs());
			UserStorageHandler::getInstance()->update(WCF::getUser()->userID, 'todoListAccessable', $test);
		}
		
		return $test;
		
	}
}
