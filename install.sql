DROP TABLE IF EXISTS wcf1_todo;
CREATE TABLE wcf1_todo (
	todoID             INT(10)      NOT NULL AUTO_INCREMENT,
	title              VARCHAR(255) NOT NULL DEFAULT '',
	description        TEXT,
	note               TEXT,
	statusID           INT(10),
	submitter          INT(10),
	username           VARCHAR(255) NOT NULL DEFAULT '',
	time               INT(10)      NOT NULL DEFAULT 0,
	endTime            INT(10)      NOT NULL DEFAULT 0,
	private            INT(1)       NOT NULL DEFAULT 0,
	comments           INT(1)       NOT NULL DEFAULT 1,
	important          INT(1)       NOT NULL DEFAULT 0,
	categoryID         INT(10),
	updatetimestamp    INT(10)      NOT NULL DEFAULT 0,
	progress           INT(3)       NOT NULL DEFAULT 0,
	remembertime       INT(10)      NOT NULL DEFAULT 0,
	enableComments     TINYINT(1)   NOT NULL DEFAULT 1,
	isDisabled         TINYINT(1)   NOT NULL DEFAULT 0,
	isDeleted          TINYINT(1)   NOT NULL DEFAULT 0,
	deleteTime         INT(10)      NOT NULL DEFAULT 0,
	deletedByID        INT(10),
	deletedBy          VARCHAR(255) NOT NULL DEFAULT '',
	deleteReason       TEXT,
	cumulativeLikes    MEDIUMINT(7) NOT NULL DEFAULT 0,
	attachments        MEDIUMINT(7) NOT NULL DEFAULT 0,
	hasEmbeddedObjects TINYINT(1)   NOT NULL DEFAULT 0,
	notesHasEmbeddedObjects TINYINT(1)   NOT NULL DEFAULT 0,
	ipAddress          VARCHAR(39)  NOT NULL DEFAULT '',
	hasLabels          TINYINT(1)   NOT NULL DEFAULT 0,
	PRIMARY KEY (todoID)
);

DROP TABLE IF EXISTS wcf1_todo_status;
CREATE TABLE wcf1_todo_status (
	statusID    INT(10)      NOT NULL AUTO_INCREMENT,
	subject     VARCHAR(255) NOT NULL DEFAULT '',
	description TEXT,
	showOrder   INT(10)      NOT NULL DEFAULT 0,
	cssClass    VARCHAR(255) NOT NULL DEFAULT '',
	locked      TINYINT(1)   NOT NULL DEFAULT 0,
	PRIMARY KEY (statusID)
);

DROP TABLE IF EXISTS wcf1_todo_to_user;
CREATE TABLE wcf1_todo_to_user (
	assignID INT(10)      NOT NULL AUTO_INCREMENT,
	todoID   INT(10),
	userID   INT(10),
	username VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (assignID)
);

DROP TABLE IF EXISTS wcf1_todo_to_group;
CREATE TABLE wcf1_todo_to_group (
	assignID  INT(10)      NOT NULL AUTO_INCREMENT,
	todoID    INT(10),
	groupID   INT(10),
	groupname VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (assignID)
);

INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked)
VALUES (1, 'abgeschlossen/solved', 99, 'green', 1);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (2, 'offen/unsolved', 2, 'red', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked)
VALUES (3, 'in Arbeit/in progress', 3, 'yellow', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked)
VALUES (4, 'verworfen/canceled', 98, 'gray', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked)
VALUES (5, 'in Vorbereitung/in preparation', 1, 'gray', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked)
VALUES (6, 'pausiert/paused', 4, 'gray', 0);

ALTER TABLE wcf1_user
	ADD todos INT(10) NOT NULL DEFAULT 0;

ALTER TABLE wcf1_todo
	ADD FOREIGN KEY (submitter) REFERENCES wcf1_user (userID)
	ON DELETE SET NULL;
ALTER TABLE wcf1_todo
	ADD FOREIGN KEY (deletedByID) REFERENCES wcf1_user (userID)
	ON DELETE SET NULL;
ALTER TABLE wcf1_todo
	ADD FOREIGN KEY (statusID) REFERENCES wcf1_todo_status (statusID)
	ON DELETE SET NULL;
ALTER TABLE wcf1_todo
	ADD FOREIGN KEY (categoryID) REFERENCES wcf1_category (categoryID)
	ON DELETE CASCADE;

ALTER TABLE wcf1_todo_to_user
	ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID)
	ON DELETE CASCADE;
ALTER TABLE wcf1_todo_to_user
	ADD FOREIGN KEY (todoID) REFERENCES wcf1_todo (todoID)
	ON DELETE CASCADE;

ALTER TABLE wcf1_todo_to_group
	ADD FOREIGN KEY (groupID) REFERENCES wcf1_user_group (groupID)
	ON DELETE CASCADE;
ALTER TABLE wcf1_todo_to_group
	ADD FOREIGN KEY (todoID) REFERENCES wcf1_todo (todoID)
	ON DELETE CASCADE;
