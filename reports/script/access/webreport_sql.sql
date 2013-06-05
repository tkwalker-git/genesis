-- 
-- Script to create webreport_sql table for MS Access database.
--

CREATE TABLE [webreport_sql] (
	[id] COUNTER PRIMARY KEY, 
	[sqlname] TEXT(100), 
	[sqlcontent] MEMO);