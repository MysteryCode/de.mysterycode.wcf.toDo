<?php

namespace wcf\data\todo\status;
use wcf\system\cache\builder\TodoStatusCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Provides functions to edit todo status.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoStatusEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = TodoStatus::class;
	
	/**
	 * @inheritDoc
	 */
	public static function resetCache() {
		TodoStatusCacheBuilder::getInstance()->reset();
	}
}
