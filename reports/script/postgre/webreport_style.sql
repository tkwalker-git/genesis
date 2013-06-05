-- 
-- Script to create webreport_style table for Postgre database.
--

CREATE TABLE "webreport_style" (
  "report_style_id" SERIAL PRIMARY KEY,
  "type" VARCHAR(6) NOT NULL,
  "field" INTEGER NOT NULL,
  "group" INTEGER NOT NULL,
  "style_str" TEXT NOT NULL,
  "uniq" INTEGER NULL,
  "repname" VARCHAR(255) NOT NULL,
  "styletype" VARCHAR(40) NOT NULL);
  
