<?php
namespace wcf\system\cache\builder;
use wcf\data\todo\ToDoList;
use wcf\data\todo\ToDoCategoryList;
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
			'todos' => array (),
			'categories' => array ()
		);
		
		// get todos
		$todoList = new ToDoList();
		$todoList->sqlOrderBy = 'todo_table.id ASC';
		$todoList->readObjects();
		$data['todos'] = $todoList->getObjects();
		
		// get categories
		$sql = "SELECT		*
			FROM		wcf".WCF_N."_todo_category
			ORDER BY	id";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		while ($object = $statement->fetchObject('wcf\data\todo\ToDoCategory')) {
			$data['categories'][$object->id] = $object;
		}
		
		/* get todo categories
		$categoryList = new ToDoCategoryList();
		$categoryList->sqlOrderBy = 'id ASC';
		$categoryList->readObjects();
		$data['categories'] = $categoryList->getObjects();
		*/
		return $data;
	}
}