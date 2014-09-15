DROP TABLE IF EXISTS wcf1_todo;
CREATE TABLE wcf1_todo (
	id			bigint(20)	NOT NULL AUTO_INCREMENT,
	title			tinytext		NOT NULL,
	description		text		NOT NULL,
	note			text		NOT NULL,
	status			int(1)		NOT NULL DEFAULT 0,
	submitter		bigint(20)	NOT NULL DEFAULT 0,
	username			varchar(255)	NOT NULL DEFAULT '',
	timestamp		bigint(20)	NOT NULL,
	endTime			bigint(20)	NOT NULL,
	private			int(1)		NOT NULL DEFAULT 0,
	comments			int(1)		NOT NULL DEFAULT 1,
	important		int(1)		NOT NULL DEFAULT 0,
	category			bigint(20)	NOT NULL DEFAULT 1,
	updatetimestamp		bigint(20)	NOT NULL DEFAULT 0,
	progress			int(3)		NOT NULL DEFAULT 0,
	html_description		int(1)		NOT NULL DEFAULT 0,
	html_notes		int(1)		NOT NULL DEFAULT 0,
	remembertime		bigint(20)	NOT NULL DEFAULT 0,
	enableSmilies		tinyint(1)	NOT NULL DEFAULT 1,
	enableHtml		tinyint(1)	NOT NULL DEFAULT 0,
	enableBBCodes		tinyint(1)	NOT NULL DEFAULT 1,
	enableComments		tinyint(1)	NOT NULL DEFAULT 1,
	isDisabled		tinyint(1)	NOT NULL DEFAULT 0,
	isDeleted		tinyint(1)	NOT NULL DEFAULT 0,
	deleteTime		int(10)		NOT NULL DEFAULT 0,
	deletedByID		int(10)		NOT NULL DEFAULT 0,
	deletedBy		varchar(255)	NOT NULL DEFAULT '',
	deleteReason		text,
	cumulativeLikes		MEDIUMINT(7)	NOT NULL DEFAULT 0,
	attachments		MEDIUMINT(7)	NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
);

DROP TABLE IF EXISTS wcf1_todo_category;
CREATE TABLE wcf1_todo_category (
	id			bigint(20)	NOT NULL AUTO_INCREMENT,
	title			tinytext		NOT NULL,
	color			varchar(255)	NOT NULL DEFAULT 'rgba(150,150,150,1)',
	PRIMARY KEY (id)
);

DROP TABLE IF EXISTS wcf1_todo_to_user;
CREATE TABLE wcf1_todo_to_user (
	toDoID			bigint(20)	NOT NULL,
	userID			bigint(20)	NOT NULL DEFAULT 0,
	username			varchar(255)	NOT NULL DEFAULT '',
	PRIMARY KEY (toDoID,userID)
);

ALTER TABLE wcf1_user ADD todos int(10) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo ADD FOREIGN KEY (category) REFERENCES wcf1_todo_category (id) ON DELETE CASCADE;