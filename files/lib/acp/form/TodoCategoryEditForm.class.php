<?php

namespace wcf\acp\form;


/**
 * Shows the todo category edit form.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
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
