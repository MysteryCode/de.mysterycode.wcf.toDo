<?php
namespace wcf\data\todo;
use wcf\system\edit\IHistorySavingObject;
use wcf\system\edit\IHistorySavingObjectTypeProvider;
use wcf\system\exception\PermissionDeniedException;

/**
 * Object type provider for history saving todos.
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class HistorySavingToDoProvider extends ToDoProvider implements IHistorySavingObjectTypeProvider {
	/**
	 * @see	\wcf\data\object\type\AbstractObjectTypeProvider::$decoratorClassName
	 */
	public $decoratorClassName = 'wcf\data\todo\HistorySavingToDo';
	
	/**
	 * @see	\wcf\system\edit\IHistorySavingObjectTypeProvider::checkPermissions()
	 */
	public function checkPermissions(IHistorySavingObject $object) {
		if (!($object instanceof HistorySavingToDo))
			return false;
		
		if (!$object->canEdit())
			throw new PermissionDeniedException();
	}
	
	/**
	 * @see	\wcf\system\edit\IHistorySavingObjectTypeProvider::getActivePageMenuItem()
	 */
	public function getActivePageMenuItem() {
		return 'wcf.header.menu.toDo';
	}
}