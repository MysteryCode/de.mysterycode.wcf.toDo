<?php

namespace wcf\data\todo;
use wcf\data\like\object\ILikeObject;
use wcf\data\like\ILikeObjectTypeProvider;
use wcf\data\object\type\AbstractObjectTypeProvider;

/**
 * Object type provider for likable todos.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class LikeableToDoProvider extends AbstractObjectTypeProvider implements ILikeObjectTypeProvider {
	/**
	 * @inheritDoc
	 */
	public $className = ToDo::class;
	
	/**
	 * @inheritDoc
	 */
	public $decoratorClassName = LikeableToDo::class;
	
	/**
	 * @inheritDoc
	 */
	public $listClassName = ToDoList::class;
	
	/**
	 * @inheritDoc
	 */
	public function checkPermissions(ILikeObject $object) {
		return $object->canEnter();
	}
}
