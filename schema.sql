-- SQL

DROP TABLE IF EXISTS feeds;

CREATE TABLE feeds(
	id int(11) NOT NULL AUTO_INCREMENT,
	location varchar(400) NOT NULL,
	PRIMARY KEY(id)
);