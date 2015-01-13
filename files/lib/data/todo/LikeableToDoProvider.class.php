<?php

namespace wcf\data\todo;
use wcf\data\like\object\ILikeObject;
use wcf\data\like\ILikeObjectTypeProvider;
use wcf\data\object\type\AbstractObjectTypeProvider;

/**
 * Object type provider for likable todos.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class LikeableToDoProvider extends AbstractObjectTypeProvider implements ILikeObjectTypeProvider {
	/**
	 * @see	\wcf\data\object\type\AbstractObjectTypeProvider::$className
	 */
	public $className = 'wcf\data\todo\ToDo';
	
	/**
	 * @see	\wcf\data\object\type\AbstractObjectTypeProvider::$decoratorClassName
	 */
	public $decoratorClassName = 'wcf\data\todo\LikeableToDo';
	
	/**
	 * @see	\wcf\data\object\type\AbstractObjectTypeProvider::$listClassName
	 */
	public $listClassName = 'wcf\data\todo\ToDoList';

	/**
	 * @see	\wcf\data\like\ILikeObjectTypeProvider::checkPermissions()
	 */
	public function checkPermissions(ILikeObject $object) {
		return $object->canEnter();
	}
}
