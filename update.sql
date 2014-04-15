ALTER TABLE wcf1_todo ADD remembertime bigint(20) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo ADD progress int(3) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo ADD html_description int(1) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_todo ADD html_notes int(1) NOT NULL DEFAULT 0;

ALTER TABLE wcf1_user ADD todos int(10) NOT NULL DEFAULT 0;