<?php

namespace wcf\acp\form;


/**
 * Shows the todo category add form.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
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
