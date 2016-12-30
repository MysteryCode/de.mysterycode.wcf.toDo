<?php

namespace wcf\data\todo\assigned\user;
use wcf\data\DatabaseObjectList;

/**
 * Represents the list of todo status.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class AssignedUserList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = 'wcf\data\todo\assigned\user\AssignedUser';
}
