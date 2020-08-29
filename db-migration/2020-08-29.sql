ALTER TABLE `event_images` 
ADD COLUMN `is_completed` ENUM('1', '0') NULL DEFAULT '0' AFTER `is_default`;

ALTER TABLE `event_images` 
ADD INDEX `index2` (`event_id` ASC, `is_completed` ASC);

CREATE TABLE `programs_images` (
  `prog_img_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) NOT NULL,
  `prog_img_name` varchar(100) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  `is_default` enum('1','0') DEFAULT '0',
  `is_completed` enum('1','0') DEFAULT '0',
  PRIMARY KEY (`prog_img_id`),
  KEY `index2` (`program_id`,`is_completed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `event_master_log` 
CHANGE COLUMN `event_status` `event_status` ENUM('active', 'inactive', 'deleted') NULL DEFAULT 'active' ,
ADD COLUMN `event_title_url` TEXT NULL AFTER `event_objectives`,
ADD COLUMN `event_youtube_url` VARCHAR(255) NULL AFTER `event_title_url`;


ALTER TABLE `event_master` 
DROP COLUMN `event_objectives`,
DROP COLUMN `event_about`,
DROP COLUMN `event_end_date_time`,
DROP COLUMN `event_start_date_time`,
DROP COLUMN `event_created_by`,
DROP COLUMN `event_created_date`,
DROP COLUMN `event_is_deleted`,
DROP COLUMN `event_status`,
DROP COLUMN `event_long_desc`;

ALTER TABLE `event_master` 
ADD COLUMN `eml_id` BIGINT NULL AFTER `epr_id`;

ALTER TABLE `event_master` 
DROP COLUMN `event_title`;

ALTER TABLE `event_master` 
CHANGE COLUMN `eml_id` `eml_id` BIGINT(20) NULL DEFAULT 0 ,
ADD UNIQUE INDEX `eml_id_UNIQUE` (`eml_id` ASC);

ALTER TABLE `event_master_log` 
CHANGE COLUMN `eml_id` `eml_id` BIGINT NOT NULL ;

ALTER TABLE `event_master_log` 
CHANGE COLUMN `eml_id` `eml_id` BIGINT(20) NOT NULL AUTO_INCREMENT ;

ALTER TABLE `programs` 
ADD COLUMN `pro_fus_custom_name` VARCHAR(255) NULL AFTER `program_status`;












