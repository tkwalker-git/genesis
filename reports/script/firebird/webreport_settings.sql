-- 
-- Script to create webreport_settings table for Firebird database.
--
CREATE TABLE "webreport_settings" (
  "id" integer not null primary key,
  "version" VARCHAR(10)
  )

-- $next

CREATE GENERATOR "g_webreport_settings"

-- $next

CREATE TRIGGER "t_webreport_settings" FOR "webreport_settings"
BEFORE INSERT
AS
BEGIN
  IF (NEW."id" IS NULL)
    THEN NEW."id" = GEN_ID("g_webreport_settings",1);
END

-- $next
  
insert into "webreport_settings" ("version") values ('1.3')