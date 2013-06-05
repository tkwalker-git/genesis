-- 
-- Script to create webreport_settings table for Postgre database.
--

CREATE TABLE "webreport_settings" (
  "id" SERIAL PRIMARY KEY,
  "version" VARCHAR(10)
  );

-- $next
  
insert into "webreport_settings" ("version") values ('1.3');  
