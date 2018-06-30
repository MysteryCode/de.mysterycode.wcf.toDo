<?php

namespace wcf\system\moderation;
use wcf\data\todo\DeletedToDoList;

/**
 * Provides a list of trahed todos
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class DeletedToDoProvider implements IDeletedContentProvider {
	/**
	 * @inheritDoc
	 */
	public function getObjectList() {
		return new DeletedToDoList();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTemplateName() {
		return 'deletedTodoList';
	}
	
	/**
	 * @inheritDoc
	 */
	public function getApplication() {
		return 'wcf';
	}
}
