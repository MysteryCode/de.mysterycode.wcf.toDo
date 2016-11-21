<?php

namespace wcf\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoAction;
use wcf\data\todo\ToDoList;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\user\notification\object\ToDoUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;

/**
 * Creates notifications to remind of todos.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoReminderCronjob extends AbstractCronjob {
	public function execute(Cronjob $cronjob) {
		$check = TIME_NOW;
		$todoIDs = array();
		
		$todoList = new ToDoList();
		$todoList->getConditionBuilder()->add('todo_table.remembertime > ?', array(0));
		$todoList->getConditionBuilder()->add('todo_table.remembertime <= ?', array($check));
		$todoList->getConditionBuilder()->add('todo_table.statusID IN (?)', array(array(1, 2, 5)));
		$todoList->readObjects();
		$todos = $todoList->getObjects();
		
		foreach ($todos as $todo) {
			$users = array_unique(array_merge(array($todo->submitter), $todo->getResponsibleIDs()));
			UserNotificationHandler::getInstance()->fireEvent('remember', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject($todo), $users);
			$todoIDs[] = $todo->todoID;
		}
		
		if (!empty($todoIDs)) {
			$todoAction = new ToDoAction($todoIDs, 'update', array('data' => array('remembertime' => 0)));
			$todoAction->executeAction();
		}
	}
}
