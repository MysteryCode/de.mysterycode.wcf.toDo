<?php

namespace wcf\page;
use wcf\data\todo\ToDo;
use wcf\data\user\online\UsersOnlineList;
use wcf\data\user\User;
use wcf\data\ILinkableObject;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\comment\CommentHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\like\LikeHandler;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;

/**
 * Shows the toDo detail page.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoPage extends AbstractPage {
	/**
	 *
	 * @see wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.header.menu.toDo';
	
	/**
	 * users online list
	 * @var	\wcf\data\user\online\UsersOnlineList
	 */
	public $usersOnlineList = null;
	
	public $neededModules = array('TODOLIST');
	
	/**
	 * @see	\wcf\page\AbstractPage::$enableTracking
	 */
	public $enableTracking = true;
	
	public $todoID = 0;
	public $todo = null;
	public $commentManager = null;
	public $commentList = null;
	public $objectType = 0;

	/**
	 * like data
	 * @var	array<\wcf\data\like\object\LikeObject>
	 */
	public $likeData = null;
	
	/**
	 *
	 * @see wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if(isset($_REQUEST['id'])) $this->todoID = intval($_REQUEST['id']);
	}
	
	/**
	 *
	 * @see wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
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
			LikeHandler::getInstance()->loadLikeObjects($objectType, array($this->todo->todoID));
			$this->likeData = LikeHandler::getInstance()->getLikeObject($objectType, $this->todo->todoID);
		}
		
		WCF::getBreadcrumbs()->add( new Breadcrumb( WCF::getLanguage()->get('wcf.header.menu.toDo'), LinkHandler::getInstance()->getLink('ToDoList', array())));
		
		if ($this->todo->getCategory())
			WCF::getBreadcrumbs()->add($this->todo->getCategory()->getBreadCrumb());
	}
	
	/**
	 *
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$submitter = new User($this->todo->submitter);
		
		DashboardHandler::getInstance()->loadBoxes('de.mysterycode.wcf.ToDoPage', $this);
		
		WCF::getTPL()->assign(array(
			'submitterusername' => $submitter->username,
			'responsibles' => $this->todo->getResponsibleIDs(),
			'commentList' => $this->commentList,
			'commentObjectTypeID' => $this->objectTypeID,
			'commentCanAdd' => $this->commentManager->canAdd($this->todoID),
			'lastCommentTime' => $this->commentList->getMinCommentTime(),
			'commentsPerPage' => $this->commentManager->getCommentsPerPage(),
			'likeData' => (MODULE_LIKE ? $this->commentList->getLikeData() : array()),
			'todo' => $this->todo,
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.mysterycode.wcf.ToDoPage'),
			'sidebarName' => 'de.mysterycode.wcf.ToDoPage',
			'attachmentList' => $this->todo->getAttachments(),
			'todoLikeData' => $this->likeData,
		));
	}
	
	/**
	 * @see	\wcf\page\ITrackablePage::getParentObjectType()
	 */
	public function getParentObjectType() {
		return 'de.mysterycode.wcf.toDo';
	}
	
	/**
	 * @see	\wcf\page\ITrackablePage::getParentObjectID()
	 */
	public function getParentObjectID() {
		if ($this->todo)
			return $this->todo->categoryID;
		
		return 0;
	}
	
	/**
	 * @see	\wcf\page\ITrackablePage::getObjectType()
	 */
	public function getObjectType() {
		return 'de.mysterycode.wcf.toDo.toDo';
	}
	
	/**
	 * @see	\wcf\page\ITrackablePage::getObjectID()
	 */
	public function getObjectID() {
		if ($this->todo)
			return $this->todoID;
		
		return 0;
	}
}
