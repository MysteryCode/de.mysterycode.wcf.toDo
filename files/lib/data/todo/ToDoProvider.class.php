<?php
namespace wcf\data\todo;
use wcf\data\object\type\AbstractObjectTypeProvider;

/**
 * 
 * 
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
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
