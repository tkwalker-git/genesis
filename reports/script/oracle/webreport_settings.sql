-- 
-- Script to create webreport_settings table for Oracle database.
--

CREATE TABLE "webreport_settings" (
  "id" NUMBER(11) PRIMARY KEY,
  "version" VARCHAR2(10)
  )

  
-- $next

CREATE SEQUENCE "S_webreport_settings" INCREMENT BY 1 START WITH 1 MINVALUE 0 NOCYCLE NOCACHE NOORDER

-- $next

CREATE OR REPLACE TRIGGER "tr_webreport_settings" BEFORE INSERT ON "webreport_settings" FOR EACH ROW
BEGIN
    Select "S_webreport_settings".NextVal into :new."id" from dual; 
END;

-- $next
  
insert into "webreport_settings" ("version") values ('1.3')
