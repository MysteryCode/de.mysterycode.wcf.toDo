<?php
use wcf\data\category\CategoryAction;
use wcf\data\todo\category\TodoCategoryEditor;
use wcf\data\todo\ToDoEditor;
use wcf\data\todo\ToDoList;
use wcf\system\category\CategoryHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
$package = $this->installation->getPackage();

// matching save
$matching = array();

// update old category system into WCF's categories
CategoryHandler::getInstance()->reloadCache();
$catObjectType = CategoryHandler::getInstance()->getObjectTypeByName('de.mysterycode.wcf.toDo');
$sql = "SELECT	*
	FROM	wcf" . WCF_N . "_todo_category";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();
$i = 1;
$matching['categories'] = array();

// default category
$categoryData = array(
	'data' => array(
		'additionalData' => serialize(array()),
		'description' => null,
		'isDisabled' => 0,
		'objectTypeID' => $catObjectType->objectTypeID,
		'parentCategoryID' => 0,
		'showOrder' => $i,
		'title' => 'Default Category'
	)
);
$categoryAction = new CategoryAction(array(), 'create', $categoryData);
$resultValues = $categoryAction->executeAction();
$matching['categories'][null] = $resultValues['returnValues']->categoryID;

while ($row = $statement->fetchArray()) {
	$categoryData = array(
		'data' => array(
			'additionalData' => serialize(array()),
			'description' => null,
			'isDisabled' => 0,
			'objectTypeID' => $catObjectType->objectTypeID,
			'parentCategoryID' => 0,
			'showOrder' => $i,
			'title' => StringUtil::trim($row['title'])
		)
	);
	
	$categoryAction = new CategoryAction(array(), 'create', $categoryData);
	$resultValues = $categoryAction->executeAction();
	$matching['categories'][$row['id']] = $resultValues['returnValues']->categoryID;
	$i ++;
}

// update old status into new status system
$matching['status'] = array(
	1 => 2,
	2 => 3,
	3 => 1,
	4 => 4,
	5 => 5,
	6 => 6,
	null => null
);

// update todo data
$todoList = new ToDoList();
$todoList->readObjects();
$todos = $todoList->getObjects();
foreach ($todos as $todo) {
	$todoEditor = new ToDoEditor($todo);
	$todoEditor->update(array(
		'statusID' => $matching['status'][$todo->status],
		'categoryID' => $matching['categories'][$todo->category]
	));
}

// reset cache
ToDoEditor::resetCache();
TodoCategoryEditor::resetCache();

