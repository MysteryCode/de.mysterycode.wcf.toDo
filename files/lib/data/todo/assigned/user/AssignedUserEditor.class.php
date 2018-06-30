<?php

namespace wcf\data\todo\assigned\user;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\AssignCacheBuilder;

/**
 * Provides functions to edit todo status.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class AssignedUserEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = AssignedUser::class;
	
	/**
	 * @inheritDoc
	 */
	public static function resetCache() {
		AssignCacheBuilder::getInstance()->reset(['user']);
	}
}
