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
class TodoCategoryListPage extends AbstractCategoryListPage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.toDoCategory.list';
	
	/**
	 * @inheritDoc
	 */
	public $objectTypeName = 'de.mysterycode.wcf.toDo';
	
	/**
	 * @inheritDoc
	 */
	public $pageTitle = 'wcf.acp.todo.category.list';
}
