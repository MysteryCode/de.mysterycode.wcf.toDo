<?php

namespace wcf\system\user\notification\object;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\user\notification\object\IUserNotificationObject;

/**
 * Represents the todo user notification object.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoUserNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject {
	/**
	 * @see \wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\todo\ToDo';
	
	/**
	 * @see \wcf\system\user\notification\object\IUserNotificationObject::getObjectID()
	 */
	public function getObjectID() {
		return $this->todoID;
	}
	
	/**
	 * @see \wcf\system\user\notification\object\IUserNotificationObject::getTitle()
	 */
	public function getTitle() {
		return '';
	}
	
	/**
	 * @see \wcf\system\user\notification\object\IUserNotificationObject::getURL()
	 */
	public function getURL() {
		return '';
	}
	
	/**
	 * @see \wcf\system\user\notification\object\IUserNotificationObject::getAuthorID()
	 */
	public function getAuthorID() {
		return $this->submitter;
	}
}
