-- 
-- Script to create webreport_settings table for MS SQL database.
--

CREATE TABLE [webreport_settings] (
  [id] INT IDENTITY PRIMARY KEY,
  [version] NVARCHAR(10)
  );

-- $next
  
insert into [webreport_settings] ([version]) values ('1.3');  
