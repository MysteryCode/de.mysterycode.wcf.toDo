<?php

namespace wcf\system\user\notification\event;
use wcf\data\comment\Comment;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Shows the todo comment response user notification event.
 *
 * @author Florian Gail
 * @copyright 2013 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoCommentResponseUserNotificationEvent extends AbstractUserNotificationEvent {
	public function getTitle() {
		return $this->getLanguage ()->get ( 'wcf.toDo.commentResponse.notification.title' );
	}
	public function getMessage() {
		$comment = new Comment ( $this->userNotificationObject->commentID );
		$toDo = array ();
		
		return $this->getLanguage ()->getDynamicVariable ( 'wcf.toDo.commentResponse.notification.message', array (
				'toDo' => $toDo,
				'author' => $this->author 
		) );
	}
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment ( $this->userNotificationObject->commentID );
		$toDo = array ();
		
		return $this->getLanguage ()->getDynamicVariable ( 'wcf.toDo.commentResponse.notification.mail', array (
				'toDo' => $toDo,
				'author' => $this->author 
		) );
	}
	public function getLink() {
		$comment = new Comment ( $this->userNotificationObject->commentID );
		
		return LinkHandler::getInstance ()->getLink ( 'ToDo', array (
				'application' => 'wcf',
				'id' => $comment->objectID 
		), '#comments' );
	}
}