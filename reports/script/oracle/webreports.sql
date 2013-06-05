-- 
-- Script to create webreports table for Oracle database.
--

CREATE TABLE webreports (
  rpt_id NUMBER(10) PRIMARY KEY, 
  rpt_name VARCHAR2(100)  NULL, 
  rpt_title VARCHAR2(500) NULL, 
  rpt_cdate DATE NOT NULL, 
  rpt_mdate DATE NULL,
  rpt_content CLOB NOT NULL, 
  rpt_owner VARCHAR2(100) NULL, 
  rpt_status VARCHAR2(10) NULL, 
  rpt_type VARCHAR2(10) NOT NULL)

-- $next

CREATE SEQUENCE S_webreports INCREMENT BY 1 START WITH 1 MINVALUE 0 NOCYCLE NOCACHE NOORDER

-- $next

CREATE OR REPLACE TRIGGER tr_webreports BEFORE INSERT ON webreports FOR EACH ROW
BEGIN
    Select S_webreports.NextVal into :new.rpt_id from dual; 
END;

