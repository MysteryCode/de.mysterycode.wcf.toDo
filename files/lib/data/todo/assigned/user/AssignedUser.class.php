<?php

namespace wcf\data\todo\assigned\user;
use wcf\data\DatabaseObject;

/**
 * Represents a todo status.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class AssignedUser extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'todo_to_user';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'assignID';
}
