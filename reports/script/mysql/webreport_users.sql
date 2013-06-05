-- 
-- Script to create webreport_users table for MySQL database.
--

CREATE TABLE `webreport_users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(200),
  `password` varchar(200),
  `email` VARCHAR(200),
  PRIMARY KEY (`id`));
