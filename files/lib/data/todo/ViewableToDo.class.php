<?php

namespace wcf\data\todo;
use wcf\data\todo\category\TodoCategoryCache;
use wcf\data\label\Label;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\language\LanguageFactory;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * Represents a viewable todo.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ViewableToDo extends DatabaseObjectDecorator {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\todo\ToDo';
	
	/**
	 * user profile object
	 * @var	\wcf\data\user\UserProfile
	 */
	protected $userProfile = null;
	
	/**
	 * list of assigned labels
	 * @var	array<\wcf\data\label\Label>
	 */
	protected $labels = array();
	
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
	 * @return	integer
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
	 * @return	\wcf\data\todo\ViewableToDoList
	 */
	public static function getToDo($todoID) {
		$list = new ViewableToDoList();
		$list->setObjectIDs(array($todoID));
		$list->readObjects();
		$objects = $list->getObjects();
		
		if (isset($objects[$todoID]))
			return $objects[$todoID];
		
		return null;
	}
	
	/**
	 * Adds a label.
	 * 
	 * @param	\wcf\data\label\Label	$label
	 */
	public function addLabel(Label $label) {
		$this->labels[$label->labelID] = $label;
	}
	
	/**
	 * Returns a list of labels.
	 * 
	 * @return	array<\wcf\data\label\Label>
	 */
	public function getLabels() {
		return $this->labels;
	}
	
	/**
	 * Returns the primary (first) label.
	 * 
	 * @return	\wcf\data\label\Label
	 */
	public function getPrimaryLabel() {
		if (!$this->hasLabels())
			return null;
		
		foreach ($this->labels as $label)
			return $label;
	}
	
	/**
	 * Returns true if one or more labels are assigned to this todo.
	 * 
	 * @return	boolean
	 */
	public function hasLabels() {
		return (count($this->labels)) ? true : false;
	}
	
	/**
	 * @see	\wcf\data\todo\ToDo::isSubscribed()
	 */
	public function isSubscribed() {
		return ($this->watchID ? 1 : 0);
	}
}
