<?php

namespace wcf\data\todo;
use wcf\data\like\object\AbstractLikeObject;

/**
 * Likeable object implementation for todos.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @see ToDo
 *
 * @method ToDo getDecoratedObject()
 */
class LikeableToDo extends AbstractLikeObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = ToDo::class;
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->getDecoratedObject()->getTitle();
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
	public function getUserID() {
		return $this->getDecoratedObject()->submitter;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getObjectID() {
		return $this->getDecoratedObject()->todoID;
	}
	
	/**
	 * @inheritDoc
	 */
	public function updateLikeCounter($cumulativeLikes) {
		$todoEditor = new ToDoEditor($this->getDecoratedObject());
		$todoEditor->update(['cumulativeLikes' => $cumulativeLikes]);
	}
}
