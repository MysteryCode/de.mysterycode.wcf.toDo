<?php

namespace wcf\data\todo\assigned\group;
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
 * @property-read integer groupID ID of the group which is assigned
 * @property-read integer todoID ID of the todo the group is assigned to
 * @property-read string  groupname groupname of the group who is assigned
 */
class AssignedGroup extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'todo_to_group';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'assignID';
}
