ALTER TABLE	wcf1_todo		CHANGE	deletedByID	deletedByID	int(10);
UPDATE	 	wcf1_todo		SET	deletedByID = NULL	WHERE	deletedByID = 0;
ALTER TABLE wcf1_todo ADD FOREIGN KEY (deletedByID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;