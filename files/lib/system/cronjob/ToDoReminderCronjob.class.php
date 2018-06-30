<?php

namespace wcf\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\data\todo\ToDoAction;
use wcf\data\todo\ToDoList;
use wcf\system\user\notification\object\ToDoUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;

/**
 * Creates notifications to remind of todos.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoReminderCronjob extends AbstractCronjob {
	/**
	 * {@inheritDoc}
	 */
	public function execute(Cronjob $cronjob) {
		$check = TIME_NOW;
		$todoIDs = [];
		
		$todoList = new ToDoList();
		$todoList->getConditionBuilder()->add('todo_table.remembertime > ?', [0]);
		$todoList->getConditionBuilder()->add('todo_table.remembertime <= ?', [$check]);
		$todoList->getConditionBuilder()->add('todo_table.statusID IN (?)', [[1, 2, 5]]);
		$todoList->readObjects();
		$todos = $todoList->getObjects();

		/** @var \wcf\data\todo\ToDo $todo */
		foreach ($todos as $todo) {
			$users = array_unique(array_merge([$todo->submitter], $todo->getResponsibleIDs()));
			UserNotificationHandler::getInstance()->fireEvent('remember', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject($todo), $users);
			$todoIDs[] = $todo->todoID;
		}
		
		if (!empty($todoIDs)) {
			$todoAction = new ToDoAction($todoIDs, 'update', ['data' => ['remembertime' => 0]]);
			$todoAction->executeAction();
		}
	}
}
