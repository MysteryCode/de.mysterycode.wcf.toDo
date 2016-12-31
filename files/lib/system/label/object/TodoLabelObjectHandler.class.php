<?php

namespace wcf\system\label\object;
use wcf\data\todo\category\TodoCategoryCache;
use wcf\system\label\LabelHandler;

/**
 * Label handler for todos.
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoLabelObjectHandler extends AbstractLabelObjectHandler {
	/**
	 * category id
	 * @var	integer
	 */
	protected $categoryID = 0;
	
	/**
	 * @inheritDoc
	 */
	protected $objectType = 'de.mysterycode.wcf.toDo.toDo';

	/**
	 * Sets current category id and loads associated label groups.
	 *
	 * @param    integer $categoryID
	 * @throws \wcf\system\exception\SystemException
	 */
	public function setCategoryID($categoryID) {
		$this->categoryID = $categoryID;
		
		// load label groups
		$groupIDs = TodoCategoryCache::getInstance()->getLabelGroups($this->categoryID);
		$this->labelGroups = (empty($groupIDs)) ? array() : LabelHandler::getInstance()->getLabelGroups($groupIDs);
	}
}
