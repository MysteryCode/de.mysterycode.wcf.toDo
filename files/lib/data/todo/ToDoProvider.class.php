<?php
namespace wcf\data\todo;
use wcf\data\object\type\AbstractObjectTypeProvider;

class ToDoProvider extends AbstractObjectTypeProvider {
	/**
	 * @see	\wcf\data\object\type\AbstractObjectTypeProvider::$className
	 */
	public $className = 'wcf\data\todo\ToDo';
	
	/**
	 * @see	\wcf\data\object\type\AbstractObjectTypeProvider::$listClassName
	 */
	public $listClassName = 'wcf\data\todo\ToDoList';
}
