<?php
use wcf\system\dashboard\DashboardHandler;
use wcf\system\WCF;

$package = $this->installation->getPackage();

// workaround because wcf doesn't prove
$sql = "SELECT *
<<<<<<< HEAD
	FROM		wcf" . WCF_N . "_dashboard_option
	WHERE		objectTypeID = (SELECT objectTypeID FROM wcf" . WCF_N . "_object_type WHERE objectType = ? AND definitionID = (SELECT definitionID FROM wcf" . WCF_N . "_object_type_definition WHERE definitionName = ?))
			AND boxID = (SELECT boxID FROM wcf" . WCF_N . "_dashboard_box WHERE boxName = ?)";
$statement = WCF::getDB()->prepareStatement($sql);

$statement->execute(array(
	'de.mysterycode.wcf.ToDoListPage',
	'com.woltlab.wcf.user.dashboardContainer',
	'de.mysterycode.wcf.toDo.outstanding'
));
=======
	FROM		wcf".WCF_N."_dashboard_option
	WHERE		objectTypeID = (SELECT objectTypeID FROM wcf".WCF_N."_object_type WHERE objectType = ? AND definitionID = (SELECT definitionID FROM wcf".WCF_N."_object_type_definition WHERE definitionName = ?))
			AND boxID = (SELECT boxID FROM wcf".WCF_N."_dashboard_box WHERE boxName = ?)";
$statement = WCF::getDB()->prepareStatement($sql);

$statement->execute(array('de.mysterycode.wcf.ToDoListPage', 'com.woltlab.wcf.user.dashboardContainer', 'de.mysterycode.wcf.toDo.outstanding'));
>>>>>>> master
$row = $statement->fetchArray();
if ($row === false) {
	// set default values for dashboard boxes
	DashboardHandler::setDefaultValues('de.mysterycode.wcf.ToDoListPage', array(
		'de.mysterycode.wcf.toDo.outstanding' => 1
	));
}
<<<<<<< HEAD
$statement->execute(array(
	'de.mysterycode.wcf.ToDoListPage',
	'com.woltlab.wcf.user.dashboardContainer',
	'de.mysterycode.wcf.toDo.statistics'
));
=======
$statement->execute(array('de.mysterycode.wcf.ToDoListPage', 'com.woltlab.wcf.user.dashboardContainer', 'de.mysterycode.wcf.toDo.statistics'));
>>>>>>> master
$row = $statement->fetchArray();
if ($row === false) {
	// set default values for dashboard boxes
	DashboardHandler::setDefaultValues('de.mysterycode.wcf.ToDoListPage', array(
		'de.mysterycode.wcf.toDo.statistics' => 2
	));
}

<<<<<<< HEAD
$statement->execute(array(
	'de.mysterycode.wcf.ToDoCategoryPage',
	'com.woltlab.wcf.user.dashboardContainer',
	'de.mysterycode.wcf.toDo.outstanding'
));
=======
$statement->execute(array('de.mysterycode.wcf.ToDoCategoryPage', 'com.woltlab.wcf.user.dashboardContainer', 'de.mysterycode.wcf.toDo.outstanding'));
>>>>>>> master
$row = $statement->fetchArray();
if ($row === false) {
	// set default values for dashboard boxes
	DashboardHandler::setDefaultValues('de.mysterycode.wcf.ToDoCategoryPage', array(
		'de.mysterycode.wcf.toDo.outstanding' => 1
	));
}
<<<<<<< HEAD
$statement->execute(array(
	'de.mysterycode.wcf.ToDoCategoryPage',
	'com.woltlab.wcf.user.dashboardContainer',
	'de.mysterycode.wcf.toDo.statistics'
));
=======
$statement->execute(array('de.mysterycode.wcf.ToDoCategoryPage', 'com.woltlab.wcf.user.dashboardContainer', 'de.mysterycode.wcf.toDo.statistics'));
>>>>>>> master
$row = $statement->fetchArray();
if ($row === false) {
	// set default values for dashboard boxes
	DashboardHandler::setDefaultValues('de.mysterycode.wcf.ToDoCategoryPage', array(
		'de.mysterycode.wcf.toDo.statistics' => 2
	));
}
<<<<<<< HEAD
=======

// unset notification settings for creating todos - also needed when reinstalling
// 
$sql = "DELETE FROM	wcf".WCF_N."_user_notification_event_to_user
	WHERE		eventID = (SELECT eventID FROM wcf".WCF_N."_user_notification_event WHERE eventName = ? AND className = ?)";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array('create', 'wcf\\system\\user\\notification\\event\\ToDoCreateUserNotificationEvent'));
>>>>>>> master
