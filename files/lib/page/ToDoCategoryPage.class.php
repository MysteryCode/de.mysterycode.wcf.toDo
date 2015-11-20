<?php

namespace wcf\page;
use wcf\data\category\Category;
use wcf\data\todo\category\TodoCategory;
use wcf\data\todo\category\RestrictedTodoCategoryNodeList;
use wcf\data\todo\ToDo;
use wcf\data\user\online\UsersOnlineList;
use wcf\data\user\User;
use wcf\data\ILinkableObject;
use wcf\page\SortablePage;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the todo category page.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoCategoryPage extends SortablePage {
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
		'status',
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
	
	public $neededPermissions = array('user.toDo.toDo.canViewList');
	
	public $neededModules = array('TODOLIST');
	
	/**
	 * @see	\wcf\page\AbstractPage::$enableTracking
	 */
	public $enableTracking = true;
	
	public $categoryID = 0;
	
	/**
	 * category node list
	 * @var	\wcf\data\todo\category\RestrictedTodoCategoryNodeList
	 */
	public $categoryNodeList = null;
	
	/**
	 *
	 * @see wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if(isset($_REQUEST['id'])) $this->categoryID = intval($_REQUEST['id']);
	}
	
	/**
	 *
	 * @see \wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		$this->objectList->getConditionBuilder()->add("categoryID = ?", array($this->categoryID));
	}
	
	/**
	 *
	 * @see wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		$category = new TodoCategory(new Category($this->categoryID));
		
		if(!$category->categoryID)
			throw new IllegalLinkException();
		
		$this->title = $category->getTitle();
		
		// init category node list
		$this->categoryNodeList = new RestrictedTodoCategoryNodeList($this->categoryID);
		
		// users online
		if (MODULE_USERS_ONLINE) {
			// init users online list
			$this->usersOnlineList = new UsersOnlineList();
			$this->usersOnlineList->readStats();
			$this->usersOnlineList->checkRecord();
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
		
		WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('wcf.toDo.taskList'), LinkHandler::getInstance()->getLink('ToDoList', array())));
		
		DashboardHandler::getInstance()->loadBoxes('de.mysterycode.wcf.ToDoListPage', $this);
		
		WCF::getTPL()->assign( array(
			'id' => $this->categoryID,
			'title' => $this->title,
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.mysterycode.wcf.ToDoCategoryPage'),
			'sidebarName' => 'de.mysterycode.wcf.ToDoCategoryPage',
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo')),
			'categoryNodeList' => $this->categoryNodeList,
			'usersOnlineList' => $this->usersOnlineList
		));
	}
}
