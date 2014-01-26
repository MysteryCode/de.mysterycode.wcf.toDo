<?php
namespace wcf\system\user\notification\event;
use wcf\data\comment\Comment;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Shows the toDo comment user notification event.
 *
 * @author	Florian Gail
 * @copyright	2013 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoCommentUserNotificationEvent extends AbstractUserNotificationEvent {

	public function getTitle() {
		return $this->getLanguage()->get('wcf.toDo.comment.notification.title');
	}
	
	public function getMessage() {
		$toDo = array();
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.comment.notification.message', array(
			'toDo' => $toDo,
			'author' => $this->author
		));
	}

	public function getEmailMessage($notificationType = 'instant') {
		$toDo = new ToDo($this->userNotificationObject->objectID);
		
		return $this->getLanguage()->getDynamicVariable('wcf.toDo.comment.notification.mail', array(
			'toDo' => $toDo,
			'author' => $this->author
		));
	}

	public function getLink() {
		$comment = new Comment($this->userNotificationObject->commentID);
		
		return LinkHandler::getInstance()->getLink('ToDo', array(
			'application' => 'wcf',
			'id' => $comment->objectID
		), '#comments');
	}
}