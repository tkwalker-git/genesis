-- 
-- Script to create webreport_users table for MS SQL database.
--

CREATE TABLE [webreport_users] (
  [id] INT IDENTITY PRIMARY KEY,
  [username] NVARCHAR(200),
  [password] NVARCHAR(200),
  [email] NVARCHAR(200)
  );
