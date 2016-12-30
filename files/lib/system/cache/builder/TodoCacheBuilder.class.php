<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\ToDoList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 *
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'todos' => array()
		);
		
		$statusList = new ToDoList();
		$statusList->sqlOrderBy = 'todo_table.todoID ASC';
		$statusList->readObjects();
		$data['todos'] = $statusList->getObjects();
		
		return $data;
	}
}
