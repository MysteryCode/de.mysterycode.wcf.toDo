<?php

namespace wcf\system\page\handler;

use wcf\data\todo\ToDoCache;

/**
 * {@inheritDoc}
 */
class ToDoPageHandler extends AbstractMenuPageHandler {
	/**
	 * @inheritdoc
	 */
	public function getLink($objectID) {
		return ToDoCache::getInstance()->getTodo($objectID)->getLink();
	}

	/**
	 * @inheritdoc
	 */
	public function isValid($objectID) {
		$todo = ToDoCache::getInstance()->getTodo($objectID);
		return $todo !== null && $todo->todoID;
	}

	/**
	 * @inheritdoc
	 */
	public function isVisible($objectID = null) {
		$todo = ToDoCache::getInstance()->getTodo($objectID);
		return $todo !== null && $todo->isVisible();
	}
}
