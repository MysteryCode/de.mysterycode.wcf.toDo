<?php

namespace wcf\data\todo\category;
use wcf\data\category\CategoryNode;

/**
 * Represents a category node.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryNode extends CategoryNode {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = TodoCategory::class;

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
	protected $stats = [];

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
	 * @return array <\wcf\data\user\User>
	 * @throws \wcf\system\exception\SystemException
	 */
	public function getUsersOnline() {
		return TodoCategoryCache::getInstance()->getUsersOnline($this->categoryID);
	}

	/**
	 * Returns the last todo of this category.
	 *
	 * @return \wcf\data\todo\ToDo
	 * @throws \wcf\system\exception\SystemException
	 */
	public function getLastTodo() {
		if (!$this->lastTodoLoaded) {
			$this->lastTodoLoaded = true;

			if ($this->getPermission('user.canEnterCategory')) {
				$this->lastTodo = TodoCategoryCache::getInstance()->getLastTodo($this->categoryID);

				$lastTodo = TodoCategoryCache::getInstance()->getLastTodo($this->categoryID);
				if ($lastTodo !== null) {
					if ($this->lastTodo === null || $lastTodo->time > $this->lastTodo->time) {
						$this->lastTodo = $lastTodo;
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
