-- 
-- Script to create webreport_style table for Oracle database.
--

CREATE TABLE webreport_style (
  report_style_id NUMBER(11) PRIMARY KEY,
  "type" VARCHAR2(6) NULL,
  "field" NUMBER(11) NULL,
  "group" NUMBER(11) NULL,
  style_str CLOB  NULL,
  "uniq" NUMBER(11) NULL,
  repname VARCHAR2(255) NULL,
  styletype VARCHAR2(40) NULL)

-- $next

CREATE SEQUENCE S_webreport_style INCREMENT BY 1 START WITH 1 MINVALUE 0 NOCYCLE NOCACHE NOORDER

-- $next

CREATE OR REPLACE TRIGGER tr_webreport_style BEFORE INSERT ON webreport_style FOR EACH ROW
BEGIN
    Select S_webreport_style.NextVal into :new.report_style_id from dual; 
END;



