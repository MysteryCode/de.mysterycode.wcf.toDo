<?php

namespace wcf\system\event\listener;
use wcf\data\todo\ToDoAction;
use wcf\data\todo\ToDoList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;
use wcf\data\todo\category\TodoCategoryEditor;

class TodoCategoryActionListener implements IParameterizedEventListener {

	/**
	 * @see	\wcf\system\event\listener\IParameterizedEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		$actionName = $eventObj->getActionName();
		if ($actionName != 'create' && $actionName != 'update' && $actionName != 'delete') {
			return;
		}

		if ($eventName == 'initializeAction') {
			if ($actionName == 'delete') {
				foreach ($eventObj->getObjectIDs() as $categoryID) {
					$todoList = new ToDoList();
					$todoList->getConditionBuilder()->add('todo_table.categoryID = ?', array(
						$categoryID
					));
					$todoList->readObjects();
					
					$todos = $todoList->getObjects();
					
					// execute action
					if (!empty($todos)) {
						$deleteAction = new ToDoAction($todos, 'delete');
						$deleteAction->executeAction();
					}
				}
			}
		} else {
			$objectTypeID = ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.category', 'de.mysterycode.wcf.toDo');
			
			$resetCache = false;
			if ($actionName == 'create') {
				// check if category is a todo category
				$returnValues = $eventObj->getReturnValues();
				if ($returnValues['returnValues']->objectTypeID == $objectTypeID) {
					$resetCache = true;
				}
			} else if ($actionName == 'update') {
				// check if there is at least one todo category
				foreach ($eventObj->getObjects() as $category) {
					if ($category->objectTypeID == $objectTypeID) {
						$resetCache = true;
						break;
					}
				}
			}
			
			// reset cache if necessary
			if ($resetCache) {
				TodoCategoryEditor::resetCache();
			}
		}
	}
}
