<?php

namespace wcf\data\todo\assigned\user;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\AssignCacheBuilder;

/**
 * Provides functions to edit todo status.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class AssignedUserEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @see \wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\todo\assigned\user\AssignedUser';
	
	/**
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetCache() {
		AssignCacheBuilder::getInstance()->reset(array('user'));
	}
}
