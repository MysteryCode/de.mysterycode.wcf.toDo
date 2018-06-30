<?php

namespace wcf\data\todo\assigned\user;
use wcf\data\todo\assigned\AssignedCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;

/**
 * Executes todo status related actions.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class AssignedUserAction extends AbstractDatabaseObjectAction {
	public function deleteByTodo() {
		if (!empty($this->parameters['userID']))
			$userID = $this->parameters['userID'];
		else
			$userID = 0;
		
		if (empty($this->parameters['todoIDs'])) {
			throw new UserInputException('todoIDs');
		}
		
		$todoIDs = $this->parameters['todoIDs'];
		foreach ($todoIDs as $todoID) {
			$assigns = AssignedCache::getInstance()->getUsersByTodo($todoID);

			$deleteIDs = [];
			foreach ($assigns as $assign) {
				if ($userID == $assign->userID) {
					$deleteIDs[] = $assign->assignID;
				}
			}
			
			if (!empty($deleteIDs)) {
				$deleteAction = new self($deleteIDs, 'delete');
				$deleteAction->executeAction();
			}
		}
	}
}
