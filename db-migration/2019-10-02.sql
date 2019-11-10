
INSERT INTO `rest_api_access` (`key`, `all_access`, `controller`) VALUES ('23456', '1', 'api/admin/contactus');


-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 02, 2019 at 04:03 PM
-- Server version: 5.6.45
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `parrotdi_birds`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `con_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` bigint(20) NOT NULL,
  `desccription` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`con_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `con_id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

ALTER TABLE `functional_units` 
ADD COLUMN `fu_managed_by` VARCHAR(100) NULL AFTER `fu_image`,
ADD COLUMN `fu_operating_location` TEXT NULL AFTER `fu_managed_by`;

ALTER TABLE `functional_units_log` 
ADD COLUMN `fu_managed_by` VARCHAR(100) NULL AFTER `fu_image`,
ADD COLUMN `fu_operating_location` TEXT NULL AFTER `fu_managed_by`;

CREATE TABLE `programs_fus_rel` (
  `pfr_id` INT NOT NULL AUTO_INCREMENT,
  `program_id` INT NOT NULL,
  `fu_id` INT NOT NULL,
  PRIMARY KEY (`pfr_id`));

  ALTER TABLE `programs` 
ADD COLUMN `program_status` ENUM('ongoing', 'upcoming', 'completed') NULL DEFAULT 'ongoing' AFTER `program_image`;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(45) DEFAULT NULL,
  `key_name` varchar(45) DEFAULT NULL,
  `key_value` longtext,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `settings` (`page`, `key_name`, `key_value`) VALUES ('home', 'main_text', 'Aariyom foundation is a simple and natural expression of time evolution. It’s main goal is to build and implement a significant real purpose human life. Thus by the thoughts of welfare of living being or the world, aariyom foundation emerged as a foundation by which one can express it’s highest potential, possibilities and become satisfied fully and maturely. So, today aariyom foundation be able to express and accelerate the truths of life , actively and highly caring at all levels of life.');
INSERT INTO `settings` (`page`, `key_name`, `key_value`) VALUES ('contact_us', 'address', 'Kolkata,West Bengal, India');
INSERT INTO `settings` (`page`, `key_name`, `key_value`) VALUES ('contact_us', 'email', 'abc@gmail.com');
INSERT INTO `settings` (`page`, `key_name`, `key_value`) VALUES ('contact_us', 'mobile', '9830116929');
INSERT INTO `settings` (`page`, `key_name`, `key_value`) VALUES ('about_us', 'who_we_are', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec luctus felis id dolor dignissim vel vulputate eros feugiat. Mauris accumsan aliquam ultrices. Vivamus sit amet pulvinar mi. Nam at placerat urna. Sed rutrum, ante eget fermentum sodales, est eros condimentum velit, nec consectetur lorem augue ac sapien. Morbi et arcu sit amet lacus ornare malesuada. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec blandit sem purus. Pellentesque quis magna odio, non mattis mi. In et dui mauris, sit amet ullamcorper nisl.\n\nDuis a orci nisi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi feugiat ultrices elementum. Nullam nisi elit, semper nec eleifend et, auctor aliquet risus. Curabitur placerat lacus et orci blandit ac lacinia sem dignissim. Nam nec odio elit. Pellentesque dapibus commodo leo quis feugiat. In hac habitasse platea dictumst. Integer id tortor sit amet purus viverra aliquam nec ac elit. Fusce facilisis urna sed ligula pellentesque molestie. Duis ac risus elit. Proin ut felis diam. Ut felis diam, convallis sit amet hendrerit id, euismod id mi. Nullam nisl purus, semper et tristique a, ullamcorper vitae metus.');
INSERT INTO `settings` (`page`, `key_name`, `key_value`) VALUES ('about_us', 'our_mission', 'Integer laoreet semper dui nec viverra. Cras nibh ligula, aliquam quis sollicitudin euPellentesque habitant morbi tristique senectus et netus et malesuada famesInteger laoreet semper dui nec viverra. Cras nibh ligula, aliquam quis sollicitudin euPellentesque habitant morbi tristique senectus et netus et malesuada famesPellentesque habitant morbi tristique senectus et netus et malesuada famesInteger laoreet semper dui nec viverra. Cras nibh ligula, aliquam quis sollicitudin eu');
INSERT INTO `settings` (`page`, `key_name`, `key_value`) VALUES ('about_us', 'image', 'test.png');




-- updated in live server
