-- 
-- Script to create webreport_admin table for MySQL database.
--

CREATE TABLE `webreport_admin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tablename` VARCHAR(250),
  `db_type` varchar(10),
  `group_name` VARCHAR(250),
  PRIMARY KEY (`id`));
  
