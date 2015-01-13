<?php

namespace wcf\acp\action;
use wcf\action\AbstractAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows the toDoCategoryDelete action.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoCategoryDeleteAction extends AbstractAction {
	
	public $neededPermissions = array('admin.content.toDo.category.canDelete');
	
	public $categoryID = 0;
	
	/**
	 * @see	wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $this->categoryID = intval($_REQUEST['id']);
	}
	
	public function execute() {
		if ($this->categoryID == 0)
			throw new IllegalLinkException();
		
		if ($this->categoryID != 0) {
			$sql = "DELETE FROM wcf" . WCF_N . "_todo_category
				WHERE id = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->categoryID));
			
			$sql = "UPDATE wcf" . WCF_N . "_todo
				SET category = ?
				WHERE category = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(0, $this->categoryID));
		}
		
		$this->executed();
		
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('ToDoCategoryList', array()));
		exit;
	}
}