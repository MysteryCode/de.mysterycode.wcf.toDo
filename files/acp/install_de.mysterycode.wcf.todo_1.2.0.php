<?php
use wcf\system\dashboard\DashboardHandler;
use wcf\system\WCF;

/**
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
$package = $this->installation->getPackage();

// workaround because wcf doesn't prove
$sql = "SELECT *
	FROM		wcf" . WCF_N . "_dashboard_option
	WHERE		objectTypeID = (SELECT objectTypeID FROM wcf" . WCF_N . "_object_type WHERE objectType = ? AND definitionID = (SELECT definitionID FROM wcf" . WCF_N . "_object_type_definition WHERE definitionName = ?))
			AND boxID = (SELECT boxID FROM wcf" . WCF_N . "_dashboard_box WHERE boxName = ?)";
$statement = WCF::getDB()->prepareStatement($sql);

$statement->execute([
	'de.mysterycode.wcf.ToDoListPage',
	'com.woltlab.wcf.user.dashboardContainer',
	'de.mysterycode.wcf.toDo.outstanding'
]);
$row = $statement->fetchArray();
if ($row === false) {
	// set default values for dashboard boxes
	DashboardHandler::setDefaultValues('de.mysterycode.wcf.ToDoListPage', [
		'de.mysterycode.wcf.toDo.outstanding' => 1
	]);
}
$statement->execute([
	'de.mysterycode.wcf.ToDoListPage',
	'com.woltlab.wcf.user.dashboardContainer',
	'de.mysterycode.wcf.toDo.statistics'
]);
$row = $statement->fetchArray();
if ($row === false) {
	// set default values for dashboard boxes
	DashboardHandler::setDefaultValues('de.mysterycode.wcf.ToDoListPage', [
		'de.mysterycode.wcf.toDo.statistics' => 2
	]);
}

$statement->execute([
	'de.mysterycode.wcf.ToDoCategoryPage',
	'com.woltlab.wcf.user.dashboardContainer',
	'de.mysterycode.wcf.toDo.outstanding'
]);
$row = $statement->fetchArray();
if ($row === false) {
	// set default values for dashboard boxes
	DashboardHandler::setDefaultValues('de.mysterycode.wcf.ToDoCategoryPage', [
		'de.mysterycode.wcf.toDo.outstanding' => 1
	]);
}
$statement->execute([
	'de.mysterycode.wcf.ToDoCategoryPage',
	'com.woltlab.wcf.user.dashboardContainer',
	'de.mysterycode.wcf.toDo.statistics'
]);
$row = $statement->fetchArray();
if ($row === false) {
	// set default values for dashboard boxes
	DashboardHandler::setDefaultValues('de.mysterycode.wcf.ToDoCategoryPage', [
		'de.mysterycode.wcf.toDo.statistics' => 2
	]);
}
