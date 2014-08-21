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
