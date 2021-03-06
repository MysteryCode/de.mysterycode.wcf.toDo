<?php

namespace wcf\page;
use wcf\data\category\Category;
use wcf\data\todo\category\RestrictedTodoCategoryNodeList;
use wcf\data\todo\category\TodoCategory;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the todo category page.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryPage extends AbstractTodoListPage {
	/**
	 * category id
	 * @var integer
	 */
	public $categoryID = 0;

	/**
	 * @var \wcf\data\todo\category\TodoCategory
	 */
	public $category = null;
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if(isset($_REQUEST['id'])) $this->categoryID = intval($_REQUEST['id']);
	}
	
	/**
	 * @inheritDoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		$this->objectList->getConditionBuilder()->add("categoryID = ?", [$this->categoryID]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		
		$this->category = new TodoCategory(new Category($this->categoryID));
		
		if(!$this->category->categoryID)
			throw new IllegalLinkException();
		
		// init category node list
		$this->categoryNodeList = new RestrictedTodoCategoryNodeList($this->categoryID);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign( [
			'id' => $this->categoryID,
			'title' => $this->category->getTitle(),
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo')),
			'category' => $this->category
		]);
	}
}
