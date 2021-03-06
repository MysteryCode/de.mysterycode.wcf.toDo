<?php

namespace wcf\system\cache\builder;

use wcf\system\WCF;

/**
 *
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoGeneralStatsCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$data = [];
		
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
