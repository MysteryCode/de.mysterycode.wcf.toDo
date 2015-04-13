<?php

namespace wcf\system\label\object\type;
use wcf\data\category\AbstractCategoryNodeList;
use wcf\data\todo\category\TodoCategoryEditor;
use wcf\system\label\object\type\AbstractLabelObjectTypeHandler;
use wcf\system\label\object\type\LabelObjectType;
use wcf\system\label\object\type\LabelObjectTypeContainer;

/**
 * Object type handler for categories.
 * 
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class TodoCategoryLabelObjectTypeHandler extends AbstractLabelObjectTypeHandler {
	/**
	 * category node list
	 * @var	\wcf\data\category\AbstractCategoryNodeList
	 */
	public $categoryNodeList = null;
	
	/**
	 * object type id
	 * @var	integer
	 */
	public $objectTypeID = 0;
	
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->categoryNodeList = new AbstractCategoryNodeList();
		$this->categoryNodeList->readNodeTree();
	}
	
	/**
	 * @see	\wcf\system\label\object\type\AbstractLabelObjectTypeHandler::setObjectTypeID()
	 */
	public function setObjectTypeID($objectTypeID) {
		parent::setObjectTypeID($objectTypeID);
		
		// build label object type container
		$this->container = new LabelObjectTypeContainer($this->objectTypeID);
		
		foreach ($this->categoryNodeList->getNodeList() as $node) {
			$objectType = new LabelObjectType($node->getCategory()->getTitle(), $node->getCategory()->categoryID, ($node->getDepth() - 1), $node->getCategory()->isCategory());
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
