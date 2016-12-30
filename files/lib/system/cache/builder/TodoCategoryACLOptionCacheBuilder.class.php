<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\category\TodoCategory;

/**
 * Caches the acl options of categories.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryACLOptionCacheBuilder extends CategoryACLOptionCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		$data = parent::rebuild($parameters);
		
		TodoCategory::inheritPermissions(0, $data);
		
		return $data;
	}
}
