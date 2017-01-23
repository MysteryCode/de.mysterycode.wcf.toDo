<?php

namespace wcf\data\todo\category;
use wcf\data\category\CategoryNode;

/**
 * Represents a restricted todo-category node list.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class RestrictedTodoCategoryNodeList extends TodoCategoryNodeList {
	/**
	 * @inheritDoc
	 */
	protected function isIncluded(CategoryNode $categoryNode) {
		return (parent::isIncluded($categoryNode) && $categoryNode->getPermission('user.canViewCategory'));
	}
}
