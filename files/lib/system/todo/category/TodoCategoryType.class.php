<?php

namespace wcf\system\todo\category;
use wcf\system\category\AbstractCategoryType;
use wcf\system\WCF;

/**
 * Category implementation.
 *
 * @author	Florian Gail
 * @copyright	2014-2015 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenpflichtige Produkte <http://downloads.mysterycode.de/index.php/License/4-Kostenpflichtige-Produkte/>
 * @package	de.mysterycode.wcf.inventar
 * @category	INVENTAR
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
