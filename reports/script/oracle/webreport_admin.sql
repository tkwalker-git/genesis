-- 
-- Script to create webreport_admin table for Oracle database.
--

CREATE TABLE webreport_admin (
  id NUMBER(11) PRIMARY KEY,
  tablename VARCHAR2(250),
  db_type VARCHAR2(10),
  group_name VARCHAR2(250)
  )

  
-- $next

CREATE SEQUENCE S_webreport_admin INCREMENT BY 1 START WITH 1 MINVALUE 0 NOCYCLE NOCACHE NOORDER

-- $next

CREATE OR REPLACE TRIGGER tr_webreport_admin BEFORE INSERT ON webreport_admin FOR EACH ROW
BEGIN
    Select S_webreport_admin.NextVal into :new.id from dual; 
END;


