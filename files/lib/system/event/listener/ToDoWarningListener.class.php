<?php

namespace wcf\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\system\todo\ToDoHandler;
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
class ToDoWarningListener implements IEventListener {
	public $unsolved = 0;
	public $overdue = 0;
	
	/**
	 *
	 * @see \wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (WCF::getUser()->userID != 0) {
			$this->unsolved = ToDoHandler::getInstance()->getUnsolovedTodoCount(WCF::getUser()->userID);
			$this->overdue = ToDoHandler::getInstance()->getOverdueTodoCount(WCF::getUser()->userID);
		}
		
		WCF::getTPL ()->assign(array(
			'unsolvedToDoCount' => $this->unsolved,
			'overdueToDoCount' => $this->overdue 
		));
	}
}
