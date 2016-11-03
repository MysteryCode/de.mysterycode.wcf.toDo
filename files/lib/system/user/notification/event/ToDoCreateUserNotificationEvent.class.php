<?php

namespace wcf\system\user\notification\event;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Notification when todos are created
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoCreateUserNotificationEvent extends AbstractUserNotificationEvent {
	public function getTitle() {
		return $this->getLanguage()->get('wcf.toDo.notification.create.title');
	}
	
	public function getMessage() {
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.notification.create.message', array(
			'todo' => $this->userNotificationObject->object,
			'author' => $this->author 
		));
	}
	
	public function getEmailMessage($notificationType = 'instant') {
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.notification.create.mail', array(
			'todo' => $this->userNotificationObject->object,
			'author' => $this->author 
		));
	}
	
	public function getLink() {
		return LinkHandler::getInstance()->getLink('ToDo', array(
			'application' => 'wcf',
			'object' => $this->userNotificationObject->object
		));
	}
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::checkAccess()
	 */
	public function checkAccess() {
		if (empty($this->userNotificationObject->object))
			$returnValue = false;
		
		$returnValue = $this->userNotificationObject->object->canEnter();
		if (!$returnValue) {
			// remove subscription
			UserObjectWatchHandler::getInstance()->deleteObjects('de.mysterycode.wcf.toDo', array($this->userNotificationObject->todoID), array(WCF::getUser()->userID));
			
			// reset user storage
			UserStorageHandler::getInstance()->reset(array(WCF::getUser()->userID), 'wcfUnreadWatchedTodos');
		}
		
		return $returnValue;
	}
}
