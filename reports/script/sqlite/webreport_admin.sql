-- 
-- Script to create webreport_admin table for SQLite database.
--

CREATE TABLE webreport_admin (
  id integer primary key,
  tablename varchar(250),
  db_type varchar(10),
  group_name varchar(250)
  );

  
