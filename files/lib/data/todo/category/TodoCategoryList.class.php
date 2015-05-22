<?php

namespace wcf\data\todo\category;
use wcf\data\DatabaseObjectList;

/**
 * Represents the list of todo categories.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class TodoCategoryList extends DatabaseObjectList {
	/**
	 * @see \wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\todo\category\TodoCategory';
}
