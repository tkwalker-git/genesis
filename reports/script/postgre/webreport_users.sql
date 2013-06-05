-- 
-- Script to create webreport_users table for Postgre database.
--

CREATE TABLE "webreport_users" (
  "id" SERIAL PRIMARY KEY,
  "username" VARCHAR(200),
  "password" VARCHAR(200),
  "email" VARCHAR(200)
  );
