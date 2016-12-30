<?php

namespace wcf\data\todo\status;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\PermissionDeniedException;

/**
 * Executes todo status related actions.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoStatusAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = 'wcf\data\todo\status\TodoStatusEditor';
	
	/**
	 * @inheritDoc
	 */
	public function delete() {
		if ($this->locked)
			throw new PermissionDeniedException();
		
		parent::delete();
	}
}
