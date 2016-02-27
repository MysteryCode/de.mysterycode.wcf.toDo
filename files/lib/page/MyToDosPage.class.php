<?php

namespace wcf\page;
use wcf\data\todo\ToDo;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;

/**
 * Shows a list of own todos.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class MyToDosPage extends AbstractToDoListPage {
	/**
	 * @see \wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		//$this->objectList->getConditionBuilder()->add("(submitter = ? OR user_assigns.userID = ?)", array(WCF::getUser()->userID, WCF::getUser()->userID));
	}
	
	/**
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('wcf.toDo.taskList'), LinkHandler::getInstance()->getLink('ToDoList', array())));
		
		// do not want to create a new objecttype
		DashboardHandler::getInstance()->loadBoxes('de.mysterycode.wcf.ToDoListPage', $this);
		
		WCF::getTPL()->assign(array(
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.mysterycode.wcf.MyToDosPage'),
			'sidebarName' => 'de.mysterycode.wcf.MyToDosPage',
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo')),
		));
	}
}
