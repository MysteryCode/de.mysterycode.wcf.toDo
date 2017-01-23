<?php

namespace wcf\page;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoCache;
use wcf\system\comment\CommentHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\like\LikeHandler;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\message\quote\MessageQuoteManager;
use wcf\system\WCF;

/**
 * Shows the toDo detail page.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoPage extends AbstractPage {
	public $neededModules = ['TODOLIST'];
	
	/**
	 * @inheritDoc
	 */
	public $enableTracking = true;
	
	public $todoID = 0;
	public $todo = null;

	/**
	 * @var \wcf\system\comment\manager\ToDoCommentManager
	 */
	public $commentManager = null;
	public $commentList = null;
	public $objectType = 0;

	/**
	 * like data
	 * @var	array<\wcf\data\like\object\LikeObject>
	 */
	public $likeData = null;

	/**
	 * comment objecttype's id
	 * @var integer
	 */
	public $objectTypeID = 0;
	
	/**
	 *
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if(isset($_REQUEST['id'])) $this->todoID = intval($_REQUEST['id']);
		
		MessageQuoteManager::getInstance()->readParameters();
	}
	
	/**
	 *
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		
		$this->todo = ToDoCache::getInstance()->getTodo($this->todoID);
		
		if (empty($this->todo))
			$this->todo = new ToDo($this->todoID);
		
		if($this->todo === null || !$this->todo->todoID)
			throw new IllegalLinkException();
		
		if(!$this->todo->canEnter())
			throw new PermissionDeniedException();
		
		$this->objectTypeID = CommentHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDoComment');
		$objectType = CommentHandler::getInstance()->getObjectType($this->objectTypeID);
		$this->commentManager = $objectType->getProcessor();
		$this->commentList = CommentHandler::getInstance()->getCommentList($this->commentManager, $this->objectTypeID, $this->todoID);
		
		MessageEmbeddedObjectManager::getInstance()->setActiveMessage('de.mysterycode.wcf.toDo', $this->todoID);
		
		if (MODULE_LIKE) {
			$objectType = LikeHandler::getInstance()->getObjectType('de.mysterycode.wcf.toDo.toDo');
			LikeHandler::getInstance()->loadLikeObjects($objectType, [$this->todo->todoID]);
			$this->likeData = LikeHandler::getInstance()->getLikeObject($objectType, $this->todo->todoID);
		}
	}
	
	/**
	 *
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		MessageQuoteManager::getInstance()->assignVariables();
		
		WCF::getTPL()->assign([
			'commentList' => $this->commentList,
			'commentObjectTypeID' => $this->objectTypeID,
			'commentCanAdd' => $this->commentManager->canAdd($this->todoID),
			'lastCommentTime' => $this->commentList->getMinCommentTime(),
			'commentsPerPage' => $this->commentManager->getCommentsPerPage(),
			'likeData' => (MODULE_LIKE ? $this->commentList->getLikeData() : []),
			'todo' => $this->todo,
			'attachmentList' => $this->todo->getAttachments(),
			'todoLikeData' => $this->likeData
		]);
	}
}
