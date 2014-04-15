<?php

namespace wcf\system\user\notification\object\type;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;
use wcf\system\WCF;

/**
 * Shows the todo todo user notification objecttype.
 *
 * @author Florian Gail
 * @copyright 2013 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoUserNotificationObjectType extends AbstractUserNotificationObjectType {
	protected static $decoratorClassName = 'wcf\system\user\notification\object\ToDoUserNotificationObject';
	protected static $objectClassName = 'wcf\data\todo\ToDo';
	protected static $objectListClassName = 'wcf\data\todo\ToDoList';
	public function getOwnerID($objectID) {
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo todo
			WHERE todo.id = ?";
		$statement = WCF::getDB()->prepareStatement( $sql );
		$statement->execute( array(
			$objectID 
		) );
		$row = $statement->fetchArray();
		
		return($row ? $row['submitter'] : 0);
	}
}