-- 
-- Script to create webreport_users table for MS Access database.
--

CREATE TABLE [webreport_users] (
  [id] COUNTER PRIMARY KEY,
  [username] TEXT(200),
  [password] TEXT(200),
  [email] TEXT(200)
  );
