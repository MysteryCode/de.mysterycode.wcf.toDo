<?php
namespace wcf\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\data\todo\ToDo;
use wcf\data\user\notification\event\UserNotificationEventList;
use wcf\data\user\notification\UserNotificationList;
use wcf\data\user\UserEditor;
use wcf\data\user\UserList;
use wcf\data\user\UserProfile;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\user\notification\object\ToDoUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Creates notifications to remind of todos.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoRememberNotificationCronjob extends AbstractCronjob {
	public function execute(Cronjob $cronjob) {
		$check = TIME_NOW;
		
		$todoList = new ToDoList();
		$todoList->getConditionBuilder()->add('todo_table.remembertime > ?', array(0));
		$todoList->getConditionBuilder()->add('todo_table.remembertime <= ?', array($check));
		$todoList->readObjects();
		$todos = $todoList->getObjects();
		
		foreach($todos as $todo) {
			UserNotificationHandler::getInstance()->fireEvent('remember', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todo->id)), $todo->getResponsibleIDs());
			$todoIDs[] = $todo->id;
		}
		
		$sql = "UPDATE wcf" . WCF_N . "_todo
			SET remembertime = ?
			WHERE id IN ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(0, $todoIDs));
	}
}