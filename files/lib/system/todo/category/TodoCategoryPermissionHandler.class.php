<?php

namespace wcf\system\todo\category;
use wcf\system\cache\builder\TodoCategoryACLOptionCacheBuilder;
use wcf\system\category\CategoryPermissionHandler as WCFCategoryPermissionHandler;

/**
 * Handles the category permissions.
 *
 * @author	Florian Gail
 * @copyright	2014-2015 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenpflichtige Produkte <http://downloads.mysterycode.de/index.php/License/4-Kostenpflichtige-Produkte/>
 * @package	de.mysterycode.wcf.inventar
 * @category	INVENTAR
 */
class TodoCategoryPermissionHandler extends WCFCategoryPermissionHandler {
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->categoryPermissions = TodoCategoryACLOptionCacheBuilder::getInstance()->getData();
	}
	
	/**
	 * Resets the category permission cache.
	 */
	public function resetCache() {
		TodoCategoryACLOptionCacheBuilder::getInstance()->reset();
	}
}
