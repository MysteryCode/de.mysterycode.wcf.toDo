<?php
namespace wcf\acp\page;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the toDoCategoryList page.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoCategoryListPage extends AbstractPage {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.toDoCategory.list';
	
	public $categoryList = array();

	/**
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category
			ORDER BY title ASC";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			$this->categoryList[] = array(
				'id' => $row['id'],
				'title' => $row['title'],
				'color' => $row['color']
			);
		}
		
		WCF::getTPL()->assign(array(
			'categoryList' => $this->categoryList
		));
	}
}