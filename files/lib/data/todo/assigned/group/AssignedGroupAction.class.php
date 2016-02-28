<?php

namespace wcf\data\todo\assigned\group;
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
