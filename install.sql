DROP TABLE IF EXISTS wcf1_todo;
CREATE TABLE wcf1_todo (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  title tinytext NOT NULL,
  description text NOT NULL,
  note text NOT NULL,
  status int(1) NOT NULL DEFAULT 0,
  submitter bigint(20) NOT NULL,
  timestamp bigint(20) NOT NULL,
  endTime bigint(20) NOT NULL,
  private int(1) NOT NULL DEFAULT 0,
  comments int(1) NOT NULL DEFAULT 1,
  important int(1) NOT NULL DEFAULT 0,
  category bigint(20) NOT NULL DEFAULT 1,
  updatetimestamp bigint(20) NOT NULL DEFAULT 0,
  progress int(3) NOT NULL DEFAULT 0,
  html_description int(1) NOT NULL DEFAULT 0,
  html_notes int(1) NOT NULL DEFAULT 0,
  remembertime bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS wcf1_todo_category;
CREATE TABLE wcf1_todo_category (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  title tinytext NOT NULL,
  color varchar(255) NOT NULL DEFAULT 'blue',
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS wcf1_todo_to_user;
CREATE TABLE wcf1_todo_to_user (
  toDoID bigint(20) NOT NULL,
  userID bigint(20) NOT NULL,
  PRIMARY KEY (toDoID,userID)
);

ALTER TABLE wcf1_user ADD todos int(10) NOT NULL DEFAULT 0;