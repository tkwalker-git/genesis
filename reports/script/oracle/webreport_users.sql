-- 
-- Script to create webreport_users table for Oracle database.
--

CREATE TABLE "webreport_users" (
  "id" NUMBER(11) PRIMARY KEY,
  "username" VARCHAR2(200),
  "password" VARCHAR2(200),
  "email" VARCHAR2(200)
  )

  
-- $next

CREATE SEQUENCE "S_webreport_users" INCREMENT BY 1 START WITH 1 MINVALUE 0 NOCYCLE NOCACHE NOORDER

-- $next

CREATE OR REPLACE TRIGGER "tr_webreport_users" BEFORE INSERT ON "webreport_users" FOR EACH ROW
BEGIN
    Select "S_webreport_users".NextVal into :new."id" from dual; 
END;
