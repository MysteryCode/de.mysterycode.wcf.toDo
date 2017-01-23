<?php

use wcf\system\WCF;

/**
 * @author	Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
$package = $this->installation->getPackage();

try {
	$sql = "ALTER TABLE wcf" . WCF_N . "_todo ADD ipAddress varchar(39) NOT NULL DEFAULT '';";
	$statement = WCF::getDB()->prepareStatement($sql);
	$statement->execute();
	$sqlLog = "INSERT INTO wcf" . WCF_N . "_package_installation_sql_log (packageID, sqlTable, sqlColumn) VALUES (?, ?, ?)";
	$statement = WCF::getDB()->prepareStatement($sqlLog);
	$statement->execute([$package->packageID, 'wcf'.WCF_N.'_todo', 'ipAddress']);
}
catch (\Exception $e) {}
