<?php
use wcf\system\dashboard\DashboardHandler;
use wcf\system\WCF;

$package = $this->installation->getPackage();

// set default values for dashboard boxes
DashboardHandler::setDefaultValues('de.mysterycode.wcf.ToDoListPage', array(
	'de.mysterycode.wcf.toDo.outstanding' => 1,
	'de.mysterycode.wcf.toDo.statistics' => 2
));
DashboardHandler::setDefaultValues('de.mysterycode.wcf.ToDoCategoryPage', array(
	'de.mysterycode.wcf.toDo.outstanding' => 1,
	'de.mysterycode.wcf.toDo.statistics' => 2

));

// unset notification settings for creating todos - also needed when reinstalling
$sql = "DELETE FROM	wcf".WCF_N."_user_notification_event_to_user
	WHERE		eventID = (SELECT eventID FROM wcf".WCF_N."_user_notification_event WHERE eventName = ? AND className = ?)";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array('create', 'wcf\\system\\user\\notification\\event\\ToDoCreateUserNotificationEvent'));