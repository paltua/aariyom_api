CREATE TABLE `event_images` (
  `ei_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `ei_image_name` varchar(100) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  `is_default` enum('1','0') DEFAULT '0',
  `is_completed` enum('1','0') DEFAULT '0',
  PRIMARY KEY (`ei_id`),
  KEY `index2` (`event_id`,`is_completed`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `event_location` (
  `el_id` int(11) NOT NULL AUTO_INCREMENT,
  `eml_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `address` text,
  `pin` int(11) DEFAULT NULL,
  PRIMARY KEY (`el_id`),
  KEY `eml_id` (`eml_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `event_master` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_code` varchar(45) NOT NULL,
  `eml_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`),
  UNIQUE KEY `event_code_UNIQUE` (`event_code`),
  UNIQUE KEY `eml_id_UNIQUE` (`eml_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `event_master_log` (
  `eml_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_long_desc` longtext CHARACTER SET utf8,
  `event_status` enum('active','inactive','deleted') DEFAULT 'active',
  `event_created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `event_created_by` int(11) NOT NULL,
  `event_start_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_end_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_about` longtext CHARACTER SET utf8,
  `event_objectives` longtext CHARACTER SET utf8,
  `event_title_url` varchar(255) NOT NULL,
  `event_youtube_url` varchar(255) DEFAULT NULL,
  `event_short_desc` text CHARACTER SET utf8,
  PRIMARY KEY (`eml_id`),
  UNIQUE KEY `event_title_url_UNIQUE` (`event_title_url`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `event_programs_rel` (
  `epr_id` int(11) NOT NULL AUTO_INCREMENT,
  `eml_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  PRIMARY KEY (`epr_id`),
  KEY `eml_id` (`eml_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `event_programs_rel_rel` (
  `eprr_id` int(11) NOT NULL AUTO_INCREMENT,
  `epr_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  PRIMARY KEY (`eprr_id`),
  KEY `epr_id_index` (`epr_id`),
  KEY `program_id_index` (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `functional_units` (
  `fu_id` int(11) NOT NULL AUTO_INCREMENT,
  `fu_title` varchar(255) NOT NULL,
  `fu_desc` longtext,
  `fu_objectives` longtext,
  `fu_is_deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `fu_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `fu_image` varchar(255) DEFAULT NULL,
  `fu_managed_by` varchar(100) DEFAULT NULL,
  `fu_operating_location` text,
  `fu_about` longtext CHARACTER SET utf8,
  `fu_short_desc` text,
  `fu_title_url` varchar(255) NOT NULL,
  PRIMARY KEY (`fu_id`),
  UNIQUE KEY `fu_title_url_UNIQUE` (`fu_title_url`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `functional_units_log` (
  `ful_id` int(11) NOT NULL AUTO_INCREMENT,
  `fu_id` int(11) NOT NULL,
  `fu_title` varchar(255) NOT NULL,
  `fu_desc` longtext CHARACTER SET utf8,
  `fu_objectives` longtext CHARACTER SET utf8,
  `fu_is_deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `fu_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `fu_created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fu_created_by` int(11) DEFAULT NULL,
  `fu_image` varchar(255) DEFAULT NULL,
  `fu_managed_by` varchar(100) DEFAULT NULL,
  `fu_operating_location` text,
  `fu_about` longtext CHARACTER SET utf8,
  `fu_short_desc` text CHARACTER SET utf8,
  `fu_title_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ful_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_title` varchar(255) NOT NULL,
  `program_desc` longtext CHARACTER SET utf8 NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `program_status` enum('ongoing','upcoming','completed') DEFAULT 'ongoing',
  `org_by_custom_name` varchar(255) DEFAULT NULL,
  `program_about` longtext CHARACTER SET utf8,
  `program_objectives` longtext CHARACTER SET utf8,
  `pro_title_url` varchar(255) DEFAULT NULL,
  `program_short_desc` text CHARACTER SET utf8,
  PRIMARY KEY (`program_id`),
  UNIQUE KEY `title_url` (`pro_title_url`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `programs_fus_rel` (
  `pfr_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) NOT NULL,
  `fu_id` int(11) NOT NULL,
  PRIMARY KEY (`pfr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;


CREATE TABLE `settings_midea_about_us` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(45) DEFAULT NULL,
  `type` varchar(55) DEFAULT NULL,
  `is_for` varchar(55) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive','deleted') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
