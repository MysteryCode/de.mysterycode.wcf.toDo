<?php
namespace wcf\system\clipboard\action;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\WCF;

/**
 * Prepares clipboard editor items for todos.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoClipboardAction extends AbstractClipboardAction {
	/**
	 * @see	\wcf\system\clipboard\action\AbstractClipboardAction::$actionClassActions
	 */
	protected $actionClassActions = array('enable', 'disable', 'delete', 'restore');

	/**
	 * @see	\wcf\system\clipboard\action\AbstractClipboardAction::$supportedActions
	 */
	protected $supportedActions = array('enable', 'disable', 'trash', 'delete', 'restore');

	/**
	 * @see	\wcf\system\clipboard\action\IClipboardAction::execute()
	 */
	public function execute(array $objects, ClipboardAction $action) {
		$item = parent::execute($objects, $action);
		if ($item === null) {
			return null;
		}

		// handle actions
		switch ($action->actionName) {
			case 'delete':
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.mysterycode.wcf.toDo.toDo.delete.confirmMessage', array(
					'count' => $item->getCount()
				)));
				break;

			case 'restore':
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.mysterycode.wcf.toDo.toDo.restore.confirmMessage', array(
					'count' => $item->getCount()
				)));
				break;
		}

		return $item;
	}

	/**
	 * @see	\wcf\system\clipboard\action\IClipboardAction::getClassName()
	 */
	public function getClassName() {
		return 'wcf\data\todo\ToDoAction';
	}

	/**
	 * @see	\wcf\system\clipboard\action\IClipboardAction::getTypeName()
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
		$todoIDs = array();

		foreach ($this->objects as $todo) {
			if ($todo->isDisabled && !$todo->isDeleted && $todo->canEnable()) {
				$todoIDs[] = $todo->id;
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
		$todoIDs = array();

		foreach ($this->objects as $todo) {
			if (!$todo->isDisabled && !$todo->isDeleted && $todo->canEnable()) {
				$todoIDs[] = $todo->id;
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
		$todoIDs = array();

		foreach ($this->objects as $todo) {
			if (!$todo->isDeleted && $todo->canDelete()) {
				$todoIDs[] = $todo->id;
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
		$todoIDs = array();

		foreach ($this->objects as $todo) {
			if ($todo->isDeleted && $todo->canDeleteCompletely()) {
				$todoIDs[] = $todo->id;
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
		$todoIDs = array();

		foreach ($this->objects as $todo) {
			if ($todo->isDeleted && $todo->canRestore()) {
				$todoIDs[] = $todo->id;
			}
		}

		return $todoIDs;
	}
}
