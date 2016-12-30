<?php

namespace wcf\system\todo\category;
use wcf\system\category\AbstractCategoryType;
use wcf\system\WCF;

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
	 * @inheritDoc
	 */
	protected $forceDescription = false;
	
	/**
	 * @inheritDoc
	 */
	protected $langVarPrefix = 'wcf.toDo.acp.category';
	
	/**
	 * @inheritDoc
	 */
	protected $permissionPrefix = 'admin.content.toDo.category';
	
	/**
	 * @inheritDoc
	 */
	protected $objectTypes = array('com.woltlab.wcf.acl' => 'de.mysterycode.wcf.toDo.category');
	
	/**
	 * @inheritDoc
	 */
	public function getApplication() {
		return 'wcf';
	}
}
