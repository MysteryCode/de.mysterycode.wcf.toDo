<?php

namespace wcf\page;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;

/**
 * Shows the todo archive page.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoArchivePage extends AbstractToDoListPage {
	/**
	 * @inheritDoc
	 */
	protected function initObjectList() {
		parent::initObjectList ();
		
		$this->objectList->getConditionBuilder()->add("statusID = ?", array(1));
	}
	
	/**
	 * @inheritDoc
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
