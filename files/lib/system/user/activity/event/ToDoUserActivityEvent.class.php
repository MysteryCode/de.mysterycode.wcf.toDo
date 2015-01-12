<?php

namespace wcf\system\user\activity\event;
use wcf\data\todo\ToDoList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Shows the todo user activity event.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * language variable to be used in event output
	 * @var	string
	 */
	protected $languageVariable = 'wcf.user.profile.recentActivity.todo';
	
	public function prepare(array $events) {
		
		$objectIDs = array();
		foreach($events as $event) {
			$objectIDs[] = $event->objectID;
		}
		
		$todoList = new ToDoList();
		$todoList->getConditionBuilder()->add("todo_table.id IN (?)", array($objectIDs));
		$todoList->readObjects();
		$todos = $todoList->getObjects();
		
		foreach ($events as $event) {
			if (isset($todos[$event->objectID])) {
				$todo = $todos[$event->objectID];
				
				if (!$todo->canEnter()) {
					continue;
				}
				$event->setIsAccessible();
				
				
				$text = WCF::getLanguage()->getDynamicVariable($this->languageVariable, array('todo' => $todo));
				$event->setTitle($text);
				
				$event->setDescription($todo->getExcerpt());
			}
			else {
				$event->setIsOrphaned();
			}
		}
	}
}