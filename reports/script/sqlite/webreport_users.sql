-- 
-- Script to create webreport_users table for SQLite database.
--

CREATE TABLE webreport_users (
  id integer PRIMARY KEY,
  username varchar(200),
  password varchar(200),
  email varchar(200)
  );
