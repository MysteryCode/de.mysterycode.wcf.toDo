<?php

namespace wcf\data\todo\assigned\user;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\todo\assigned\AssignedCache;

/**
 * Executes todo status related actions.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class AssignedUserAction extends AbstractDatabaseObjectAction {
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'wcf\data\todo\assigned\user\AssignedUserEditor';
	
	public function deleteByTodo() {
		if (!(isset($this->parameters['userID']) && $this->parameters['userID'] !== null))
			$userID = $this->parameters['userID'];
		else
			$userID = 0;
		
		foreach ($objects as $todo) {
			$assigns = AssignedCache::getUsersByTodo($todo->todoID);
			
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
