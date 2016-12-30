<?php

namespace wcf\system\user\notification\object\type;

use wcf\system\WCF;

/**
 * Shows the todo todo user notification objecttype.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoUserNotificationObjectType extends AbstractUserNotificationObjectType {
	protected static $decoratorClassName = 'wcf\system\user\notification\object\ToDoUserNotificationObject';
	protected static $objectClassName = 'wcf\data\todo\ToDo';
	protected static $objectListClassName = 'wcf\data\todo\ToDoList';

	public function getOwnerID($objectID) {
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo todo
			WHERE todo.todoID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$objectID
		));
		$row = $statement->fetchArray();
		
		return ($row ? $row['submitter'] : 0);
	}
}
