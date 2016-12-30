<?php

namespace wcf\system\todo\category;
use wcf\system\category\AbstractCategoryType;

/**
 * Category implementation.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryType extends AbstractCategoryType {
	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$forceDescription
	 */
	protected $forceDescription = false;
	
	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$langVarPrefix
	 */
	protected $langVarPrefix = 'wcf.toDo.acp.category';
	
	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$permissionPrefix
	 */
	protected $permissionPrefix = 'admin.content.toDo.category';
	
	/**
	 * @see	\wcf\system\category\AbstractCategoryType::$objectTypes
	 */
	protected $objectTypes = array('com.woltlab.wcf.acl' => 'de.mysterycode.wcf.toDo.category');
	
	/**
	 * @see	\wcf\system\category\ICategoryType::getApplication()
	 */
	public function getApplication() {
		return 'wcf';
	}
}
