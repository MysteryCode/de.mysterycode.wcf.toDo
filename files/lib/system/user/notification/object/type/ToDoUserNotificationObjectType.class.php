<?php

namespace wcf\system\user\notification\object\type;

use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoList;
use wcf\system\user\notification\object\ToDoUserNotificationObject;
use wcf\system\WCF;

/**
 * Shows the todo todo user notification objecttype.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoUserNotificationObjectType extends AbstractUserNotificationObjectType {
	protected static $decoratorClassName = ToDoUserNotificationObject::class;
	protected static $objectClassName = ToDo::class;
	protected static $objectListClassName = ToDoList::class;

	public function getOwnerID($objectID) {
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo todo
			WHERE todo.todoID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([
			$objectID
		]);
		$row = $statement->fetchArray();
		
		return ($row ? $row['submitter'] : 0);
	}
}
