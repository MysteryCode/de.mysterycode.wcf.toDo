<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\status\TodoStatusList;
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
class TodoStatusCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'status' => array()
		);
		
		$statusList = new TodoStatusList();
		$statusList->sqlOrderBy = 'todo_status.statusID ASC';
		$statusList->readObjects();
		$data['status'] = $statusList->getObjects();
		
		return $data;
	}
}
