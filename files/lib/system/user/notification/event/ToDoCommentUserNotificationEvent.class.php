<?php

namespace wcf\system\user\notification\event;

use wcf\data\todo\ToDo;
use wcf\system\request\LinkHandler;

/**
 * Shows the todo comment user notification event.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoCommentUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @inheritdoc
	 */
	protected $stackable = true;
	
	public function getTitle() {
		$count = count($this->getAuthors());
		
		// this notification was triggered by multiple users
		if ($count > 1) {
			return $this->getLanguage()->getDynamicVariable('wcf.toDo.comment.notification.title.stacked', array(
				'count' => $count,
				'timesTriggered' => $this->notification->timesTriggered
			));
		}
		
		return $this->getLanguage()->get('wcf.toDo.comment.notification.title');
	}
	
	public function getMessage() {
		$todo = new ToDo($this->userNotificationObject->objectID);
		
		$authors = array_values($this->getAuthors());
		$count = count($authors);
		
		// this notification was triggered by multiple users
		if ($count > 1) {
			return $this->getLanguage()->getDynamicVariable('wcf.toDo.comment.notification.message.stacked', array(
				'todo' => $todo,
				'author' => $this->author,
				'authors' => $authors,
				'count' => $count,
				'others' => $count - 1
			));
		}
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.comment.notification.message', array(
			'todo' => $todo,
			'author' => $this->author 
		));
	}
	
	public function getEmailMessage($notificationType = 'instant') {
		$todo = new ToDo($this->userNotificationObject->objectID);
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.comment.notification.mail', array(
			'todo' => $todo,
			'author' => $this->author 
		));
	}
	
	public function getLink() {
		$todo = new ToDo($this->userNotificationObject->objectID);
		
		return LinkHandler::getInstance()->getLink('ToDo', array(
			'application' => 'wcf',
			'object' => $todo
		), '#comments');
	}
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::checkAccess()
	 */
	public function checkAccess() {
		$todo = new ToDo($this->userNotificationObject->objectID);
		
		if (empty($todo))
			$returnValue = false;
		
		$returnValue = $todo->canEnter();
		if (!$todo->canEnter()) {
// 			// remove subscription
// 			UserObjectWatchHandler::getInstance()->deleteObjects('de.mysterycode.wcf.toDo', array($todo->todoID), array(WCF::getUser()->userID));
			
// 			// reset user storage
// 			UserStorageHandler::getInstance()->reset(array(WCF::getUser()->userID), 'wcfUnreadWatchedTodos');
			
			$returnValue = false;
		}
		
		return $returnValue;
	}
}
