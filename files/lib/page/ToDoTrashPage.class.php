<?php

namespace wcf\page;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\WCF;

/**
 * Shows the todo trash page.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoTrashPage extends AbstractToDoListPage {
	/**
	 * @inheritDoc
	 */
	protected function initObjectList() {
		parent::initObjectList ();
		
		$this->objectList->getConditionBuilder()->add("statusID = ?", array(4));
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo'))
		));
	}
}
