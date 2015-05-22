<?php

namespace wcf\data\todo\status;
use wcf\data\DatabaseObjectList;

/**
 * Represents the list of todo status.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class TodoStatusList extends DatabaseObjectList {
	/**
	 * @see \wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\todo\status\TodoStatus';
}
