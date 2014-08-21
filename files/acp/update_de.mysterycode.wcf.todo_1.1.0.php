<?php
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoList;
use wcf\data\user\User;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\WCF;

$package = $this->installation->getPackage();

// set username of todos
$sql = "SELECT	*
	FROM	wcf".WCF_N."_todo";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();
$categories = array();
$update = "UPDATE		wcf".WCF_N."_todo
	SET		username = ?
	WHERE		id = ?";
$statementUpdate = WCF::getDB()->prepareStatement($update);
while ($row = $statement->fetchArray()) {
	$user = new User($row['submitter']);
	if($user->username)
		$username = $user->username;
	else
		$username = 'Gast';
	$statementUpdate->execute(array($username, $row['id']));
}

// set username of responsibles
$sql = "SELECT	*
	FROM	wcf".WCF_N."_todo_to_user";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();
$categories = array();
$update = "UPDATE		wcf".WCF_N."_todo_to_user
	SET		username = ?
	WHERE		toDoID = ?
			AND userID = ?";
$statementUpdate = WCF::getDB()->prepareStatement($update);
while ($row = $statement->fetchArray()) {
	$user = new User($row['userID']);
	if($user->username)
		$username = $user->username;
	else
		$username = 'Gast';
	$statementUpdate->execute(array($username, $row['toDoID'], $row['userID']));
}