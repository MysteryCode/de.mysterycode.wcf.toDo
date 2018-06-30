<?php

namespace wcf\data\todo\status;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a todo status.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 *
 * @property-read integer statusID ID of this status-object
 * @property-read string  subject title of the status
 * @property-read string  description description of the status
 * @property-read integer showOrder show order of the category as positive integer
 * @property-read string  cssClass css classes for appearance
 * @property-read boolean locked this status can't be deleted
 */
class TodoStatus extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'todo_status';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'statusID';
	
	public function getTitle() {
		return WCF::getLanguage()->get($this->subject);
	}
}
