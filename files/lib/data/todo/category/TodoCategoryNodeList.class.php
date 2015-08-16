<?php

namespace wcf\data\todo\category;

/**
 * Represents a one dimensional list of category nodes.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
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
