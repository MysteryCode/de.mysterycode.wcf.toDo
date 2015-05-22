DROP TABLE IF EXISTS wcf1_todo;
CREATE TABLE wcf1_todo (
	id			int(10)		NOT NULL AUTO_INCREMENT,
	title			tinytext		NOT NULL,
	description		text		NOT NULL,
	note			text		NOT NULL,
	statusID			int(10),
	submitter		int(10),
	username			varchar(255)	NOT NULL DEFAULT '',
	timestamp		int(10)		NOT NULL,
	endTime			int(10)		NOT NULL,
	private			int(1)		NOT NULL DEFAULT 0,
	comments			int(1)		NOT NULL DEFAULT 1,
	important		int(1)		NOT NULL DEFAULT 0,
	categoryID		int(10),
	updatetimestamp		int(10)		NOT NULL DEFAULT 0,
	progress			int(3)		NOT NULL DEFAULT 0,
	html_description		int(1)		NOT NULL DEFAULT 0,
	html_notes		int(1)		NOT NULL DEFAULT 0,
	remembertime		int(10)		NOT NULL DEFAULT 0,
	enableSmilies		tinyint(1)	NOT NULL DEFAULT 1,
	enableHtml		tinyint(1)	NOT NULL DEFAULT 0,
	enableBBCodes		tinyint(1)	NOT NULL DEFAULT 1,
	enableComments		tinyint(1)	NOT NULL DEFAULT 1,
	isDisabled		tinyint(1)	NOT NULL DEFAULT 0,
	isDeleted		tinyint(1)	NOT NULL DEFAULT 0,
	deleteTime		int(10)		NOT NULL DEFAULT 0,
	deletedByID		int(10),
	deletedBy		varchar(255)	NOT NULL DEFAULT '',
	deleteReason		text,
	cumulativeLikes		mediumint(7)	NOT NULL DEFAULT 0,
	attachments		mediumint(7)	NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
);

DROP TABLE IF EXISTS wcf1_todo_status;
CREATE TABLE wcf1_todo_status (
	statusID			int(10)		NOT NULL AUTO_INCREMENT,
	subject			varchar(255)	NOT NULL	DEFAULT '',
	description		text,
	showOrder		int(10)		NOT NULL	DEFAULT 0,
	cssClass			varchar(255)	NOT NULL	DEFAULT '',
	PRIMARY KEY (statusID)
);

DROP TABLE IF EXISTS wcf1_todo_to_user;
CREATE TABLE wcf1_todo_to_user (
	toDoID			bigint(20)	NOT NULL,
	userID			bigint(20)	NOT NULL DEFAULT 0,
	username			varchar(255)	NOT NULL DEFAULT '',
	PRIMARY KEY (toDoID,userID)
);

INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass) VALUES (1, 'abgeschlossen', 99, 'green');

ALTER TABLE wcf1_user ADD todos int(10) NOT NULL DEFAULT 0;

ALTER TABLE wcf1_todo ADD FOREIGN KEY (submitter) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE wcf1_todo ADD FOREIGN KEY (deletedByID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE wcf1_todo ADD FOREIGN KEY (statusID) REFERENCES wcf1_todo_status (statusID) ON DELETE SET NULL;
