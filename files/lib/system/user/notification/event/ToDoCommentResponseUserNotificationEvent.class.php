<?php

namespace wcf\system\user\notification\event;
use wcf\data\comment\Comment;
use wcf\data\todo\ToDo;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Shows the todo comment response user notification event.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoCommentResponseUserNotificationEvent extends AbstractUserNotificationEvent {
	public function getTitle() {
		return $this->getLanguage()->get('wcf.toDo.commentResponse.notification.title');
	}
	
	public function getMessage() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($comment->objectID);
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponse.notification.message', array(
			'todo' => $todo,
			'author' => $this->author 
		));
	}
	
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($comment->objectID);
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponse.notification.mail', array(
			'todo' => $todo,
			'author' => $this->author 
		));
	}
	
	public function getLink() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($comment->objectID);
		
		return LinkHandler::getInstance()->getLink('ToDo', array(
			'application' => 'wcf',
			'object' => $todo
		), '#comments' );
	}
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::checkAccess()
	 */
	public function checkAccess() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($comment->objectID);
		
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
