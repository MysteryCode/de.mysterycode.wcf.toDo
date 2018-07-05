<?php

namespace wcf\data\todo;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\AssignCacheBuilder;
use wcf\system\cache\builder\TodoCacheBuilder;
use wcf\system\cache\builder\TodoGeneralStatsCacheBuilder;
use wcf\system\cache\builder\TodoUserCacheBuilder;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Provides functions to edit todos.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 *
 * @mixin       ToDo
 * @method      ToDo    getDecoratedObject()
 */
class ToDoEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = ToDo::class;

	/**
	 * Updates the todo counter of the given users.
	 *
	 * @param array $users
	 *        => todo counter increase/decrease
	 * @throws \wcf\system\database\exception\DatabaseQueryException
	 * @throws \wcf\system\database\exception\DatabaseQueryExecutionException
	 */
	public static function updateUserToDoCounter(array $users) {
		$sql = "UPDATE	wcf" . WCF_N . "_user
			SET	todos = todos + ?
			WHERE	userID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		
		foreach ($users as $userID => $todos) {
			$statement->execute([$todos, $userID]);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public static function resetCache() {
		TodoCacheBuilder::getInstance()->reset();
		TodoGeneralStatsCacheBuilder::getInstance()->reset();
		AssignCacheBuilder::getInstance()->reset();
		TodoUserCacheBuilder::getInstance()->reset();
		
		UserStorageHandler::getInstance()->resetAll('unsolvedTodoCount');
		UserStorageHandler::getInstance()->resetAll('overdueTodoCount');
		UserStorageHandler::getInstance()->resetAll('waitingTodoCount');
		
		UserStorageHandler::getInstance()->resetAll('todoListAccessable');
	}
}
