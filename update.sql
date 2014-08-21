ALTER TABLE wcf1_todo		ADD	remembertime	bigint(20)	NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo		ADD	progress		int(3)		NOT NULL DEFAULT 0;

ALTER TABLE wcf1_todo		ADD	enableSmilies	tinyint(1)	NOT NULL DEFAULT 1;
ALTER TABLE wcf1_todo		ADD	enableHtml	tinyint(1)	NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo		ADD	enableBBCodes	tinyint(1)	NOT NULL DEFAULT 1;
ALTER TABLE wcf1_todo		ADD	enableComments	tinyint(1)	NOT NULL DEFAULT 1;
ALTER TABLE wcf1_todo		ADD	isDisabled	tinyint(1)	NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo		ADD	isDeleted	tinyint(1)	NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo		ADD	deleteTime	int(10)		NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo		ADD	deletedByID	int(10)		NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo		ADD	deletedBy	varchar(255)	NOT NULL DEFAULT '';
ALTER TABLE wcf1_todo		ADD	deleteReason	text;
ALTER TABLE wcf1_todo 		ADD	cumulativeLikes	MEDIUMINT(7)	NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo		ADD	username		varchar(255)	NOT NULL DEFAULT '';

ALTER TABLE wcf1_todo_to_user	ADD	username		varchar(255)	NOT NULL DEFAULT '';

ALTER TABLE wcf1_user		ADD	todos		int(10)		NOT NULL DEFAULT 0;