ALTER TABLE wcf1_todo ADD statusID int(10);
ALTER TABLE wcf1_todo ADD categoryID int(10);

ALTER TABLE wcf1_todo CHANGE id todoID int(10);
ALTER TABLE wcf1_todo CHANGE timestamp timestamp int(10) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo CHANGE endTime endTime int(10) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo CHANGE updatetimestamp updatetimestamp int(10) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo CHANGE remembertime remembertime int(10) NOT NULL DEFAULT 0;

ALTER TABLE wcf1_todo_to_user ADD assignID int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE wcf1_todo_to_user CHANGE toDoID todoID int(10);
ALTER TABLE wcf1_todo_to_user CHANGE userID userID int(10);

DELETE FROM wcf1_todo_to_user WHERE userID = 0 OR userID IS NULL;
DELETE FROM wcf1_todo_to_user WHERE userID NOT IN (SELECT userID FROM wcf1_user WHERE userID IS NOT NULL);
DELETE FROM wcf1_todo_to_user WHERE todoID = 0 OR todoID IS NULL;
DELETE FROM wcf1_todo_to_user WHERE todoID NOT IN (SELECT todoID FROM wcf1_todo WHERE todoID IS NOT NULL);

DROP TABLE IF EXISTS wcf1_todo_status;
CREATE TABLE wcf1_todo_status (
	statusID			int(10)		NOT NULL AUTO_INCREMENT,
	subject			varchar(255)	NOT NULL	DEFAULT '',
	description		text,
	showOrder		int(10)		NOT NULL	DEFAULT 0,
	cssClass			varchar(255)	NOT NULL	DEFAULT '',
	locked			tinyint(1)	NOT NULL	DEFAULT 0,
	PRIMARY KEY (statusID)
);


INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (1, 'abgeschlossen/solved', 99, 'green', 1);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (2, 'offen/unsolved', 2, 'green', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (3, 'in Arbeit/in progress',3, 'green', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (4, 'verworfen/canceled', 98, 'green', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (5, 'in Vorbereitung/in preparation', 1, 'green', 0);
INSERT INTO wcf1_todo_status (statusID, subject, showOrder, cssClass, locked) VALUES (6, 'pausiert/paused', 4, 'green', 0);

ALTER TABLE wcf1_todo ADD FOREIGN KEY (statusID) REFERENCES wcf1_todo_status (statusID) ON DELETE SET NULL;
ALTER TABLE wcf1_todo ADD FOREIGN KEY (categoryID) REFERENCES wcf1_category (categoryID) ON DELETE CASCADE;

ALTER TABLE wcf1_todo_to_user ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE wcf1_todo_to_user ADD FOREIGN KEY (todoID) REFERENCES wcf1_todo (todoID) ON DELETE CASCADE
