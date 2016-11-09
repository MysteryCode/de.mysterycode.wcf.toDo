ALTER TABLE wcf1_todo CHANGE title title varchar(255) NOT NULL DEFAULT '';
ALTER TABLE wcf1_todo CHANGE description description text;
ALTER TABLE wcf1_todo CHANGE note note text;
ALTER TABLE wcf1_todo ADD ipAddress varchar(39) NOT NULL DEFAULT '';

ALTER TABLE wcf1_todo DROP html_description;
ALTER TABLE wcf1_todo DROP html_notes;
