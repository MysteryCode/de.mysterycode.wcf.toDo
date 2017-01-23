<?php

namespace wcf\system\cache\builder;

use wcf\system\WCF;

/**
 *
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoGeneralStatsCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	protected function rebuild(array $parameters) {
		$data = array();

		$sql = "SELECT
				(
					SELECT COUNT(todoID)
					FROM wcf" . WCF_N . "_todo
				) AS todos,
				(
					SELECT COUNT(todoID)
					FROM wcf" . WCF_N . "_todo
					WHERE statusID <> 1
				) AS todosInProgress,
				(
					SELECT COUNT(todoID)
					FROM wcf" . WCF_N . "_todo
					WHERE statusID = 1
				) AS todosFinished";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchSingleRow();
			
		$data['todos'] = (empty($row['todos']) ? 0 : $row['todos']);
		$data['todosInProgress'] = (empty($row['todosInProgress']) ? 0 : $row['todosInProgress']);
		$data['todosFinished'] = (empty($row['todosFinished']) ? 0 : $row['todosFinished']);
		
		return $data;
	}
}
