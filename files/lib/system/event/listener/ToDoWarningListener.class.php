<?php

namespace wcf\system\event\listener;
<<<<<<< HEAD
use wcf\system\event\IEventListener;
=======
use wcf\system\event\listener\IParameterizedEventListener;
>>>>>>> master
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
<<<<<<< HEAD
class ToDoWarningListener implements IEventListener {
=======
class ToDoWarningListener implements IParameterizedEventListener {
>>>>>>> master
	public $unsolved = 0;
	public $overdue = 0;
	
	/**
<<<<<<< HEAD
	 *
	 * @see \wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
=======
	 * @see \wcf\system\event\listener\IParameterizedEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
>>>>>>> master
		if (WCF::getUser()->userID != 0) {
			$this->unsolved = ToDoHandler::getInstance()->getUnsolovedTodoCount(WCF::getUser()->userID);
			$this->overdue = ToDoHandler::getInstance()->getOverdueTodoCount(WCF::getUser()->userID);
		}
		
<<<<<<< HEAD
		WCF::getTPL ()->assign(array(
			'unsolvedToDoCount' => $this->unsolved,
			'overdueToDoCount' => $this->overdue 
=======
		WCF::getTPL()->assign(array(
			'unsolvedToDoCount' => $this->unsolved,
			'overdueToDoCount' => $this->overdue
>>>>>>> master
		));
	}
}
