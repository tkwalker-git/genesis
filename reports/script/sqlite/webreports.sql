-- 
-- Script to create webreports table for SQLite database.
--

CREATE TABLE webreports (
  rpt_id integer PRIMARY KEY, 
  rpt_name varchar(100), 
  rpt_title varchar(255), 
  rpt_cdate DATETIME, 
  rpt_mdate DATETIME, 
  rpt_content text, 
  rpt_owner varchar(100), 
  rpt_status varchar(10), 
  rpt_type varchar(10));

