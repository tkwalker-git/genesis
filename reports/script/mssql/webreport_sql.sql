-- 
-- Script to create webreport_sql table for MS SQL.
--

CREATE TABLE [webreport_sql] ( 
  [id] INT IDENTITY PRIMARY KEY, 
  [sqlname]NVARCHAR(100) NOT NULL,
  [sqlcontent] TEXT NOT NULL);

