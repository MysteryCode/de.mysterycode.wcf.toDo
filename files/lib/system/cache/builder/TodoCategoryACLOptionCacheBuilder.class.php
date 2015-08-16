<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\category\TodoCategory;
use wcf\system\cache\builder\CategoryACLOptionCacheBuilder;

/**
 * Caches the acl options of categories.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
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
