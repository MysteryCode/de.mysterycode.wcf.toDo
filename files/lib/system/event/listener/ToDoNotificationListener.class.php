<?php
namespace wcf\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\system\WCF;

/**
 * Shows the toDo notification listener.
 *
 * @author	Florian Gail
 * @copyright	2013 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoNotificationListener implements IEventListener {
	
	public $count = 0;
	public $unsolved = 0;
	public $overdue = 0;
		
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$sql = "SELECT COUNT(*)
			FROM wcf" . WCF_N . "_todo,wcf" . WCF_N . "_todo_to_user
			WHERE wcf" . WCF_N . "_todo.id = wcf" . WCF_N . "_todo_to_user.toDoID
				AND wcf" . WCF_N . "_todo.status = 1
				AND wcf" . WCF_N . "_todo_to_user.userID = " . WCF::getUser()->userID;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$this->count = $statement->fetchArray();
		$this->unsolved = $this->count["COUNT(*)"];
		
		$sql = "SELECT COUNT(*)
			FROM wcf" . WCF_N . "_todo,wcf" . WCF_N . "_todo_to_user
			WHERE wcf" . WCF_N . "_todo.id = wcf" . WCF_N . "_todo_to_user.toDoID
				AND wcf" . WCF_N . "_todo.status != 3
				AND wcf" . WCF_N . "_todo.status != 4
				AND wcf" . WCF_N . "_todo.endTime < " . TIME_NOW . "
				AND wcf" . WCF_N . "_todo.endTime != 0
				AND wcf" . WCF_N . "_todo_to_user.userID = " . WCF::getUser()->userID;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$this->count = $statement->fetchArray();
		$this->overdue = $this->count["COUNT(*)"];
		
		WCF::getTPL()->assign(array(
			'unsolvedToDoCount' => $this->unsolved,
			'overdueToDoCount' => $this->overdue
		));
	}
}