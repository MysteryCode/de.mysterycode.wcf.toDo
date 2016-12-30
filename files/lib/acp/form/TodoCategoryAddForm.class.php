<?php

namespace wcf\acp\form;
use wcf\acp\form\AbstractCategoryAddForm;
use wcf\data\todo\category\TodoCategory;

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
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.toDoCategory.add';
	
	/**
	 * @inheritDoc
	 */
	public $objectTypeName = 'de.mysterycode.wcf.toDo';
	
	/**
	 * @inheritDoc
	 */
	public $pageTitle = 'wcf.acp.todo.category.add';
}
