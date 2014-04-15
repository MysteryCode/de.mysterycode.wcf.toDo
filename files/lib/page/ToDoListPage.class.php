<?php

namespace wcf\page;
use wcf\data\todo\ToDoList;
use wcf\data\user\User;
use wcf\data\user\online\UsersOnlineList;
use wcf\data\ILinkableObject;
use wcf\page\SortablePage;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the todo list page.
 *
 * @author Florian Gail
 * @copyright 2014 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoListPage extends SortablePage {
	/**
	 *
	 * @see wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.header.menu.toDo';
	
	/**
	 *
	 * @see \wcf\page\SortablePage::$defaultSortField
	 */
	public $defaultSortField = TODO_DEFAULT_SORT_FIELD;
	
	/**
	 *
	 * @see \wcf\page\SortablePage::$defaultSortOrder
	 */
	public $defaultSortOrder = TODO_DEFAULT_SORT_ORDER;
	
	/**
	 *
	 * @see \wcf\page\SortablePage::$validSortFields
	 */
	public $validSortFields = array (
		'status',
		'category',
		'title',
		'submitTime',
		'endTime',
		'submitter',
		'timestamp',
		'updatetimestamp',
		'important',
		'remembertime' 
	);
	
	/**
	 *
	 * @see \wcf\page\MultipleLinkPage::$itemsPerPage
	 */
	public $itemsPerPage = TODO_TODOS_PER_PAGE;
	
	/**
	 *
	 * @see \wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'wcf\data\todo\ToDoList';
	public $neededModules = array (
		'TODOLIST' 
	);
	public $neededPermissions = array (
		'user.toDo.toDo.canViewList' 
	);
	
	/**
	 *
	 * @see \wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		parent::initObjectList ();
		
		$this->objectList->getConditionBuilder()->add( "private = ? or submitter = ?", array (
			0,
			WCF::getUser ()->userID 
		) );
	}
	
	/**
	 *
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables ();
		
		DashboardHandler::getInstance()->loadBoxes( 'de.mysterycode.wcf.ToDoListPage', $this );
		
		WCF::getTPL ()->assign ( array (
			'entryCount' => count( $this->objectList->objects ),
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed( 'com.woltlab.wcf.collapsibleSidebar', 'de.mysterycode.wcf.ToDoListPage' ),
			'sidebarName' => 'de.mysterycode.wcf.ToDoListPage' 
		) );
	}
}