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
 */
class LikeableToDo extends AbstractLikeObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = 'wcf\data\todo\ToDo';
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getURL() {
		return $this->getLink();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getUserID() {
		return $this->submitter;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getObjectID() {
		return $this->todoID;
	}
	
	/**
	 * @inheritDoc
	 */
	public function updateLikeCounter($cumulativeLikes) {
		$todoEditor = new ToDoEditor($this->getDecoratedObject());
		$todoEditor->update(array(
			'cumulativeLikes' => $cumulativeLikes
		));
	}
}
