<?php

namespace wcf\data\todo\category;

/**
 * Represents a one dimensional list of category nodes.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryNodeList extends TodoCategoryNodeTree {
	/**
	 * @inheritDoc
	 */
	public function getIterator() {
		$iterator = parent::getIterator();

		// just loop through categories of the relative root level
		$iterator->setMaxDepth(0);

		return $iterator;
	}
}
