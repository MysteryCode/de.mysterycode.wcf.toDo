<?php

namespace wcf\data\todo\assigned\user;
use wcf\data\todo\assigned\AssignedCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;

/**
 * Executes todo status related actions.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class AssignedUserAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = 'wcf\data\todo\assigned\user\AssignedUserEditor';
	
	public function deleteByTodo() {
		if (!(isset($this->parameters['userID']) && $this->parameters['userID'] !== null))
			$userID = $this->parameters['userID'];
		else
			$userID = 0;
		
		if (empty($this->parameters['todoIDs'])) {
			throw new UserInputException('todoIDs');
		}
		
		$todoIDs = $this->parameters['todoIDs'];
		foreach ($todoIDs as $todoID) {
			$assigns = AssignedCache::getInstance()->getUsersByTodo($todoID);
			
			$deleteIDs = array();
			if (!empty($assigns)) {
				foreach ($assigns as $assign) {
					if ($userID == $assign->userID)
						$deleteIDs[] = $assign['assignID'];
				}
			}
			
			if (!empty($deleteIDs)) {
				$deleteAction = new self($deleteIDs, 'delete');
				$deleteAction->executeAction();
			}
		}
	}
}
