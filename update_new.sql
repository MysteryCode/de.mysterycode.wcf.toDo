ALTER TABLE wcf1_todo		ADD	cumulativeLikes	MEDIUMINT(7)	NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo		ADD	username		varchar(255)	NOT NULL DEFAULT '';
ALTER TABLE wcf1_todo_to_user	ADD	username		varchar(255)	NOT NULL DEFAULT '';