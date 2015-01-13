<?php

namespace wcf\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Delete unused todo-categories.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class DeleteUnusedToDoCategoriesCronjob extends AbstractCronjob {
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		
		if (!TODO_DELETE_OBSOLETE_CATEGORIES)
			return;
		
		// read used categories
		$sql = "SELECT category
			FROM wcf" . WCF_N . "_todo
			GROUP BY category";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		$test = array();
		
		while ($row = $statement->fetchArray()) {
			$test[] = $row['category'];
		}
		
		// read all categories
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		$delete = array();
		while ($row = $statement->fetchArray()) {
			// check whether category is used
			if (!in_array($row['id'], $test)) {
				$delete[] = $row['id'];
			}
		}
		
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("id IN (?)", array($delete));
		$sql = "DELETE FROM wcf" . WCF_N . "_todo_category
				".$conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
	}
}
