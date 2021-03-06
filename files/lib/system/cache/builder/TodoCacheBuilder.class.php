<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\ToDoList;

/**
 *
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$data = [
			'todos' => []
		];
		
		$statusList = new ToDoList();
		$statusList->sqlOrderBy = 'todo_table.todoID ASC';
		$statusList->readObjects();
		$data['todos'] = $statusList->getObjects();
		
		return $data;
	}
}
