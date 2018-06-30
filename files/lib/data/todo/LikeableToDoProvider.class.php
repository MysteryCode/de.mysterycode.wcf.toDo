<?php

namespace wcf\data\todo;
use wcf\data\like\IRestrictedLikeObjectTypeProvider;
use wcf\data\like\object\ILikeObject;
use wcf\data\object\type\AbstractObjectTypeProvider;
use wcf\system\like\IViewableLikeProvider;
use wcf\system\WCF;

/**
 * Object type provider for likable todos.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class LikeableToDoProvider extends AbstractObjectTypeProvider implements IRestrictedLikeObjectTypeProvider, IViewableLikeProvider {
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
	 * @param LikeableToDo $object
	 */
	public function checkPermissions(ILikeObject $object) {
		return $object->canEnter();
	}
	
	/**
	 * @inheritDoc
	 */
	public function canLike(ILikeObject $object) {
		return $this->checkPermissions($object) && ($object->getUserID() !== WCF::getUser()->userID || !LIKE_ALLOW_FOR_OWN_CONTENT);
	}
	
	/**
	 * @inheritDoc
	 */
	public function canViewLikes(ILikeObject $object) {
		return $this->checkPermissions($object);
	}
	
	/**
	 * @inheritDoc
	 */
	public function prepare(array $likes) {
		$entryIDs = [];
		foreach ($likes as $like) {
			$entryIDs[] = $like->objectID;
		}
		
		// fetch todos
		$todoList = new ViewableToDoList();
		$todoList->setObjectIDs($entryIDs);
		$todoList->readObjects();
		$todos = $todoList->getObjects();
		
		// set message
		foreach ($likes as $like) {
			if (isset($todos[$like->objectID])) {
				$todo = $todos[$like->objectID];
				
				// check permissions
				if (!$todo->isVisible()) {
					continue;
				}
				$like->setIsAccessible();
				
				// short output
				$text = WCF::getLanguage()->getDynamicVariable('wcf.like.title.de.mysterycode.mcgb.likeableEntry', [
					'todo' => $todo,
					'like' => $like
				]);
				$like->setTitle($text);
				
				// output
				$like->setDescription($todo->getSimplifiedFormattedMessage());
			}
		}
	}
}
