-- 
-- Script to create webreport_style table for MS Access database.
--
CREATE TABLE [webreport_style] (
  [report_style_id] COUNTER PRIMARY KEY,
  [type] TEXT(6),
  [field] INT,
  [group] INT,
  [style_str] MEMO,
  [uniq] INT,
  [repname] TEXT(255),
  [styletype] TEXT(40));
  
