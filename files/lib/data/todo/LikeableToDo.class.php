<?php

namespace wcf\data\todo;
use wcf\data\like\object\AbstractLikeObject;
use wcf\data\todo\ToDoEditor;

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
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\todo\ToDo';
	
	/**
	 * @see	\wcf\data\like\object\ITitle::getTitle()
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * @see	\wcf\data\like\object\ILikeObject::getURL()
	 */
	public function getURL() {
		return $this->getLink();
	}
	
	/**
	 * @see	\wcf\data\like\object\ILikeObject::getUserID()
	 */
	public function getUserID() {
		return $this->submitter;
	}
	
	/**
	 * @see	\wcf\data\like\object\ILikeObject::getObjectID()
	 */
	public function getObjectID() {
		return $this->todoID;
	}
	
	/**
	 * @see	\wcf\data\like\object\ILikeObject::updateLikeCounter()
	 */
	public function updateLikeCounter($cumulativeLikes) {
		$todoEditor = new ToDoEditor($this->getDecoratedObject());
		$todoEditor->update(array(
			'cumulativeLikes' => $cumulativeLikes
		));
	}
}
