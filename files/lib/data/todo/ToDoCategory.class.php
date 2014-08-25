<?php
namespace wcf\data\todo;
use wcf\data\todo\ToDoCache;
use wcf\data\DatabaseObject;

/**
 * Represents a todo category.
 * 
 * @author	Tim Duesterhus, Alexander Ebert
 * @copyright	2001-2014 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.smiley.category
 * @category	Community Framework
 */
class ToDoCategory extends DatabaseObject {

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'todo_category';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'id';
}
