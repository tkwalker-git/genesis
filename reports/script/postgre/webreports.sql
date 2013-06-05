-- 
-- Script to create webreports table for Postgre database.
--

CREATE TABLE "webreports" ( 
  rpt_id SERIAL PRIMARY KEY, 
  rpt_name VARCHAR(100) NOT NULL, 
  rpt_title VARCHAR(500) NULL, 
  rpt_cdate TIMESTAMP NOT NULL, 
  rpt_mdate TIMESTAMP NULL, 
  rpt_content TEXT NOT NULL, 
  rpt_owner VARCHAR(100) NOT NULL, 
  rpt_status VARCHAR(10) NOT NULL, 
  rpt_type VARCHAR(10) NOT NULL);

