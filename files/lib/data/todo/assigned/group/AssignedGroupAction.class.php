<?php

namespace wcf\data\todo\assigned\group;
use wcf\data\todo\assigned\AssignedCache;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes todo status related actions.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class AssignedGroupAction extends AbstractDatabaseObjectAction {
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'wcf\data\todo\assigned\group\AssignedGroupEditor';
	
	public function deleteByTodo() {
		if (!(isset($this->parameters['groupID']) && $this->parameters['groupID'] !== null))
			$groupID = $this->parameters['groupID'];
		else
			$groupID = 0;
		
		foreach ($objects as $todo) {
			$assigns = AssignedCache::getGroupsByTodo($todo->todoID);
			
			$deleteIDs = array();
			if (!empty($assigns)) {
				foreach ($assigns as $assign) {
					if ($groupID == $assign->groupID)
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
