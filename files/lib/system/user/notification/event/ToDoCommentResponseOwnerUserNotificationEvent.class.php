<?php

namespace wcf\system\user\notification\event;
use wcf\data\comment\Comment;
use wcf\data\todo\ToDo;
use wcf\data\user\User;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Shows the todo comment response owner user notification event.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoCommentResponseOwnerUserNotificationEvent extends AbstractUserNotificationEvent {
	public function getTitle() {
		return $this->getLanguage()->get('wcf.toDo.commentResponseOwner.notification.title');
	}
	
	public function getMessage() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($this->userNotificationObject->objectID);
		$commentAuthor = new User($comment->userID);
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponseOwner.notification.message', array(
			'todo' => $todo,
			'author' => $this->author,
			'commentAuthor' => $commentAuthor 
		));
	}
	
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($this->userNotificationObject->objectID);
		$commentAuthor = new User($comment->userID);
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponseOwner.notification.mail', array(
			'todo' => $todo,
			'author' => $this->author,
			'commentAuthor' => $commentAuthor 
		));
	}
	
	public function getLink() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($this->userNotificationObject->objectID);
		
		return LinkHandler::getInstance()->getLink('ToDo', array(
			'application' => 'wcf',
			'object' => $todo
		), '#comments');
	}
}