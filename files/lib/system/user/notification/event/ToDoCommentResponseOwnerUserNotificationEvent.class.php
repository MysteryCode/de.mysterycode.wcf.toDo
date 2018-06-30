<?php

namespace wcf\system\user\notification\event;
use wcf\data\comment\Comment;
use wcf\data\todo\ToDo;
use wcf\data\user\User;
use wcf\system\request\LinkHandler;

/**
 * Shows the todo comment response owner user notification event.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoCommentResponseOwnerUserNotificationEvent extends AbstractUserNotificationEvent {
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
			return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponseOwner.notification.title.stacked', [
				'count' => $count,
				'timesTriggered' => $this->notification->timesTriggered
			]);
		}
		
		return $this->getLanguage()->get('wcf.toDo.commentResponseOwner.notification.title');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMessage() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($comment->objectID);
		$commentAuthor = new User($comment->userID);
		
		$authors = array_values($this->getAuthors());
		$count = count($authors);
		
		// this notification was triggered by multiple users
		if ($count > 1) {
			return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponseOwner.notification.message.stacked', [
				'todo' => $todo,
				'author' => $this->author,
				'authors' => $authors,
				'count' => $count,
				'others' => $count - 1,
				'commentAuthor' => $commentAuthor
			]);
		}
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponseOwner.notification.message', [
			'todo' => $todo,
			'author' => $this->author,
			'commentAuthor' => $commentAuthor
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($comment->objectID);
		$commentAuthor = new User($comment->userID);
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.commentResponseOwner.notification.mail', [
			'todo' => $todo,
			'author' => $this->author,
			'commentAuthor' => $commentAuthor
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLink() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$todo = new ToDo($comment->objectID);
		
		return LinkHandler::getInstance()->getLink('Todo', [
			'application' => 'wcf',
			'object' => $todo
		], '#comments');
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
		if ($todo === null || !$todo->canEnter()) {
// 			// remove subscription
// 			UserObjectWatchHandler::getInstance()->deleteObjects('de.mysterycode.wcf.toDo', array($todo->todoID), array(WCF::getUser()->userID));
			
// 			// reset user storage
// 			UserStorageHandler::getInstance()->reset(array(WCF::getUser()->userID), 'wcfUnreadWatchedTodos');
			
			$returnValue = false;
		}
		
		return $returnValue;
	}
}
