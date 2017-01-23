<?php

namespace wcf\data\todo\assigned\group;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\AssignCacheBuilder;

/**
 * Provides functions to edit todo status.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class AssignedGroupEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = 'wcf\data\todo\assigned\group\AssignedGroup';
	
	/**
	 * @inheritDoc
	 */
	public static function resetCache() {
		AssignCacheBuilder::getInstance()->reset(['group']);
	}
}
