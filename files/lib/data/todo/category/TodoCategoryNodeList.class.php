<?php

namespace wcf\data\todo\category;

/**
 * Represents a one dimensional list of category nodes.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryNodeList extends TodoCategoryNodeTree {
	/**
	 * @see	\wcf\data\category\CategoryNodeTree::getIterator()
	 */
	public function getIterator() {
		$iterator = parent::getIterator();

		// just loop through categories of the relative root level
		$iterator->setMaxDepth(0);

		return $iterator;
	}
}
