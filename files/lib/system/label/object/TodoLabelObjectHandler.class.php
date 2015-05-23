<?php

namespace wcf\system\label\object;
use wcf\data\todo\category\TodoCategoryCache;
use wcf\system\label\object\AbstractLabelObjectHandler;
use wcf\system\label\LabelHandler;

/**
 * Label handler for todos.
 * 
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class TodoLabelObjectHandler extends AbstractLabelObjectHandler {
	/**
	 * category id
	 * @var	integer
	 */
	protected $categoryID = 0;
	
	/**
	 * @see	\wcf\system\label\object\AbstractLabelObjectHandler::$objectType
	 */
	protected $objectType = 'de.mysterycode.wcf.toDo.toDo';
	
	/**
	 * Sets current category id and loads associated label groups.
	 * 
	 * @param	integer		$categoryID
	 */
	public function setCategoryID($categoryID) {
		$this->categoryID = $categoryID;
		
		// load label groups
		$groupIDs = TodoCategoryCache::getInstance()->getLabelGroups($this->categoryID);
		$this->labelGroups = (empty($groupIDs)) ? array() : LabelHandler::getInstance()->getLabelGroups($groupIDs);
	}
}