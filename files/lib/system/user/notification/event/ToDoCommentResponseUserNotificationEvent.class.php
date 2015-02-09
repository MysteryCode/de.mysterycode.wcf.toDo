<?php

namespace wcf\system\user\notification\event;
use wcf\data\comment\Comment;
use wcf\data\todo\ToDo;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Shows the todo comment response user notification event.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
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
}
