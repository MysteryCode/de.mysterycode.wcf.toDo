<?php

namespace wcf\system\event\listener;

use wcf\system\WCF;

/**
 * Changes the usernames in todos when a user wants to change his username.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoUserActionRenameListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
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
		$statement->execute([$username, $userID]);
		
		// responsibles
		$sql = "UPDATE	wcf".WCF_N."_todo_to_user
			SET	username = ?
			WHERE	userID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$username, $userID]);
		
		WCF::getDB()->commitTransaction();
	}
}
