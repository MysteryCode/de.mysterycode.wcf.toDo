<?php

namespace wcf\page;
use wcf\data\todo\ToDoList;
use wcf\data\user\online\UsersOnlineList;
use wcf\data\user\User;
use wcf\data\ILinkableObject;
use wcf\page\SortablePage;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the todo archive page.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoArchivePage extends SortablePage {
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
	public $validSortFields = array(
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
	
	public $neededModules = array('TODOLIST');
	
	public $neededPermissions = array('user.toDo.toDo.canViewList');
	
	/**
	 * @see	\wcf\page\AbstractPage::$enableTracking
	 */
	public $enableTracking = true;
	
	/**
	 *
	 * @see \wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		parent::initObjectList ();
		
		$this->objectList->getConditionBuilder()->add("status = ?", array(3));
	}
	
	/**
	 *
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		DashboardHandler::getInstance()->loadBoxes('de.mysterycode.wcf.ToDoArchivePage', $this);
		
		WCF::getTPL()->assign(array(
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.mysterycode.wcf.ToDoTrashPage'),
			'sidebarName' => 'de.mysterycode.wcf.ToDoArchivePage',
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo')),
		));
	}
}