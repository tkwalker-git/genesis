-- 
-- Script to create webreport_style table for Firebird database.
--

CREATE TABLE webreport_style (
  report_style_id integer not null PRIMARY KEY,
  "type" VARCHAR(6),
  "field" integer,
  "group" integer,
  style_str BLOB SUB_TYPE TEXT,
  "uniq" integer,
  repname VARCHAR(255),
  styletype VARCHAR(40))

-- $next

CREATE GENERATOR g_webreport_style

-- $next

CREATE TRIGGER t_webreport_style FOR webreport_style
BEFORE INSERT
AS
BEGIN
  IF (NEW.report_style_id IS NULL)
    THEN NEW.report_style_id = GEN_ID(g_webreport_style, 1);
END
