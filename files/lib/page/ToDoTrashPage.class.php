<?php

namespace wcf\page;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;

/**
 * Shows the todo trash page.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoTrashPage extends AbstractToDoListPage {
	/**
	 * @see \wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		parent::initObjectList ();
		
		$this->objectList->getConditionBuilder()->add("statusID = ?", array(4));
	}
	
	/**
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// load same boxes as @ ToDoArchivePage
		// dont want to add a new sidebarName.. lazy..
		DashboardHandler::getInstance()->loadBoxes('de.mysterycode.wcf.ToDoArchivePage', $this);
		
		WCF::getTPL()->assign(array(
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo')),
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.mysterycode.wcf.ToDoTrashPage'),
			'sidebarName' => 'de.mysterycode.wcf.ToDoArchivePage'
		));
	}
}
