<?php

namespace wcf\system\user\notification\event;
use wcf\data\comment\Comment;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Notification when todos are created
 *
 * @author Florian Gail
 * @copyright 2013 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
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
		return LinkHandler::getInstance()->getLink ('ToDo', array (
			'application' => 'wcf',
			'id' => $this->userNotificationObject->object->data['id']
		));
	}
}