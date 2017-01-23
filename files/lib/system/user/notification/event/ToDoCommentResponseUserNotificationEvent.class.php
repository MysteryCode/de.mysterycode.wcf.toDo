<?php

namespace wcf\system\user\notification\event;
use wcf\data\comment\Comment;
use wcf\data\todo\ToDo;
use wcf\system\request\LinkHandler;

/**
 * Shows the todo comment response user notification event.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoCommentResponseUserNotificationEvent extends AbstractUserNotificationEvent {
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
			return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponse.notification.titlestacked', [
				'count' => $count,
				'timesTriggered' => $this->notification->timesTriggered
			]);
		}
		
		return $this->getLanguage()->get('wcf.toDo.commentResponse.notification.title');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMessage() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($comment->objectID);
		
		$authors = array_values($this->getAuthors());
		$count = count($authors);
		
		// this notification was triggered by multiple users
		if ($count > 1) {
			return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponse.notification.message.stacked', [
				'todo' => $todo,
				'author' => $this->author,
				'authors' => $authors,
				'count' => $count,
				'others' => $count - 1
			]);
		}
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponse.notification.message', [
			'todo' => $todo,
			'author' => $this->author
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($comment->objectID);
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponse.notification.mail', [
			'todo' => $todo,
			'author' => $this->author
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLink() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($comment->objectID);
		
		return LinkHandler::getInstance()->getLink('ToDo', [
			'application' => 'wcf',
			'object' => $todo
		], '#comments' );
	}
	
	/**
	 * @inheritDoc
	 */
	public function checkAccess() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($comment->objectID);
		
		if (empty($todo))
			return false;
		
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
