<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\TodoList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 *
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenpflichtige Produkte <http://downloads.mysterycode.de/index.php/License/4-Kostenpflichtige-Produkte/>
 * @contact	de.mysterycode.inventar
 * @category 	inventar
 */
class TodoCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'todos' => array()
		);
		
		$statusList = new TodoList();
		$statusList->sqlOrderBy = 'todo_table.todoID ASC';
		$statusList->readObjects();
		$data['todos'] = $statusList->getObjects();
		
		return $data;
	}
}
