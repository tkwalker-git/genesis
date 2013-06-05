-- 
-- Script to create webreport_sql table for MySQL database.
--

CREATE TABLE `webreport_sql` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `sqlname` VARCHAR(100),
  `sqlcontent` TEXT NULL,
  PRIMARY KEY (`id`));
