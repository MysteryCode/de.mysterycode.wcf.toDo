<?php

namespace wcf\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\data\category\CategoryAction;
use wcf\data\todo\category\ToDoCategoryList;
use wcf\data\category\CategoryAction;
use wcf\data\todo\ToDoList;
use wcf\system\cronjob\AbstractCronjob;

/**
 * Delete unused todo-categories.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class DeleteUnusedToDoCategoriesCronjob extends AbstractCronjob {
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);

		if (!TODO_DELETE_OBSOLETE_CATEGORIES)
			return;

		// read used categories
		$todoList = new ToDoList();
		$todoList->readObjects();
		
		$usedCategoryIDs = array();
		foreach ($todoList->getObjects() as $todo) {
			if (!in_array($todo->categoryID, $usedCategoryIDs) && $todo->categoryID)
				$usedCategoryIDs[] = $todo->categoryID;
		}

		// read all categories
		$categoryList = TodoCategoryList();
		$categoryList->readObjects();
		
		$delete = array();
		foreach ($categoryList->getObjects() as $category) {
			if (!in_array($category->categoryID, $usedCategoryIDs))
				$delete[] = $category->categoryID;
		}

		if (!empty($delete)) {
			$deleteAction = new CategoryAction($delete, 'delete');
			$deleteAction->executeAction();
		}
	}
}
