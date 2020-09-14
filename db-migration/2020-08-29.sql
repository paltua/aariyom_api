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
ADD COLUMN `org_by_custom_name` VARCHAR(255) NULL AFTER `program_status`;

ALTER TABLE `programs` 
DROP COLUMN `program_image`;

ALTER TABLE `programs` 
ADD COLUMN `program_about` LONGTEXT NULL AFTER `org_by_custom_name`,
ADD COLUMN `program_objectives` LONGTEXT NULL AFTER `program_about`;

ALTER TABLE `programs` 
ADD COLUMN `pro_title_url` VARCHAR(255) NULL AFTER `program_objectives`;

UPDATE event_master_log SET
    event_title_url = lower(event_title),
    event_title_url = replace(event_title_url, '.', ' '),
    event_title_url = replace(event_title_url, ',', ' '),
    event_title_url = replace(event_title_url, ';', ' '),
    event_title_url = replace(event_title_url, ':', ' '),
    event_title_url = replace(event_title_url, '?', ' '),
    event_title_url = replace(event_title_url, '%', ' '),
    event_title_url = replace(event_title_url, '&', ' '),
    event_title_url = replace(event_title_url, '#', ' '),
    event_title_url = replace(event_title_url, '*', ' '),
    event_title_url = replace(event_title_url, '!', ' '),
    event_title_url = replace(event_title_url, '_', ' '),
    event_title_url = replace(event_title_url, '@', ' '),
    event_title_url = replace(event_title_url, '+', ' '),
    event_title_url = replace(event_title_url, '(', ' '),
    event_title_url = replace(event_title_url, ')', ' '),
    event_title_url = replace(event_title_url, '[', ' '),
    event_title_url = replace(event_title_url, ']', ' '),
    event_title_url = replace(event_title_url, '/', ' '),
    event_title_url = replace(event_title_url, '-', ' '),
    event_title_url = replace(event_title_url, '\'', ''),
    event_title_url = trim(event_title_url),
    event_title_url = replace(event_title_url, ' ', '-'),
    event_title_url = replace(event_title_url, '--', '-');

    ALTER TABLE `event_master_log` 
CHANGE COLUMN `event_title_url` `event_title_url` VARCHAR(255) NOT NULL ,
ADD UNIQUE INDEX `event_title_url_UNIQUE` (`event_title_url` ASC);

ALTER TABLE `event_master_log` 
ADD INDEX `event_id` (`event_id` ASC);

ALTER TABLE `event_programs_rel` 
CHANGE COLUMN `event_id` `eml_id` INT(11) NOT NULL ,
ADD COLUMN `program_id` INT NOT NULL AFTER `eml_id`,
ADD INDEX `eml_id` (`eml_id` ASC);

ALTER TABLE `event_location` 
CHANGE COLUMN `event_id` `eml_id` INT(11) NOT NULL ,
ADD INDEX `eml_id` (`eml_id` ASC);

ALTER TABLE `event_master` 
DROP COLUMN `epr_id`,
DROP COLUMN `el_id`,
CHANGE COLUMN `eml_id` `eml_id` BIGINT(20) NOT NULL DEFAULT '0' ;

ALTER TABLE `programs` 
CHANGE COLUMN `program_desc` `program_desc` LONGTEXT CHARACTER SET 'utf8' NOT NULL ,
CHANGE COLUMN `program_about` `program_about` LONGTEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `program_objectives` `program_objectives` LONGTEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ,
ADD COLUMN `program_short_desc` TEXT CHARACTER SET 'utf8' NULL AFTER `pro_title_url`;

ALTER TABLE `functional_units_log` 
CHANGE COLUMN `fu_desc` `fu_desc` LONGTEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `fu_objectives` `fu_objectives` LONGTEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ,
ADD COLUMN `fu_about` LONGTEXT CHARACTER SET 'utf8' NULL AFTER `fu_operating_location`,
ADD COLUMN `fu_short_desc` TEXT CHARACTER SET 'utf8' NULL AFTER `fu_about`;

ALTER TABLE `event_master_log` 
CHANGE COLUMN `event_long_desc` `event_long_desc` LONGTEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `event_about` `event_about` LONGTEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `event_objectives` `event_objectives` LONGTEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ,
ADD COLUMN `event_short_desc` TEXT CHARACTER SET 'utf8' NULL AFTER `event_youtube_url`;

ALTER TABLE `functional_units` 
ADD COLUMN `fu_about` LONGTEXT CHARACTER SET 'utf8' NULL AFTER `fu_operating_location`,
ADD COLUMN `fu_short_desc` TEXT NULL AFTER `fu_about`;

ALTER TABLE `functional_units` 
CHANGE COLUMN `fu_desc` `fu_desc` LONGTEXT NULL DEFAULT NULL ,
CHANGE COLUMN `fu_objectives` `fu_objectives` LONGTEXT NULL DEFAULT NULL ;























