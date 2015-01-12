<?php
use wcf\system\WCF;

$package = $this->installation->getPackage();

// unset notification settings for creating todos - also needed when reinstalling
$sql = "DELETE FROM	wcf".WCF_N."_user_notification_event_to_user
	WHERE		eventID = (SELECT eventID FROM wcf".WCF_N."_user_notification_event WHERE eventName = ? AND className = ?)";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array('create', 'wcf\\system\\user\\notification\\event\\ToDoCreateUserNotificationEvent'));
