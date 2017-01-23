<?php

namespace wcf\system\user\notification\event;
use wcf\system\request\LinkHandler;
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
	/**
	 * @inheritdoc
	 */
	protected $stackable = true;

	/**
	 * {@inheritDoc}
	 */
	public function getTitle() {
		$count = count($this->getAuthors());
		
		// this notification was triggered by multiple users
		if ($count > 1) {
			return $this->getLanguage()->getDynamicVariable('wcf.toDo.notification.create.title.stacked', [
				'count' => $count,
				'timesTriggered' => $this->notification->timesTriggered
			]);
		}
		
		return $this->getLanguage()->get('wcf.toDo.notification.create.title');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMessage() {
		$authors = array_values($this->getAuthors());
		$count = count($authors);
		
		// this notification was triggered by multiple users
		if ($count > 1) {
			return $this->getLanguage()->getDynamicVariable('wcf.toDo.notification.create.message.stacked', [
				'todo' => $this->userNotificationObject->object,
				'author' => $this->author,
				'authors' => $authors,
				'count' => $count,
				'others' => $count - 1
			]);
		}
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.notification.create.message', [
				'todo' => $this->userNotificationObject->object,
				'author' => $this->author
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmailMessage($notificationType = 'instant') {
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.notification.create.mail', [
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
