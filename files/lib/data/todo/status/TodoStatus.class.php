<?php

namespace wcf\data\todo\status;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a todo status.
 *
 * @author	Florian Gail
 * @copyright	2014-2015 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class TodoStatus extends DatabaseObject {
	/**
	 * @see \wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'todo_status';
	
	/**
	 * @see \wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'statusID';
	
	public function getTitle() {
		return WCF::getLanguage()->get($this->subject);
	}
}
