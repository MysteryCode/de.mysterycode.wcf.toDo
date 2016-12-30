<?php

namespace wcf\acp\form;


/**
 * Shows the todo category add form.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryAddForm extends AbstractCategoryAddForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.toDoCategory.add';
	
	/**
	 * @see	\wcf\acp\page\AbstractCategoryListPage::$objectTypeName
	 */
	public $objectTypeName = 'de.mysterycode.wcf.toDo';
	
	/**
	 * @see	\wcf\acp\page\AbstractCategoryListPage::$pageTitle
	 */
	public $pageTitle = 'wcf.acp.todo.category.add';
}
