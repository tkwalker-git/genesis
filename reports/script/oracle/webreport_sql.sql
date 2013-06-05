-- 
-- Script to create webreport_sql table for Oracle database.
--

CREATE TABLE "webreport_sql" (
  "id" NUMBER(10) PRIMARY KEY, 
  "sqlname" VARCHAR2(100)  NULL, 
  "sqlcontent" CLOB NOT NULL)

-- $next

CREATE SEQUENCE "S_webreport_sql" INCREMENT BY 1 START WITH 1 MINVALUE 0 NOCYCLE NOCACHE NOORDER

-- $next

CREATE OR REPLACE TRIGGER "tr_webreport_sql" BEFORE INSERT ON "webreport_sql" FOR EACH ROW
BEGIN
  if :new."id" is null then
    Select "S_webreport_sql".NextVal into :new."id" from dual; 
  end if;
END;

