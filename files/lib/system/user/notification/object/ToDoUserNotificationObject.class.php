<?php

namespace wcf\system\user\notification\object;
use wcf\data\todo\ToDo;
use wcf\data\DatabaseObjectDecorator;

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
	 * @inheritDoc
	 */
	protected static $baseClass = ToDo::class;
	
	/**
	 * @inheritDoc
	 */
	public function getObjectID() {
		return $this->todoID;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->getDecoratedObject()->getLink();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getURL() {
		return $this->getDecoratedObject()->getLink();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getAuthorID() {
		return $this->getDecoratedObject()->submitter;
	}
}
