<?php
use wcf\system\WCF;

/**
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
$package = $this->installation->getPackage();

$sql[] = "ALTER TABLE wcf".WCF_N."_todo ADD statusID int(10);";
$sql[] = "ALTER TABLE wcf".WCF_N."_todo ADD categoryID int(10);";
$sql[] = "ALTER TABLE wcf".WCF_N."_todo ADD hasEmbeddedObjects tinyint(1) NOT NULL DEFAULT 0;";
$sql[] = "ALTER TABLE wcf".WCF_N."_todo ADD ipAddress varchar(39) NOT NULL DEFAULT '';";
$sql[] = "ALTER TABLE wcf".WCF_N."_todo CHANGE id todoID int(10) AUTO_INCREMENT;";

foreach ($sql as $sqlStatement) {
	try {
		$statement = WCF::getDB()->prepareStatement($sqlStatement);
		$statement->execute();
		
		$matches = array();
		preg_match("/wcf".WCF_N."_todo ADD ([A-za-z0-9]+) /", $sqlStatement, $matches);
		
		if (!empty($matches[1])) {
			$sqlLog = "INSERT INTO wcf" . WCF_N . "_package_installation_sql_log (packageID, sqlTable, sqlColumn) VALUES (?, ?, ?)";
			$statement = WCF::getDB()->prepareStatement($sqlLog);
			$statement->execute(array($package->packageID, 'wcf".WCF_N."_todo', $matches[1]));
		}
	}
	catch (\Exception $e) {}
}
