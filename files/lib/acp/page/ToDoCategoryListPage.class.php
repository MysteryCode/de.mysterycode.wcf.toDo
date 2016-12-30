<?php
namespace wcf\acp\page;


/**
 * Shows the todo category list page.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
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
