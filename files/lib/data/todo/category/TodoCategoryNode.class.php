<?php

namespace wcf\data\todo\category;
use wcf\data\category\CategoryNode;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Represents a category node.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */
class TodoCategoryNode extends CategoryNode {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\todo\category\TodoCategory';

	/**
	 * depth of this category node
	 * @var	integer
	 */
	protected $depth = 0;

	/**
	 * True if the last todo has already been loaded.
	 * @var	boolean
	 */
	protected $lastTodoLoaded = false;

	/**
	 * last todo in this category
	 * @var	\wcf\data\todo\ToDo
	 */
	protected $lastTodo = null;

	/**
	 * list of category stats
	 * @var array
	 */
	protected $stats = array();

	/**
	 * Returns a list of sub categories of this category.
	 *
	 * @return array<\wcf\data\category\CategoryNode>
	 */
	public function getSubCategories() {
		return $this->children;
	}

	/**
	 * Sets the depth for this category node.
	 *
	 * @param	integer		$depth
	 */
	public function setDepth($depth) {
		$this->depth = $depth;
	}

	/**
	 * Returns the depth of this category node.
	 *
	 * @return	integer
	 */
	public function getDepth() {
		return $this->depth;
	}

	/**
	 * Returns the users online list.
	 *
	 * @return	array<\wcf\data\user\User>
	 */
	public function getUsersOnline() {
		return TodoCategoryCache::getInstance()->getUsersOnline($this->categoryID);
	}

	/**
	 * Returns the last todo of this category.
	 *
	 * @return	\wcf\data\todo\ToDo
	 */
	public function getLastTodo() {
		if (!$this->lastTodoLoaded) {
			$this->lastTodoLoaded = true;

			if ($this->getPermission('user.canEnterCategory')) {
				$this->lastTodo = TodoCategoryCache::getInstance()->getLastTodo($this->categoryID);

				$languageIDs = WCF::getUser()->getLanguageIDs();
				foreach ($languageIDs as $languageID) {
					$lastTodo = TodoCategoryCache::getInstance()->getLastTodo($this->categoryID, $languageID);
					if ($lastTodo !== null) {
						if ($this->lastTodo === null || $lastTodo->time > $this->lastTodo->time) {
							$this->lastTodo = $lastTodo;
						}
					}
				}

				foreach ($this->children as $childNode) {
					if (($childLastTodo = $childNode->getLastTodo()) !== null) {
						if ($this->lastTodo === null || $childLastTodo->time > $this->lastTodo->time) {
							$this->lastTodo = $childLastTodo;
						}
					}
				}
			}
		}

		return $this->lastTodo;
	}

	/**
	 * Returns the number of items for the given stat type of this category.
	 *
	 * @param	string		$statType
	 * @return	integer
	 */
	public function getStats($statType) {
		switch ($statType) {
			case 'todos':
			case 'todoComments':
				if (!isset($this->stats[$statType])) {
					$this->stats[$statType] = $this->getStatObject()->$statType;
					foreach ($this->children as $childNode) {
						$this->stats[$statType] += $childNode->getStats($statType);
					}
				}

				return $this->stats[$statType];
				break;
			default:
				return 0;
		}
	}
}
