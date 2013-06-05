-- 
-- Script to create webreports table for MS Access database.
--

CREATE TABLE [webreports] (
  rpt_id COUNTER PRIMARY KEY, 
  rpt_name TEXT(100), 
  rpt_title TEXT(255), 
  rpt_cdate DATETIME, 
  rpt_mdate DATETIME, 
  rpt_content MEMO, 
  rpt_owner TEXT(100), 
  rpt_status TEXT(10), 
  rpt_type TEXT(10));

