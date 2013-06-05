-- 
-- Script to create webreport_sql table for Firebird database.
--

CREATE TABLE "webreport_sql" (
  "id" integer not null PRIMARY KEY, 
  "sqlname" VARCHAR(100), 
  "sqlcontent" BLOB SUB_TYPE TEXT)

-- $next

CREATE GENERATOR "g_webreport_sql"

-- $next

CREATE TRIGGER "t_webreport_sql" FOR "webreport_sql"
BEFORE INSERT
AS
BEGIN
  IF (NEW."id" IS NULL)
    THEN NEW."id" = GEN_ID("g_webreport_sql", 1);
END


