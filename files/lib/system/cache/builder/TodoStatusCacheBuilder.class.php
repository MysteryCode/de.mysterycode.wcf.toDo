<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\status\TodoStatusList;

/**
 *
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
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
