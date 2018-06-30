<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\status\TodoStatusList;

/**
 *
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoStatusCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$data = [
			'status' => []
		];
		
		$statusList = new TodoStatusList();
		$statusList->sqlOrderBy = 'todo_status.statusID ASC';
		$statusList->readObjects();
		$data['status'] = $statusList->getObjects();
		
		return $data;
	}
}
