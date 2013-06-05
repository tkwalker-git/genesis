-- 
-- Script to create webreports table for Firebird database.
--

CREATE TABLE webreports (
  rpt_id integer not null PRIMARY KEY, 
  rpt_name VARCHAR(100), 
  rpt_title VARCHAR(500), 
  rpt_cdate TIMESTAMP NOT NULL, 
  rpt_mdate TIMESTAMP,
  rpt_content BLOB SUB_TYPE TEXT, 
  rpt_owner VARCHAR(100), 
  rpt_status VARCHAR(10), 
  rpt_type VARCHAR(10) NOT NULL)

-- $next

CREATE GENERATOR g_webreports

-- $next

CREATE TRIGGER t_webreports FOR webreports
BEFORE INSERT
AS
BEGIN
  IF (NEW.rpt_id IS NULL)
    THEN NEW.rpt_id = GEN_ID(g_webreports, 1);
END


