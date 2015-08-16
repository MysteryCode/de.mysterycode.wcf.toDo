<?php

namespace wcf\data\todo\category;
use wcf\data\category\CategoryNode;

/**
 * Represents a restricted tree of category nodes.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */
class RestrictedTodoCategoryNodeTree extends TodoCategoryNodeTree {
	/**
	 * @see	\wcf\data\category\CategoryNodeTree::isIncluded()
	 */
	protected function isIncluded(CategoryNode $categoryNode) {
		return (parent::isIncluded($categoryNode) && $categoryNode->getPermission());
	}
}
