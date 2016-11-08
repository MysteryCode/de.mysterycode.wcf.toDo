DROP TABLE IF EXISTS wcf1_todo;
CREATE TABLE wcf1_todo (
	todoID				int(10)			NOT NULL AUTO_INCREMENT,
	title				tinytext		NOT NULL DEFAULT '',
	description			text			NOT NULL DEFAULT '',
	note				text			NOT NULL DEFAULT '',
	statusID			int(10),
	submitter			int(10),
	username			varchar(255)	NOT NULL DEFAULT '',
	timestamp			int(10)			NOT NULL DEFAULT 0,
	endTime				int(10)			NOT NULL DEFAULT 0,
	private				int(1)			NOT NULL DEFAULT 0,
	comments			int(1)			NOT NULL DEFAULT 1,
	important			int(1)			NOT NULL DEFAULT 0,
	categoryID			int(10),
	updatetimestamp		int(10)			NOT NULL DEFAULT 0,
	progress			int(3)			NOT NULL DEFAULT 0,
	remembertime		int(10)			NOT NULL DEFAULT 0,
	enableSmilies		tinyint(1)		NOT NULL DEFAULT 1,
	enableHtml			tinyint(1)		NOT NULL DEFAULT 0,
	enableBBCodes		tinyint(1)		NOT NULL DEFAULT 1,
	enableComments		tinyint(1)		NOT NULL DEFAULT 1,
	isDisabled			tinyint(1)		NOT NULL DEFAULT 0,
	isDeleted			tinyint(1)		NOT NULL DEFAULT 0,
	deleteTime			int(10)			NOT NULL DEFAULT 0,
	deletedByID			int(10),
	deletedBy			varchar(255)	NOT NULL DEFAULT '',
	deleteReason		text,
	cumulativeLikes		mediumint(7)	NOT NULL DEFAULT 0,
	attachments			mediumint(7)	NOT NULL DEFAULT 0,
	hasEmbeddedObjects	tinyint(1)		NOT NULL DEFAULT 0,
	ipAddress			varchar(39)		NOT NULL DEFAULT 0,
	PRIMARY KEY (todoID)
);

DROP TABLE IF EXISTS wcf1_todo_status;
CREATE TABLE wcf1_todo_status (
	statusID			int(10)			NOT NULL AUTO_INCREMENT,
	subject				varchar(255)	NOT NULL DEFAULT '',
	description			text,
	showOrder			int(10)			NOT NULL DEFAULT 0,
	cssClass			varchar(255)	NOT NULL DEFAULT '',
	locked				tinyint(1)		NOT NULL DEFAULT 0,
	PRIMARY KEY (statusID)
);

DROP TABLE IF EXISTS wcf1_todo_to_user;
CREATE TABLE wcf1_todo_to_user (
	assignID			int(10)			NOT NULL AUTO_INCREMENT,
	todoID				int(10),
	userID				int(10),
	username			varchar(255)	NOT NULL DEFAULT '',
	PRIMARY KEY (assignID)
);

DROP TABLE IF EXISTS wcf1_todo_to_group;
CREATE TABLE wcf1_todo_to_group (
	assignID			int(10)			NOT NULL AUTO_INCREMENT,
	todoID				int(10),
	groupID				int(10),
	groupname			varchar(255)	NOT NULL DEFAULT '',
	PRIMARY KEY (assignID)
);

INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (1, 'abgeschlossen/solved', 99, 'green', 1);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (2, 'offen/unsolved', 2, 'red', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (3, 'in Arbeit/in progress',3, 'yellow', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (4, 'verworfen/canceled', 98, 'gray', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (5, 'in Vorbereitung/in preparation', 1, 'gray', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (6, 'pausiert/paused', 4, 'gray', 0);

ALTER TABLE wcf1_user ADD todos int(10) NOT NULL DEFAULT 0;

ALTER TABLE wcf1_todo ADD FOREIGN KEY (submitter) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE wcf1_todo ADD FOREIGN KEY (deletedByID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE wcf1_todo ADD FOREIGN KEY (statusID) REFERENCES wcf1_todo_status (statusID) ON DELETE SET NULL;
ALTER TABLE wcf1_todo ADD FOREIGN KEY (categoryID) REFERENCES wcf1_category (categoryID) ON DELETE CASCADE;

ALTER TABLE wcf1_todo_to_user ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE wcf1_todo_to_user ADD FOREIGN KEY (todoID) REFERENCES wcf1_todo (todoID) ON DELETE CASCADE;

ALTER TABLE wcf1_todo_to_group ADD FOREIGN KEY (groupID) REFERENCES wcf1_user_group (groupID) ON DELETE CASCADE;
ALTER TABLE wcf1_todo_to_group ADD FOREIGN KEY (todoID) REFERENCES wcf1_todo (todoID) ON DELETE CASCADE;
