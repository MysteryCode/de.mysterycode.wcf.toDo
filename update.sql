ALTER TABLE wcf1_todo		CHANGE	deletedByID	deletedByID	int(10);
ALTER TABLE wcf1_todo ADD FOREIGN KEY (deletedByID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;