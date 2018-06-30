<?php

namespace wcf\data\todo\status;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\PermissionDeniedException;

/**
 * Executes todo status related actions.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoStatusAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.content.toDo.status.canDelete'];
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsUpdate = ['admin.content.toDo.status.canEdit'];
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsCreate = ['admin.content.toDo.status.canAdd'];
	
	/**
	 * @inheritDoc
	 */
	public function validateDelete() {
		parent::validateDelete();
		
		/** @var \wcf\data\todo\status\TodoStatus $status */
		foreach ($this->objects as $status) {
			if ($status->locked) {
				throw new PermissionDeniedException();
			}
		}
	}
}
