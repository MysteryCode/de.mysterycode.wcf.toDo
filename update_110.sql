ALTER TABLE wcf1_todo		CHANGE	submitter	submitter	int(10);
ALTER TABLE wcf1_todo		ADD	FOREIGN KEY (submitter) REFERENCES wcf1_user (userID) ON DELETE SET NULL;