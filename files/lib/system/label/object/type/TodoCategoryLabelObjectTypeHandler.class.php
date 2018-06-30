<?php

namespace wcf\system\label\object\type;
use wcf\data\todo\category\TodoCategoryEditor;
use wcf\data\todo\category\TodoCategoryNodeTree;

/**
 * Object type handler for categories.
 * 
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryLabelObjectTypeHandler extends AbstractLabelObjectTypeHandler {
	/**
	 * category node list
	 * @var	\wcf\data\todo\category\TodoCategoryNodeTree
	 */
	public $categoryNodeTree = null;
	
	/**
	 * object type id
	 * @var	integer
	 */
	public $objectTypeID = 0;
	
	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->categoryNodeTree = new TodoCategoryNodeTree();
	}
	
	/**
	 * @inheritDoc
	 */
	public function setObjectTypeID($objectTypeID) {
		parent::setObjectTypeID($objectTypeID);
		
		// build label object type container
		$this->container = new LabelObjectTypeContainer($this->objectTypeID);
		
		foreach ($this->categoryNodeTree->getIterator() as $node) {
			$objectType = new LabelObjectType($node->getTitle(), $node->categoryID, ($node->getDepth() - 1));
			$this->container->add($objectType);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		TodoCategoryEditor::resetCache();
	}
}
