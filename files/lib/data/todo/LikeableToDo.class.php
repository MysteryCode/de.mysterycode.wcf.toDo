<?php

namespace wcf\data\todo;
use wcf\data\like\object\AbstractLikeObject;
use wcf\data\todo\ToDoEditor;
use wcf\system\request\LinkHandler;

/**
 * Likeable object implementation for todos.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
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
		return $this->id;
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