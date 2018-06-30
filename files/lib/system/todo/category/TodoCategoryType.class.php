<?php

namespace wcf\system\todo\category;
use wcf\system\category\AbstractCategoryType;

/**
 * Category implementation.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
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
	protected $objectTypes = ['com.woltlab.wcf.acl' => 'de.mysterycode.wcf.toDo.category'];
	
	/**
	 * @inheritDoc
	 */
	public function getApplication() {
		return 'wcf';
	}
}
