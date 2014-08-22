<?php
namespace wcf\system\cache\builder;
use wcf\data\todo\ToDoList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Represents the todo cache builder.
 *
 * @author Florian Gail
 * @copyright 2014 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoCacheBuilder extends AbstractCacheBuilder {
	/**
	 *
	 * @see \wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'todos' => array () 
		);
		
		$todoList = new ToDoList ();
		$todoList->sqlOrderBy = 'todo_table.id ASC';
		$todoList->readObjects();
		$data['todos'] = $todoList->getObjects();
		return $data;
	}
}