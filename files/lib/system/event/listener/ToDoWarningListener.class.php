<?php

namespace wcf\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\todo\ToDoHandler;
use wcf\system\WCF;

/**
 * Shows the todo warning listener.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoWarningListener implements IParameterizedEventListener {
	public $unsolved = 0;
	public $overdue = 0;
	public $waiting = 0;
	
	/**
	 * @see \wcf\system\event\listener\IParameterizedEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (WCF::getUser()->userID != 0) {
			$this->unsolved = ToDoHandler::getInstance()->getUnsolovedTodoCount(WCF::getUser()->userID);
			$this->overdue = ToDoHandler::getInstance()->getOverdueTodoCount(WCF::getUser()->userID);
			$this->waiting = ToDoHandler::getInstance()->getWaitingTodoCount(WCF::getUser()->userID);
		}
		
		WCF::getTPL()->assign(array(
			'unsolvedToDoCount' => $this->unsolved,
			'overdueToDoCount' => $this->overdue,
			'waitingToDoCount' => $this->waiting
		));
	}
}
