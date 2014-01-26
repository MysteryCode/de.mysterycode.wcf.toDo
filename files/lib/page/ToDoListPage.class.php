<?php
namespace wcf\page;
use wcf\data\user\User;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the toDoList page.
 *
 * @author	Florian Gail
 * @copyright	2013 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoListPage extends AbstractPage {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.header.menu.toDo';
	
	public $neededPermissions = array('user.toDo.toDo.canViewList');
	
	public $tasks = array();
	public $categoryList = array();

	/**
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		$this->categoryList[0] = array(
				'id' => 0,
				'title' => WCF::getLanguage()->get('wcf.toDo.category.notAvailable'),
				'color' => 'gray'
		);
		
		while ($row = $statement->fetchArray()) {
			$this->categoryList[$row['id']] = array(
				'id' => $row['id'],
				'title' => $row['title'],
				'color' => $row['color']
			);
		}
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo
			WHERE private = 0
				OR submitter = " . WCF::getUser()->userID . "
			ORDER BY status ASC";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		$a = 0;
		while ($row = $statement->fetchArray()) {
			
			$this->tasks[] = array(
				'id' => $row['id'],
				'title' => $row['title'],
				'status' => $row['status'],
				'responsible' => $this->getResponsible($row['id']),
				'submitTime' => $row['timestamp'],
				'endTime' => $row['endTime'],
				'category' => $row['category'],
				'categoryname' => $this->categoryList[$row['category']]['title'],
				'categorycolor' => $this->categoryList[$row['category']]['color'],
				'important' => $row['important'],
				'private' => $row['private']
			);
			$a++;
		}

		WCF::getTPL()->assign(array(
			'tasks' => $this->tasks,
			'entryCount' => $a,
			'allowSpidersToIndexThisPage' => false
		));
	}
	
	public function getResponsible($taskID) {
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_to_user
			WHERE toDoID = " . $taskID . "
			ORDER BY userID ASC";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		
		$this->responsibles = array();
		$i = 1;
		while($responsible = $statement->fetchArray()) {
			$user = new User($responsible["userID"]);
			$this->responsibles[$i] = array(
				'id' => $responsible["userID"],
				'username' => $user->username
			);
			$i++;
		}
		
		return $this->responsibles;
	}
}