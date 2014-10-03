ALTER TABLE wcf1_todo		ADD	attachments	MEDIUMINT(7)	NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo		ADD	FOREIGN KEY (category) REFERENCES wcf1_todo_category (id) ON DELETE CASCADE;
ALTER TABLE wcf1_todo		CHANGE	submitter	submitter	int(10);
UPDATE TABLE wcf1_todo		SET submitter = NULL	WHERE submitter = 0;
ALTER TABLE wcf1_todo		ADD	FOREIGN KEY (submitter) REFERENCES wcf1_user (userID) ON DELETE SET NULL;