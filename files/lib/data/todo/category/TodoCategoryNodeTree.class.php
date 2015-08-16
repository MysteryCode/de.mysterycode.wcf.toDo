<?php

namespace wcf\data\todo\category;
use wcf\data\category\CategoryNodeTree;
use wcf\data\category\CategoryNode;


/**
 * Represents a tree of category nodes.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */
class TodoCategoryNodeTree extends CategoryNodeTree {
	/**
	 * @see	\wcf\data\category\CategoryNodeTree::$nodeClassName
	 */
	protected $nodeClassName = 'wcf\data\todo\category\TodoCategoryNode';

	/**
	 * Creates a new instance of CategoryNodeTree.
	 *
	 * @see		wcf\data\category\CategoryNode::__construct()
	 * @param	integer			$parentCategoryID
	 * @param	boolean			$includeDisabledCategories
	 * @param	array<integer>		$excludedCategoryIDs
	 */
	public function __construct($parentCategoryID = 0, $includeDisabledCategories = false, array $excludedCategoryIDs = array()) {
		parent::__construct('de.mysterycode.wcf.toDo', $parentCategoryID, $includeDisabledCategories, $excludedCategoryIDs);
	}

	/**
	 * @see	\wcf\data\category\CategoryNodeTree::buildTreeLevel()
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
