<?php

namespace wcf\system\comment\manager;
use wcf\data\comment\response\CommentResponse;
use wcf\data\comment\Comment;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoCache;
use wcf\system\WCF;

/**
 * Shows the todo comment manager.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoCommentManager extends AbstractCommentManager {
	protected $currentToDo = null;
	
	/**
	 * @inheritDoc
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
	 * @inheritDoc
	 */
	public function canEditComment(Comment $comment) {
		$this->setCurrentToDo($comment->objectID);
		
		return $this->canEdit(($comment->userID == WCF::getUser()->userID));
	}
	
	/**
	 * @inheritDoc
	 */
	public function canEditResponse(CommentResponse $response) {
		$this->setCurrentToDo($response->getComment()->objectID);
		
		return $this->canEdit(($response->userID == WCF::getUser()->userID));
	}
	
	/**
	 * @inheritDoc
	 */
	public function canDeleteComment(Comment $comment) {
		$this->setCurrentToDo($comment->objectID);
		
		return $this->canDelete(($comment->userID == WCF::getUser()->userID));
	}
	
	/**
	 * @inheritDoc
	 */
	public function canDeleteResponse(CommentResponse $response) {
		$this->setCurrentToDo($response->getComment()->objectID);
		
		return $this->canDelete(($response->userID == WCF::getUser()->userID));
	}
	
	/**
	 * @inheritDoc
	 */
	public function canModerate($objectTypeID, $objectID) {
		if (!$this->isAccessible($objectID)) {
			return false;
		}
		
		if (!WCF::getUser()->userID) {
			return false;
		}
		
		return WCF::getSession()->getPermission('mod.toDo.comment.canModerate');
	}

	/**
	 * Sets the current entry.
	 *
	 * @param integer $todoID
	 * @throws \wcf\system\exception\SystemException
	 */
	protected function setCurrentToDo($todoID) {
		$this->currentToDo = ToDoCache::getInstance()->getTodo($todoID);
		
		if (empty($this->currentToDo))
			$this->currentToDo = new ToDo($todoID);
	}
	
	/**
	 * @inheritDoc
	 */
	public function isAccessible($objectID, $validateWritePermission = false) {
		$this->setCurrentToDo($objectID);
		
		if (!$this->currentToDo)
			return false;
		
		// check object id
		if (!$this->currentToDo->todoID)
			return false;
		
		// check view permission
		if (!$this->currentToDo->canEnter())
			return false;
		
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	protected function canEdit($isOwner) {
		return $this->canModify($isOwner, 'mod.toDo.comment.canEdit');
	}
	
	/**
	 *
	 * @inheritDoc
	 */
	protected function canDelete($isOwner) {
		return $this->canModify($isOwner, 'mod.toDo.comment.canDelete');
	}
	
	/**
	 * Returns true if the active user has the permission to modify a comment.
	 *
	 * @param integer $isOwner        	
	 * @param string $modifyPermission        	
	 * @return boolean
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
		if (!$this->isAccessible($this->currentToDo->todoID)) {
			return false;
		}
		
		if (WCF::getSession()->getPermission($modifyPermission)) {
			return true;
		}
		
		if ($isOwner && WCF::getSession()->getPermission($modifyPermission) . 'Own') {
			return true;
		}
		
		return false;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getLink($objectTypeID, $objectID) {
		$todo = new ToDo($objectID);
		return $todo->getLink();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTitle($objectTypeID, $objectID, $isResponse = false) {
		if ($isResponse) {
			return WCF::getLanguage()->get('wcf.toDo.comment.response');
		}
		
		return WCF::getLanguage()->getDynamicVariable('wcf.toDo.comment');
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateCounter($objectID, $value) {

	}
}
