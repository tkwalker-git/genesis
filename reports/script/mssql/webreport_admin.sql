-- 
-- Script to create webreport_admin table for MS SQL database.
--

CREATE TABLE [webreport_admin] (
  [id] INT IDENTITY PRIMARY KEY,
  [tablename] NVARCHAR(250),
  [db_type] NVARCHAR(10),
  [group_name] NVARCHAR(250)
  );

  
