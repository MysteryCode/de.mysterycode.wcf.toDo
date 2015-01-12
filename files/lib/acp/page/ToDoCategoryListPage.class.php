<?php
namespace wcf\acp\page;
use wcf\acp\page\AbstractCategoryListPage;

/**
 * Shows the todo category list page.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */
class ToDoCategoryListPage extends AbstractCategoryListPage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.toDoCategory.list';
	
	/**
	 * @see	\wcf\acp\page\AbstractCategoryListPage::$objectTypeName
	 */
	public $objectTypeName = 'de.mysterycode.wcf.toDo';
	
	/**
	 * @see	\wcf\acp\page\AbstractCategoryListPage::$pageTitle
	 */
	public $pageTitle = 'wcf.acp.todo.category.list';
}
