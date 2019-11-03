
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


