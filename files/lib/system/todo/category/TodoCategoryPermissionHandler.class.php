<?php

namespace wcf\system\todo\category;
use wcf\system\cache\builder\TodoCategoryACLOptionCacheBuilder;
use wcf\system\category\CategoryPermissionHandler as WCFCategoryPermissionHandler;

/**
 * Handles the category permissions.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryPermissionHandler extends WCFCategoryPermissionHandler {
	/**
	 * @inheritDoc
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
