<?php

namespace wcf\system\user\object\watch;
use wcf\data\object\type\AbstractObjectTypeProcessor;
use wcf\data\todo\ToDo;
use wcf\system\exception\IllegalLinkException;

use wcf\system\user\storage\UserStorageHandler;

/**
 * Implementation of IUserObjectWatch for watched todos.
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoUserObjectWatch extends AbstractObjectTypeProcessor implements IUserObjectWatch {
	/**
	 * @see	\wcf\system\user\object\watch\IUserObjectWatch::validateObjectID()
	 */
	public function validateObjectID($objectID) {
		$todo = new ToDo($objectID);
		if (!$todo->todoID)
			throw new IllegalLinkException();
		
		// check permission
		$todo->getCategory()->checkPermission();
	}
	
	/**
	 * @see	\wcf\system\user\object\watch\IUserObjectWatch::resetUserStorage()
	 */
	public function resetUserStorage(array $userIDs) {
		UserStorageHandler::getInstance()->reset($userIDs, 'wcfUnreadWatchedTodos');
	}
}
