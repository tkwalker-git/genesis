-- 
-- Script to create webreport_users table for Firebird database.
--

CREATE TABLE "webreport_users" (
  "id" integer not null PRIMARY KEY,
  "username" VARCHAR(200),
  "password" VARCHAR(200),
  "email" VARCHAR(200)
  )

  
-- $next

CREATE generator "g_webreport_users"

-- $next

CREATE TRIGGER "t_webreport_users" FOR "webreport_users"
BEFORE INSERT
AS
BEGIN
  IF (NEW."id" IS NULL)
    THEN NEW."id" = GEN_ID("g_webreport_users", 1);
END
