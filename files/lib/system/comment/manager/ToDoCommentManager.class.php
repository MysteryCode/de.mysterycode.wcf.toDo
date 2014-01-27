<?php

namespace wcf\system\comment\manager;
use wcf\data\comment\response\CommentResponse;
use wcf\data\comment\Comment;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the toDo comment manager.
 *
 * @author	Florian Gail
 * @copyright	2013 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoCommentManager extends AbstractCommentManager {
	protected $currentToDo = null;

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::canAdd()
	 */
	public function canAdd($objectID) {
		if (!$this->isAccessible($objectID)) {
			return false;
		}
		
		if (!WCF::getUser()->userID) {
			return false;
		}
		
		return WCF::getSession()->getPermission('user.toDo.comment.canAdd');
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::canEditComment()
	 */
	public function canEditComment(Comment $comment) {
		$this->setCurrentToDo($comment->objectID);
		
		return $this->canEdit(($comment->userID == WCF::getUser()->userID));
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::canEditResponse()
	 */
	public function canEditResponse(CommentResponse $response) {
		$this->setCurrentToDo($response->getComment()->objectID);
		
		return $this->canEdit(($response->userID == WCF::getUser()->userID));
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::canDeleteComment()
	 */
	public function canDeleteComment(Comment $comment) {
		$this->setCurrentToDo($comment->objectID);

		return $this->canDelete(($comment->userID == WCF::getUser()->userID));
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::canDeleteResponse()
	 */
	public function canDeleteResponse(CommentResponse $response) {
		$this->setCurrentToDo($response->getComment()->objectID);

		return $this->canDelete(($response->userID == WCF::getUser()->userID));
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::canModerate()
	 */
	public function canModerate($objectTypeID, $objectID) {
		if (!$this->isAccessible($objectID)) {
			return false;
		}
		
		if (!WCF::getUser()->userID) {
			return false;
		}
		
		return WCF::getSession()->getPermission('mod.canModerateToDoComment');
	}

	/**
	 * Sets the current entry.
	 *
	 * @param	array		$toDoID
	 */
	protected function setCurrentToDo($toDoID) {
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo
			WHERE id = " . $toDoID;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$this->currentToDo = $statement->fetchArray();
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::isAccessible()
	 */
	public function isAccessible($objectID, $validateWritePermission = false) {
		$this->setCurrentToDo($objectID);

		// check object id
		if (!$this->currentToDo['id'])
			return false;

		// check view permission
		if(!WCF::getSession()->getPermission('user.toDo.toDo.canViewDetail'))
			return false;
		
		if ($this->currentToDo['private'] == 1 && $this->currentToDo['submitter'] != WCF::getUser()->userID)
			return false;
		
		return true;
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::canEdit()
	 */
	protected function canEdit($isOwner) {
		return $this->canModify($isOwner, 'user.toDo.comment.canEdit');
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::canDelete()
	 */
	protected function canDelete($isOwner) {
		return $this->canModify($isOwner, 'user.toDo.comment.canDelete');
	}

	/**
	 * Returns true if the active user has the permission to modify a comment.
	 *
	 * @param	integer		$isOwner
	 * @param	string		$modifyPermission
	 * @return	boolean
	 */
	protected function canModify($isOwner, $modifyPermission) {
		// disallow guests
		if (!WCF::getUser()->userID) {
			return false;
		}
		
		if ($this->currentToDo === null) {
			return false;
		}
		
		// check access
		if (!$this->isAccessible($this->currentToDo['id'])) {
			return false;
		}
		
		if(WCF::getSession()->getPermission($modifyPermission)) {
			return true;
		}
		
		if($isOwner && WCF::getSession()->getPermission($modifyPermission) . 'Own') {
			return true;
		}

		return false;
	}
	
	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::getLink()
	 */
	public function getLink($objectTypeID, $objectID) {
		return LinkHandler::getInstance()->getLink('ToDo', array('id' => $objectID));
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::getTitle()
	 */
	public function getTitle($objectTypeID, $objectID, $isResponse = false) {
		if ($isResponse) {
			return WCF::getLanguage()->get('wcf.toDo.comment.response');
		}

		return WCF::getLanguage()->getDynamicVariable('wcf.toDo.comment');
	}
	
	public function updateCounter($objectID, $value) { }
}