-- 
-- Script to create webreport_sql table for SQLite database.
--

CREATE TABLE webreport_sql (
  id integer PRIMARY KEY, 
  sqlname varchar(100), 
  sqlcontent text);

