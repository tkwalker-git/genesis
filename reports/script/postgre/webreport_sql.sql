-- 
-- Script to create webreport_sql table for Postgre database.
--

CREATE TABLE "webreport_sql" ( 
  "id" SERIAL PRIMARY KEY, 
  "sqlname" VARCHAR(100) NOT NULL, 
  "sqlcontent" TEXT NOT NULL);

