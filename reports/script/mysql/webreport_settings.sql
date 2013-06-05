-- 
-- Script to create webreport_settings table for MySQL database.
--

CREATE TABLE `webreport_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `version` VARCHAR(10),
  PRIMARY KEY (`id`));
  
-- $next
  
insert into `webreport_settings` (`version`) values ('1.3');