ALTER TABLE wcf1_todo DROP FOREIGN KEY category;
ALTER TABLE wcf1_todo DROP category;
ALTER TABLE wcf1_todo DROP status;
ALTER TABLE wcf1_todo DROP html_description;
ALTER TABLE wcf1_todo DROP html_notes;

DROP TABLE IF EXISTS wcf1_todo_category;
