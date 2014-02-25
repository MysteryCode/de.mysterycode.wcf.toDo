<?php
namespace wcf\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the toDo delete unused category cronjob.
 *
 * @author	Florian Gail
 * @copyright	2013 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class DeleteUnusedToDoCategoriesCronjob extends AbstractCronjob {
	
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		
		// read used categories
		$sql = "SELECT category
			FROM wcf" . WCF_N . "_todo
			GROUP BY category";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		$test = array();
		
		while($row = $statement->fetchArray()) {
			$test[] = $row['category'];
		}
		
		// read all categories
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		while($row = $statement->fetchArray()) {
			// check whether category is used
			if(!in_array($row['id'], $test)) {
				// if not used delete the category
				$sql = "DELETE FROM wcf" . WCF_N . "_todo_category
					WHERE id = " . $row['id'];
				$statementDelete = WCF::getDB()->prepareStatement($sql);
				$statementDelete->execute();
			}
		}
	}
}