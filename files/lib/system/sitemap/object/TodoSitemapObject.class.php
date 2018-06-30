<?php

namespace wcf\system\sitemap\object;

use wcf\data\DatabaseObject;
use wcf\data\todo\ToDo;

/**
 * Represents the todo sitemap objects.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoSitemapObject extends AbstractSitemapObjectObjectType {
	/**
	 * @inheritDoc
	 */
	public function getObjectClass() {
		return ToDo::class;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getLastModifiedColumn() {
		return 'time';
	}
	
	/**
	 * @inheritDoc
	 * @param ToDo $object
	 */
	public function canView(DatabaseObject $object) {
		return $object->isVisible();
	}
}
