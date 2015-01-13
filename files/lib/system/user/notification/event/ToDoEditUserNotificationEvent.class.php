<?php

namespace wcf\system\user\notification\event;
use wcf\data\comment\Comment;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Notification when a todo has been edited
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
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
}
