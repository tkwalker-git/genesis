-- 
-- Script to create webreport_admin table for Firebird database.
--

CREATE TABLE webreport_admin (
  id integer not null primary key,
  tablename VARCHAR(250),
  db_type VARCHAR(10),
  group_name VARCHAR(250)
)

-- $next

CREATE GENERATOR g_webreport_admin

-- $next

CREATE TRIGGER t_webreport_admin FOR webreport_admin
BEFORE INSERT
AS
BEGIN
  IF (NEW.id IS NULL)
    THEN NEW.id = GEN_ID(g_webreport_admin, 1);
END
