<?php

use wcf\data\object\type\ObjectType;
use wcf\data\todo\ToDoEditor;
use wcf\data\todo\ToDoList;
use wcf\system\comment\CommentHandler;
use wcf\system\WCF;

/**
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
$package = $this->installation->getPackage();

$sql = "SELECT  otype.objectTypeID
	FROM    wcf" . WCF_N . "_object_type otype,
		wcf" . WCF_N . "_object_type_definition odef
	WHERE   otype.objectType = ?
		AND odef.definitionName = ?
		AND otype.definitionID = odef.definitionID";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(['de.mysterycode.wcf.toDo.toDoComment', 'com.woltlab.wcf.comment.commentableContent']);
$objectTypeID = $statement->fetchSingleRow(PDO::FETCH_COLUMN);

if ($objectTypeID !== false || !($objectTypeID > 0)) {
	$objectType = new ObjectType($objectTypeID);
	$commentManager = $objectType->getProcessor();
	
	$todoList = new ToDoList();
	$todoList->readObjects();
	
	foreach ($todoList->getObjects() as $todo) {
		$commentList = CommentHandler::getInstance()->getCommentList($commentManager, $objectTypeID, $todo->todoID);
		$todoEditor = new ToDoEditor($todo);
		$todoEditor->update([
			'comments' => $commentList->countObjects()
		]);
	}
}
