<?php

namespace wcf\system\user\activity\event;
use wcf\data\todo\ToDoList;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Shows the todo user activity event.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * language variable to be used in event output
	 * @var	string
	 */
	protected $languageVariable = 'wcf.user.profile.recentActivity.todo';

	/**
	 * {@inheritDoc}
	 */
	public function prepare(array $events) {
		$objectIDs = [];
		foreach ($events as $event) {
			$objectIDs[] = $event->objectID;
		}
		
		$todoList = new ToDoList();
		$todoList->getConditionBuilder()->add("todo_table.todoID IN (?)", [
			$objectIDs
		]);
		$todoList->readObjects();
		$todos = $todoList->getObjects();
		
		/** @var \wcf\data\user\activity\event\ViewableUserActivityEvent $event */
		foreach ($events as $event) {
			if (isset($todos[$event->objectID])) {
				/** @var \wcf\data\todo\ToDo $todo */
				$todo = $todos[$event->objectID];
				
				if (!$todo->canEnter()) {
					continue;
				}
				$event->setIsAccessible();
				
				$text = WCF::getLanguage()->getDynamicVariable($this->languageVariable, [
					'todo' => $todo
				]);
				$event->setTitle($text);
				
				$event->setDescription($todo->getSimplifiedFormattedMessage());
			} else {
				$event->setIsOrphaned();
			}
		}
	}
}
