<?php
namespace wcf\data\todo;
use wcf\data\object\type\AbstractObjectTypeProvider;

/**
 * 
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoProvider extends AbstractObjectTypeProvider {
	/**
	 * @inheritDoc
	 */
	public $className = ToDo::class;
	
	/**
	 * @inheritDoc
	 */
	public $listClassName = ToDoList::class;
}
