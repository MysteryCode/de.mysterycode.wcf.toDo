<?php

namespace wcf\system\cache\builder;
use wcf\data\user\UserList;

/**
 *
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoUserCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$data = [
			'users' => []
		];
		
		$statusList = new UserList();
		$statusList->sqlOrderBy = 'user_table.userID ASC';
		$statusList->readObjects();
		$data['users'] = $statusList->getObjects();
		
		return $data;
	}
}
