<?php

namespace wcf\acp\form;
use wcf\acp\form\AbstractCategoryEditForm;
use wcf\data\todo\category\TodoCategory;

/**
 * Shows the todo category edit form.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryEditForm extends AbstractCategoryEditForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.toDoCategory';
	
	/**
	 * @inheritDoc
	 */
	public $objectTypeName = 'de.mysterycode.wcf.toDo';
	
	/**
	 * @inheritDoc
	 */
	public $pageTitle = 'wcf.acp.todo.category.edit';
}
