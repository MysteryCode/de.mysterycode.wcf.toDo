<?php

namespace wcf\data\todo;
use wcf\data\label\Label;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * Represents a viewable todo.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 *
 * @mixin       ToDo
 */
class ViewableToDo extends DatabaseObjectDecorator {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = ToDo::class;
	
	/**
	 * user profile object
	 * @var	\wcf\data\user\UserProfile
	 */
	protected $userProfile = null;
	
	/**
	 * list of assigned labels
	 * @var	Label[]
	 */
	protected $labels = [];
	
	/**
	 * effective visit time
	 * @var	integer
	 */
	protected $effectiveVisitTime = null;
	
	/**
	 * Returns the user profile object.
	 * 
	 * @return	\wcf\data\user\UserProfile
	 */
	public function getUserProfile() {
		if ($this->userProfile === null) {
			$this->userProfile = new UserProfile(new User(null, $this->getDecoratedObject()->data));
		}
		
		return $this->userProfile;
	}

	/**
	 * Returns the effective visit time.
	 *
	 * @return int
	 * @throws \wcf\system\exception\SystemException
	 */
	public function getVisitTime() {
		if ($this->effectiveVisitTime === null) {
			if (WCF::getUser()->userID) {
				$this->effectiveVisitTime = max($this->visitTime, $this->categoryVisitTime, VisitTracker::getInstance()->getVisitTime('de.mysterycode.wcf.toDo'));
			} else {
				$this->effectiveVisitTime = max(VisitTracker::getInstance()->getObjectVisitTime('de.mysterycode.wcf.toDo', $this->todoID), VisitTracker::getInstance()->getObjectVisitTime('de.mysterycode.wcf.toDo.category', $this->categoryID), VisitTracker::getInstance()->getVisitTime('de.mysterycode.wcf.toDo'));
			}
			if ($this->effectiveVisitTime === null) {
				$this->effectiveVisitTime = 0;
			}
		}
		
		return $this->effectiveVisitTime;
	}
	
	/**
	 * Returns true if this todo is new for the active user.
	 * 
	 * @return	boolean
	 */
	public function isNew() {
		if ($this->time > $this->getVisitTime()) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the viewable todo object with the given todo id.
	 * 
	 * @param	integer		$todoID
	 * @return ViewableToDo|null
	 */
	public static function getToDo($todoID) {
		$list = new ViewableToDoList();
		$list->setObjectIDs([$todoID]);
		$list->readObjects();
		/** @var ViewableToDo[] $objects */
		$objects = $list->getObjects();

		if (isset($objects[$todoID])) {
			return $objects[$todoID];
		}
		
		return null;
	}
	
	/**
	 * Adds a label.
	 *
	 * @param	Label	$label
	 */
	public function addLabel(Label $label) {
		$this->labels[$label->labelID] = $label;
	}
	
	/**
	 * Returns a list of labels.
	 *
	 * @return	Label[]
	 */
	public function getLabels() {
		return $this->labels;
	}
	
	/**
	 * Returns the primary (first) label.
	 *
	 * @return	Label|null
	 */
	public function getPrimaryLabel() {
		if (!$this->hasLabels()) return null;
		
		return reset($this->labels);
	}
	
	/**
	 * Returns true if one or more labels are assigned to this thread.
	 *
	 * @return	boolean
	 */
	public function hasLabels() {
		return count($this->labels) ? true : false;
	}
	
	/**
	 * @inheritDoc
	 */
	public function isSubscribed() {
		return ($this->watchID ? 1 : 0);
	}
}
