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
	public function delete() {
		/** @var \wcf\data\todo\status\TodoStatus $status */
		foreach ($this->objects as $status) {
			if ($status->locked) {
				throw new PermissionDeniedException();
			}
		}

		parent::delete();
	}
}
