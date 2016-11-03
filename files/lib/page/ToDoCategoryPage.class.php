<?php

namespace wcf\page;
use wcf\data\category\Category;
use wcf\data\todo\category\RestrictedTodoCategoryNodeList;
use wcf\data\todo\category\TodoCategory;
use wcf\data\todo\ToDo;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;

/**
 * Shows the todo category page.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoCategoryPage extends AbstractToDoListPage {
	public $categoryID = 0;
	
	public $category = null;
	
	/**
	 * category node list
	 * @var	\wcf\data\todo\category\RestrictedTodoCategoryNodeList
	 */
	public $categoryNodeList = null;
	
	/**
	 * @see wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if(isset($_REQUEST['id'])) $this->categoryID = intval($_REQUEST['id']);
	}
	
	/**
	 * @see \wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		$this->objectList->getConditionBuilder()->add("categoryID = ?", array($this->categoryID));
	}
	
	/**
	 * @see wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		$this->category = new TodoCategory(new Category($this->categoryID));
		
		if(!$this->category->categoryID)
			throw new IllegalLinkException();
		
		$this->title = $this->category->getTitle();
		
		// init category node list
		$this->categoryNodeList = new RestrictedTodoCategoryNodeList($this->categoryID);
	}
	
	/**
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
			'category' => $this->category
		));
	}
}
