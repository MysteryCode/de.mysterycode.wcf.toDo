<?php

namespace wcf\system\label\object\type;
use wcf\data\todo\category\TodoCategoryEditor;
use wcf\data\todo\category\TodoCategoryNodeTree;

/**
 * Object type handler for categories.
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryLabelObjectTypeHandler extends AbstractLabelObjectTypeHandler {
	/**
	 * category node list
	 * @var	\wcf\data\category\AbstractCategoryNodeList
	 */
	public $categoryNodeTree = null;
	
	/**
	 * object type id
	 * @var	integer
	 */
	public $objectTypeID = 0;
	
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->categoryNodeTree = new TodoCategoryNodeTree();
	}
	
	/**
	 * @see	\wcf\system\label\object\type\AbstractLabelObjectTypeHandler::setObjectTypeID()
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
	 * @see	\wcf\system\label\object\type\ILabelObjectTypeHandler::save()
	 */
	public function save() {
		TodoCategoryEditor::resetCache();
	}
}
