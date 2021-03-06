<?php

namespace wcf\data\todo\assigned\user;
use wcf\data\DatabaseObject;

/**
 * Represents a todo status.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 *
 * @property-read integer assignID ID of this assign-object
 * @property-read integer userID ID of the user who is assigned
 * @property-read integer todoID ID of the todo the user is assigned to
 * @property-read string  username username of the user who is assigned
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
