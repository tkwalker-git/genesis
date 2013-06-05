-- 
-- Script to create webreports table for MySQL database.
--

CREATE TABLE `webreports` ( 
  `rpt_id` INT NOT NULL AUTO_INCREMENT, 
  `rpt_name` VARCHAR(45) NOT NULL, 
  `rpt_title` TEXT NULL, 
  `rpt_cdate` DATETIME NOT NULL, 
  `rpt_mdate` DATETIME NULL,  
  `rpt_content` LONGTEXT NOT NULL , 
  `rpt_owner` VARCHAR(100) NOT NULL , 
  `rpt_status` ENUM("public","private") NOT NULL DEFAULT "public", 
  `rpt_type` ENUM("report","chart") NOT NULL, 
  PRIMARY KEY (`rpt_id`));

