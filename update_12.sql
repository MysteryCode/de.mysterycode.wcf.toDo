ALTER TABLE wcf1_todo CHANGE timestamp timestamp int(10) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo CHANGE endTime endTime int(10) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo CHANGE updatetimestamp updatetimestamp int(10) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo CHANGE remembertime remembertime int(10) NOT NULL DEFAULT 0;

ALTER TABLE wcf1_todo CHANGE title title varchar(255) NOT NULL DEFAULT '';
ALTER TABLE wcf1_todo CHANGE description description text;
ALTER TABLE wcf1_todo CHANGE note note text;

ALTER TABLE wcf1_todo_to_user CHANGE userID userID int(10);

DELETE FROM wcf1_todo_to_user WHERE userID = 0 OR userID IS NULL;
DELETE FROM wcf1_todo_to_user WHERE userID NOT IN (SELECT userID FROM wcf1_user WHERE userID IS NOT NULL);
DELETE FROM wcf1_todo_to_user WHERE todoID = 0 OR todoID IS NULL;
DELETE FROM wcf1_todo_to_user WHERE todoID NOT IN (SELECT todoID FROM wcf1_todo WHERE todoID IS NOT NULL);
