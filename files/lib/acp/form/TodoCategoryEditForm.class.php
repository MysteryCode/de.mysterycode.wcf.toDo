<?php

namespace wcf\acp\form;
use wcf\acp\form\AbstractCategoryEditForm;
use wcf\data\todo\category\TodoCategory;

/**
 * Shows the todo category edit form.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */
class TodoCategoryEditForm extends AbstractCategoryEditForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.toDoCategory';
	
	/**
	 * @see	\wcf\acp\page\AbstractCategoryListPage::$objectTypeName
	 */
	public $objectTypeName = 'de.mysterycode.wcf.toDo';
	
	/**
	 * @see	\wcf\acp\page\AbstractCategoryListPage::$pageTitle
	 */
	public $pageTitle = 'wcf.acp.todo.category.edit';
}
