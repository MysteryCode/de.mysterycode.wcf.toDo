<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\category\TodoCategory;

/**
 * Caches the acl options of categories.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryACLOptionCacheBuilder extends CategoryACLOptionCacheBuilder {
	/**
	 * @inheritDoc
	 */
	public function rebuild(array $parameters) {
		$data = parent::rebuild($parameters);
		
		TodoCategory::inheritPermissions(0, $data);
		
		return $data;
	}
}
