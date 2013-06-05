-- 
-- Script to create webreports table for MS SQL.
--

CREATE TABLE [webreports] ( 
  rpt_id INT IDENTITY PRIMARY KEY, 
  rpt_name NVARCHAR(100) NOT NULL,
  rpt_title NVARCHAR(500) NULL, 
  rpt_cdate DATETIME NOT NULL, 
  rpt_mdate DATETIME NULL, 
  rpt_content TEXT NOT NULL, 
  rpt_owner VARCHAR(100) NOT NULL, 
  rpt_status VARCHAR(10) NOT NULL, 
  rpt_type VARCHAR(10) NOT NULL);

