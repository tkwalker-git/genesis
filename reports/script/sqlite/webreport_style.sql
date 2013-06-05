-- 
-- Script to create webreport_style table for SQLite database.
--
CREATE TABLE webreport_style (
  report_style_id integer PRIMARY KEY,
  type varchar(6),
  field integer,
  group integer,
  style_str text,
  uniq integer,
  repname varchar(255),
  styletype varchar(40));
  
