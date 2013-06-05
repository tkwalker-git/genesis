-- 
-- Script to create webreport_admin table for Postgre database.
--

CREATE TABLE "webreport_admin" (
  "id" INT(11) SERIAL PRIMARY KEY,
  "tablename" VARCHAR(250),
  "db_type" VARCHAR(10),
  "group_name" VARCHAR(250)
  );

  
