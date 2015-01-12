<?php

namespace wcf\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\todo\ToDoHandler;
use wcf\system\WCF;

/**
 * Changes the usernames in todos when a user wants to change his username.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoUserActionRenameListener implements IParameterizedEventListener {
	/**
	 * @see \wcf\system\event\listener\IParameterizedEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// get userID
		$objects = $eventObj->getObjects();
		$userID = $objects[0]->userID;
		
		// get new username
		$parameters = $eventObj->getParameters();
		$username = $parameters['data']['username'];
		
		WCF::getDB()->beginTransaction();
		
		// todos
		$sql = "UPDATE	wcf".WCF_N."_todo
			SET	username = ?
			WHERE	submitter = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($username, $userID));
		
		// responsibles
		$sql = "UPDATE	wcf".WCF_N."_todo_to_user
			SET	username = ?
			WHERE	userID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($username, $userID));
		
		WCF::getDB()->commitTransaction();
	}
}
