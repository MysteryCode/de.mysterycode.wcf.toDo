<?php

namespace wcf\system\user\notification\object;

use wcf\data\DatabaseObjectDecorator;
use wcf\system\user\notification\object\IUserNotificationObject;

class ToDoUserNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject {
	
	/**
	 *
	 * @see \wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\todo\ToDo';
	
	/**
	 *
	 * @see \wcf\system\user\notification\object\IUserNotificationObject::getObjectID()
	 */
	public function getObjectID() {
		return $this->id;
	}
	
	/**
	 *
	 * @see \wcf\system\user\notification\object\IUserNotificationObject::getTitle()
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 *
	 * @see \wcf\system\user\notification\object\IUserNotificationObject::getURL()
	 */
	public function getURL() {
		return $this->getLink();
	}
	
	/**
	 *
	 * @see \wcf\system\user\notification\object\IUserNotificationObject::getAuthorID()
	 */
	public function getAuthorID() {
		return $this->submitter;
	}
}
