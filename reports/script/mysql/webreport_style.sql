-- 
-- Script to create webreport_style table for MySQL database.
--

CREATE TABLE `webreport_style` (
  `report_style_id` INT(11) NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(6) NOT NULL,
  `field` INT(11) NOT NULL,
  `group` INT(11) NOT NULL,
  `style_str` TEXT NOT NULL,
  `uniq` INT(11) DEFAULT NULL,
  `repname` VARCHAR(255) NOT NULL,
  `styletype` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`report_style_id`));
  
