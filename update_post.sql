ALTER TABLE wcf1_todo DROP FOREIGN KEY category;
ALTER TABLE wcf1_todo DROP category;
ALTER TABLE wcf1_todo DROP status;

DROP TABLE IF EXISTS wcf1_todo_category;
