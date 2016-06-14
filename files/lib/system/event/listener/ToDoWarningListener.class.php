<?php

namespace wcf\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

/**
 * Shows the todo warning listener.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
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
			$this->unsolved = 0;//ToDoHandler::getInstance()->getUnsolovedTodoCount(WCF::getUser()->userID);
			$this->overdue = 0;//ToDoHandler::getInstance()->getOverdueTodoCount(WCF::getUser()->userID);
			$this->waiting = 0;//ToDoHandler::getInstance()->getOverdueTodoCount(WCF::getUser()->userID);
		}
		
		WCF::getTPL()->assign(array(
			'unsolvedToDoCount' => $this->unsolved,
			'overdueToDoCount' => $this->overdue,
			'waitingToDoCount' => $this->waiting
		));
	}
}
