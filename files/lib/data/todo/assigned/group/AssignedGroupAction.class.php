<?php

namespace wcf\data\todo\assigned\group;
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
class AssignedGroupAction extends AbstractDatabaseObjectAction {
	public function deleteByTodo() {
		if (!empty($this->parameters['groupID']))
			$groupID = $this->parameters['groupID'];
		else
			$groupID = 0;
		
		if (empty($this->parameters['todoIDs'])) {
			throw new UserInputException('todoIDs');
		}
		
		$todoIDs = $this->parameters['todoIDs'];
		foreach ($todoIDs as $todoID) {
			$assigns = AssignedCache::getInstance()->getGroupsByTodo($todoID);
			
			$deleteIDs = [];
			foreach ($assigns as $assign) {
				if ($groupID == $assign->groupID) {
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
