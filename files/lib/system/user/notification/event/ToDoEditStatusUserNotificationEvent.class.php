<?php

namespace wcf\system\user\notification\event;
use wcf\system\request\LinkHandler;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Notification when the status of a todo has been updated
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoEditStatusUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * {@inheritDoc}
	 */
	public function getTitle() {
		return $this->getLanguage()->get('wcf.toDo.notification.editstatus.title');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMessage() {
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.notification.editstatus.message', [
			'todo' => $this->userNotificationObject->object,
			'author' => $this->author
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmailMessage($notificationType = 'instant') {
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.notification.editstatus.mail', [
			'todo' => $this->userNotificationObject->object,
			'author' => $this->author
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('Todo', [
			'application' => 'wcf',
			'object' => $this->userNotificationObject->object
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function checkAccess() {
		if (empty($this->userNotificationObject->object) || !$this->userNotificationObject->getObjectID())
			return false;
		
		$returnValue = $this->userNotificationObject->object->canEnter();
		if (!$returnValue && $this->userNotificationObject->getObjectID()) {
			// remove subscription
			UserObjectWatchHandler::getInstance()->deleteObjects('de.mysterycode.wcf.toDo', [$this->userNotificationObject->getObjectID()], [WCF::getUser()->userID]);
			
			// reset user storage
			UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'wcfUnreadWatchedTodos');
		}
		
		return $returnValue;
	}
}
