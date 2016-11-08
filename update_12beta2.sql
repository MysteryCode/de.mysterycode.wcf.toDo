ALTER TABLE wcf1_todo CHANGE title title tinytext NOT NULL DEFAULT '';
ALTER TABLE wcf1_todo CHANGE description description text NOT NULL DEFAULT '';
ALTER TABLE wcf1_todo CHANGE note note text NOT NULL DEFAULT '';
ALTER TABLE wcf1_todo ADD ipAddress varchar(39) NOT NULL DEFAULT 0;

ALTER TABLE wcf1_todo DROP html_description;
ALTER TABLE wcf1_todo DROP html_notes;
