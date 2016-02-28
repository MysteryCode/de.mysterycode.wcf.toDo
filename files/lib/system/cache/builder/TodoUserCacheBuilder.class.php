<?php

namespace wcf\system\cache\builder;
use wcf\data\user\UserList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 *
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenpflichtige Produkte <http://downloads.mysterycode.de/index.php/License/4-Kostenpflichtige-Produkte/>
 * @contact	de.mysterycode.inventar
 * @category 	inventar
 */
class TodoUserCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'users' => array()
		);
		
		$statusList = new UserList();
		$statusList->sqlOrderBy = 'user_table.userID ASC';
		$statusList->readObjects();
		$data['users'] = $statusList->getObjects();
		
		return $data;
	}
}
