<?php
use wcf\system\WCF;

/**
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
$sql[] = "ALTER TABLE wcf1_todo_to_user DROP PRIMARY KEY;";
$sql[] = "ALTER TABLE wcf1_todo_to_user ADD assignID int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;";
$sql[] = "ALTER TABLE wcf1_todo_to_user CHANGE toDoID todoID int(10);";
$sql[] = "CREATE TABLE wcf1_todo_status (
	statusID			int(10)			NOT NULL AUTO_INCREMENT,
	subject				varchar(255)	NOT NULL	DEFAULT '',
	description			text,
	showOrder			int(10)			NOT NULL	DEFAULT 0,
	cssClass			varchar(255)	NOT NULL	DEFAULT '',
	locked				tinyint(1)		NOT NULL	DEFAULT 0,
	PRIMARY KEY (statusID)
);";
$sql[] = "CREATE TABLE wcf1_todo_to_group (
	assignID			int(10)			NOT NULL AUTO_INCREMENT,
	todoID			int(10),
	groupID			int(10),
	groupname		varchar(255)		NOT NULL DEFAULT '',
	PRIMARY KEY (assignID)
);";
$sql[] = "INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (1, 'abgeschlossen/solved', 99, 'green', 1);";
$sql[] = "INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (2, 'offen/unsolved', 2, 'red', 0);";
$sql[] = "INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (3, 'in Arbeit/in progress',3, 'yellow', 0);";
$sql[] = "INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (4, 'verworfen/canceled', 98, 'gray', 0);";
$sql[] = "INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (5, 'in Vorbereitung/in preparation', 1, 'gray', 0);";
$sql[] = "INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (6, 'pausiert/paused', 4, 'gray', 0);";
$sql[] = "ALTER TABLE wcf1_todo ADD FOREIGN KEY (statusID) REFERENCES wcf1_todo_status (statusID) ON DELETE SET NULL;";
$sql[] = "ALTER TABLE wcf1_todo ADD FOREIGN KEY (categoryID) REFERENCES wcf1_category (categoryID) ON DELETE CASCADE;";
$sql[] = "ALTER TABLE wcf1_todo_to_user ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;";
$sql[] = "ALTER TABLE wcf1_todo_to_user ADD FOREIGN KEY (todoID) REFERENCES wcf1_todo (todoID) ON DELETE CASCADE";
$sql[] = "ALTER TABLE wcf1_todo_to_group ADD FOREIGN KEY (groupID) REFERENCES wcf1_user_group (groupID) ON DELETE CASCADE;";
$sql[] = "ALTER TABLE wcf1_todo_to_group ADD FOREIGN KEY (todoID) REFERENCES wcf1_todo (todoID) ON DELETE CASCADE;";
$sql[] = "ALTER TABLE wcf1_todo DROP FOREIGN KEY category;";
$sql[] = "ALTER TABLE wcf1_todo DROP category;";
$sql[] = "ALTER TABLE wcf1_todo DROP status;";
$sql[] = "ALTER TABLE wcf1_todo DROP html_description;";
$sql[] = "ALTER TABLE wcf1_todo DROP html_notes;";
$sql[] = "DROP TABLE IF EXISTS wcf1_todo_category;";

foreach ($sql as $sqlStatement) {
	try {
		$statement = WCF::getDB()->prepareStatement($sqlStatement);
		$statement->execute();
	}
	catch (\Exception $e) {}
}
