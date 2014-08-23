<?php
namespace wcf\data\todo;
use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

/**
 * Represents the list of todo categories.
 *
 * @author	Daniel Nold	
 * @copyright	2014 Daniel Nold
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoCategoryList extends DatabaseObjectList {
	/**
	 *
	 * @see \wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\todo\ToDoCategory';
}