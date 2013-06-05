-- 
-- Script to create webreport_settings table for SQLite database.
--

CREATE TABLE webreport_settings (
  id integer PRIMARY KEY,
  version varchar(10)
  );

-- $next
  
insert into webreport_settings(version) values('1.3');