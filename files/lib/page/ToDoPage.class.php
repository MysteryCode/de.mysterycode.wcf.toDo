<?php

namespace wcf\page;
use wcf\data\todo\ToDoCache;
use wcf\data\todo\ToDo;
use wcf\data\user\online\UsersOnlineList;
use wcf\data\user\User;
use wcf\data\ILinkableObject;
use wcf\system\comment\CommentHandler;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the toDo detail page.
 *
 * @author Florian Gail
 * @copyright 2014 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
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
	public $neededPermissions = array('user.toDo.toDo.canViewDetail');
	public $todoID = 0;
	public $todo = null;
	public $commentManager = null;
	public $commentList = null;
	public $objectType = 0;
	
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
		if($this->todo === null) {
			throw new IllegalLinkException();
		}
		
		$this->objectTypeID = CommentHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDoComment');
		$objectType = CommentHandler::getInstance()->getObjectType($this->objectTypeID);
		$this->commentManager = $objectType->getProcessor();
		$this->commentList = CommentHandler::getInstance()->getCommentList($this->commentManager, $this->objectTypeID, $this->todoID);
		
		WCF::getBreadcrumbs()->add( new Breadcrumb( WCF::getLanguage()->get('wcf.header.menu.toDo'), LinkHandler::getInstance()->getLink('ToDoList', array())));
		
		if($this->todo->categorytitle != '') {
			WCF::getBreadcrumbs()->add(new Breadcrumb($this->todo->categorytitle, LinkHandler::getInstance()->getLink('ToDoCategory', array(
				'id' => $this->todo->category 
			))));
		}
		
		// users online
		if (MODULE_USERS_ONLINE) {
			$this->usersOnlineList = new UsersOnlineList();
			$this->usersOnlineList->getConditionBuilder()->add('((session.objectType = ? AND session.objectID = ?) OR (session.parentObjectType = ? AND session.parentObjectID = ?))', array('de.mysterycode.wcf.toDo.toDo', $this->todoID, 'de.mysterycode.wcf.toDo.toDo', $this->todoID));
			$this->usersOnlineList->readStats();
			$this->usersOnlineList->getConditionBuilder()->add('session.userID IS NOT NULL');
			$this->usersOnlineList->readObjects();
		}
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
			'likeData' =>(MODULE_LIKE ? $this->commentList->getLikeData() : array()),
			'todo' => $this->todo,
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.mysterycode.wcf.ToDoPage'),
			'sidebarName' => 'de.mysterycode.wcf.ToDoPage' 
		));
	}
}