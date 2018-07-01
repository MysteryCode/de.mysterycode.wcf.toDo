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
		UserStorageHandler::getInstance()->resetAll('todoListAccessable');
		$test = UserStorageHandler::getInstance()->getField('todoListAccessable');
		
		if ($test === null) {
			$categoryList = new RestrictedTodoCategoryNodeList();
			$todoList = new AccessibleToDoList();
			$todoList->readObjectIDs();
			
			$res = false;
			foreach ($categoryList as $category) {
				if ($category->categoryID) {
					$res = true;
					break;
				}
			}
			
			$test = intval($res || !empty($todoList->getObjectIDs()));
			
			UserStorageHandler::getInstance()->update(WCF::getUser()->userID, 'todoListAccessable', $test);
		}
		
		return $test;
		
	}
}
