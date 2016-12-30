<?php

namespace wcf\system\user\notification\event;
use wcf\system\request\LinkHandler;

/**
 * Notification when a todo has been edited
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoEditUserNotificationEvent extends AbstractUserNotificationEvent {
	public function getTitle() {
		return $this->getLanguage()->get('wcf.toDo.notification.edit.title');
	}
	
	public function getMessage() {
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.notification.edit.message', array(
			'todo' => $this->userNotificationObject->object,
			'author' => $this->author 
		));
	}
	
	public function getEmailMessage($notificationType = 'instant') {
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.notification.edit.mail', array(
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
		if (empty($this->userNotificationObject->object) || !$this->userNotificationObject->getObjectID())
			$returnValue = false;
		
		$returnValue = $this->userNotificationObject->object->canEnter();
		if (!$returnValue && $this->userNotificationObject->getObjectID()) {
			// remove subscription
			UserObjectWatchHandler::getInstance()->deleteObjects('de.mysterycode.wcf.toDo', array($this->userNotificationObject->getObjectID()), array(WCF::getUser()->userID));
			
			// reset user storage
			UserStorageHandler::getInstance()->reset(array(WCF::getUser()->userID), 'wcfUnreadWatchedTodos');
		}
		
		return $returnValue;
	}
}
