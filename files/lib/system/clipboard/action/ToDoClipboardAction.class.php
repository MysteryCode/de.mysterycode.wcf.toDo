<?php

namespace wcf\system\clipboard\action;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\WCF;

/**
 * Prepares clipboard editor items for todos.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoClipboardAction extends AbstractClipboardAction {
	/**
	 * @inheritDoc
	 */
	protected $actionClassActions = ['enable', 'disable', 'delete', 'restore'];

	/**
	 * @inheritDoc
	 */
	protected $supportedActions = ['enable', 'disable', 'trash', 'delete', 'restore'];

	/**
	 * @inheritDoc
	 */
	public function execute(array $objects, ClipboardAction $action) {
		$item = parent::execute($objects, $action);
		if ($item === null) {
			return null;
		}

		// handle actions
		switch ($action->actionName) {
			case 'delete':
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.mysterycode.wcf.toDo.toDo.delete.confirmMessage', [
					'count' => $item->getCount()
				]));
				break;

			case 'restore':
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.mysterycode.wcf.toDo.toDo.restore.confirmMessage', [
					'count' => $item->getCount()
				]));
				break;
		}

		return $item;
	}

	/**
	 * @inheritDoc
	 */
	public function getClassName() {
		return 'wcf\data\todo\ToDoAction';
	}

	/**
	 * @inheritDoc
	 */
	public function getTypeName() {
		return 'de.mysterycode.wcf.toDo.toDo';
	}

	/**
	 * Returns the todo ids of todos which can be enabled.
	 *
	 * @return	array<integer>
	 */
	public function validateEnable() {
		$todoIDs = [];

		foreach ($this->objects as $todo) {
			if ($todo->isDisabled && !$todo->isDeleted && $todo->canEnable()) {
				$todoIDs[] = $todo->todoID;
			}
		}

		return $todoIDs;
	}

	/**
	 * Returns the todo ids of todos which can be disabled.
	 *
	 * @return	array<integer>
	 */
	public function validateDisable() {
		$todoIDs = [];

		foreach ($this->objects as $todo) {
			if (!$todo->isDisabled && !$todo->isDeleted && $todo->canEnable()) {
				$todoIDs[] = $todo->todoID;
			}
		}

		return $todoIDs;
	}

	/**
	 * Returns the todo ids of todos which can be trashed.
	 *
	 * @return	array<integer>
	 */
	public function validateTrash() {
		$todoIDs = [];

		foreach ($this->objects as $todo) {
			if (!$todo->isDeleted && $todo->canDelete()) {
				$todoIDs[] = $todo->todoID;
			}
		}

		return $todoIDs;
	}

	/**
	 * Returns the todo ids of todos which can be deleted.
	 *
	 * @return	array<integer>
	 */
	public function validateDelete() {
		$todoIDs = [];

		foreach ($this->objects as $todo) {
			if ($todo->isDeleted && $todo->canDeleteCompletely()) {
				$todoIDs[] = $todo->todoID;
			}
		}

		return $todoIDs;
	}

	/**
	 * Returns the todo ids of todos which can be restored.
	 *
	 * @return	array<integer>
	 */
	public function validateRestore() {
		$todoIDs = [];

		foreach ($this->objects as $todo) {
			if ($todo->isDeleted && $todo->canRestore()) {
				$todoIDs[] = $todo->todoID;
			}
		}

		return $todoIDs;
	}
}
