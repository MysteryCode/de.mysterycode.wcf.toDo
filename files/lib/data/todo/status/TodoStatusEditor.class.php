<?php

namespace wcf\data\todo\status;
use wcf\system\cache\builder\TodoStatusCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Provides functions to edit todo status.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoStatusEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = 'wcf\data\todo\status\TodoStatus';
	
	/**
	 * @inheritDoc
	 */
	public static function resetCache() {
		TodoStatusCacheBuilder::getInstance()->reset();
	}
}
