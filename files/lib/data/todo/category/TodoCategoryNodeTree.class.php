<?php

namespace wcf\data\todo\category;
use wcf\data\category\CategoryNode;
use wcf\data\category\CategoryNodeTree;

/**
 * Represents a tree of category nodes.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryNodeTree extends CategoryNodeTree {
	/**
	 * @inheritDoc
	 */
	protected $nodeClassName = TodoCategoryNode::class;

	/**
	 * Creates a new instance of CategoryNodeTree.
	 *
	 * @inheritDoc
	 * @param	integer			$parentCategoryID
	 * @param	boolean			$includeDisabledCategories
	 * @param	array<integer>		$excludedCategoryIDs
	 */
	public function __construct($parentCategoryID = 0, $includeDisabledCategories = false, array $excludedCategoryIDs = []) {
		parent::__construct('de.mysterycode.wcf.toDo', $parentCategoryID, $includeDisabledCategories, $excludedCategoryIDs);
	}

	/**
	 * @inheritDoc
	 */
	protected function buildTreeLevel(CategoryNode $parentNode, $depth = 0) {
		if ($this->maxDepth != -1 && $depth < 0) {
			return;
		}

		foreach ($this->getChildCategories($parentNode) as $childCategory) {
			$childNode = $this->getNode($childCategory->categoryID);
			$childNode->setDepth(($parentNode->getDepth() + 1));

			if ($this->isIncluded($childNode)) {
				$parentNode->addChild($childNode);

				// build next level
				$this->buildTreeLevel($childNode);
			}
		}
	}
}
