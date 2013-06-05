-- 
-- Script to create webreport_settings table for MS Access database.
--

CREATE TABLE [webreport_settings] (
  [id] COUNTER PRIMARY KEY,
  [version] TEXT(10)
  );

-- $next
  
insert into [webreport_settings] ([version]) values ('1.3');