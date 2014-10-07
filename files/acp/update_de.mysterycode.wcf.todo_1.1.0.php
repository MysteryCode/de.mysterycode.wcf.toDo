<?php
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoList;
use wcf\data\user\User;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\WCF;

$package = $this->installation->getPackage();

// set username of todos
$sql = "SELECT	*
	FROM	wcf".WCF_N."_todo
	WHERE	username = ?";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array(''));
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
	FROM	wcf".WCF_N."_todo_to_user
	WHERE	username = ?";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array(''));
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

// change colors to rgba
$update = "UPDATE		wcf".WCF_N."_todo_category
	SET		color = ?
	WHERE		color = ?";
$statementUpdate = WCF::getDB()->prepareStatement($update);
$statementUpdate->execute(array('rgba(51,102,153,1)', 'blue'));
$statementUpdate->execute(array('rgba(102,102,102,1)', 'grey'));
$statementUpdate->execute(array('rgba(51,51,51,1)', 'black'));
$statementUpdate->execute(array('rgba(255,255,0,1)', 'yellow'));
$statementUpdate->execute(array('rgba(204,0,0,1)', 'red'));
$statementUpdate->execute(array('rgba(0,153,0,1)', 'green'));

// unset notification settings for creating todos - also needed when reinstalling
$sql = "DELETE FROM	wcf".WCF_N."_user_notification_event_to_user
	WHERE		eventID = (SELECT eventID FROM wcf".WCF_N."_user_notification_event WHERE eventName = ? AND className = ?)";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array('create', 'wcf\\system\\user\\notification\\event\\ToDoCreateUserNotificationEvent'));