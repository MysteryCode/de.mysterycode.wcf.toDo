<?php

namespace wcf\data\todo\status;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a todo status.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
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
