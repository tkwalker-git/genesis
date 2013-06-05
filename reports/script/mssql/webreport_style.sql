-- 
-- Script to create webreport_style table for MS SQL.

CREATE TABLE [webreport_style] (
  [report_style_id] INT IDENTITY PRIMARY KEY,
  [type] NVARCHAR(6) NOT NULL,
  [field] INT NOT NULL,
  [group] INT NOT NULL,
  [style_str] TEXT NOT NULL,
  [uniq] INT DEFAULT NULL,
  [repname] NVARCHAR(255) NOT NULL,
  [styletype] NVARCHAR(40) NOT NULL);
