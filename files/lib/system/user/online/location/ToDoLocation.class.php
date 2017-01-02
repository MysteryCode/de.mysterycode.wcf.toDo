<?php

namespace wcf\system\user\online\location;
use wcf\data\todo\ToDoCache;
use wcf\data\user\online\UserOnline;
use wcf\system\WCF;

/**
 * Provides the todo user online location
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoLocation implements IUserOnlineLocation {
	/**
	 * @see \wcf\system\user\online\location\IUserOnlineLocation::cache()
	 */
	public function cache(UserOnline $user) {
		// lazy method
	}

	/**
	 * @see \wcf\system\user\online\location\IUserOnlineLocation::get()
	 */
	public function get(UserOnline $user, $languageVariable = '') {
		$todo = ToDoCache::getInstance()->getTodo($user->objectID);
		
		if ($todo !== null && ($todo->canEnter() | $todo->canView())) {
			return WCF::getLanguage()->getDynamicVariable($languageVariable, array(
				'todo' => $todo
			));
		}
		
		return '';
	}
}
