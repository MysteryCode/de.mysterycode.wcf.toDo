<?php

namespace wcf\data\todo;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\AssignCacheBuilder;
use wcf\system\cache\builder\TodoCacheBuilder;
use wcf\system\WCF;

/**
 * Provides functions to edit todos.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @see \wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\todo\ToDo';
	
	/**
	 * Updates the todo counter of the given users.
	 *
	 * @param array $users
	 *        => todo counter increase/decrease
	 */
	public static function updateUserToDoCounter(array $users) {
		$sql = "UPDATE	wcf" . WCF_N . "_user
			SET	todos = todos + ?
			WHERE	userID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		
		foreach ($users as $userID => $todos) {
			$statement->execute(array($todos, $userID));
		}
	}
	
	/**
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetCache() {
		TodoCacheBuilder::getInstance()->reset();
		AssignCacheBuilder::getInstance()->reset();
	}
}
