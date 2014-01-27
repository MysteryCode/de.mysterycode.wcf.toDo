<?php
namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\user\User;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Shows the toDo no responsible dashboardbox.
 *
 * @author	Florian Gail
 * @copyright	2013 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoNoResponsibleDashboardBox extends AbstractSidebarDashboardBox {
	
	public $toDoList = array();
	
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo
			WHERE ( private = 0 OR submitter = " . WCF::getUser()->userID . " )
				AND status != 4
				AND status != 3
			ORDER BY endTime DESC
			LIMIT 5";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		while($stat = $statement->fetchArray()) {
			
			$sqlCheck = "SELECT toDoID
				FROM wcf" . WCF_N . "_todo_to_user
				WHERE toDoID = " .$stat['id'];
			$check = WCF::getDB()->prepareStatement($sqlCheck);
			$check->execute();
			
			if(!$check->fetchArray()) {
				$user = new User($stat['submitter']);
				$this->toDoList[] = array(
					'id' => $stat['id'],
					'title' => $stat['title'],
					'submitter' => $stat['submitter'],
					'username' => $user->username,
					'timestamp' => $stat['timestamp']
				);
			}
		}
		
		WCF::getTPL()->assign(array(
			'toDoList' => $this->toDoList
		));
	}
	
	protected function render() {
		return WCF::getTPL()->fetch('dashboardBoxToDoNoResponsible', 'wcf');
	}
}