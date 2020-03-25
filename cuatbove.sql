-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 02, 2019 at 07:27 AM
-- Server version: 5.6.41-84.1-log
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spsdaurm_users`
--

-- --------------------------------------------------------

--
-- Table structure for table `cutabove`
--

CREATE TABLE `cutabove` (
  `id` int(11) NOT NULL,
  `clg_reg` int(9) NOT NULL,
  `name` text NOT NULL,
  `dob` text NOT NULL,
  `semester` text NOT NULL,
  `phone` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `phone_whatsapp` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `email` text NOT NULL,
  `theme` text NOT NULL,
  `comments` text NOT NULL,
  `kit` tinyint(1) NOT NULL DEFAULT '0',
  `stg1fee` int(11) NOT NULL DEFAULT '0',
  `stg1w1` int(11) NOT NULL DEFAULT '0',
  `stg1w2` int(11) NOT NULL DEFAULT '0',
  `stg1w3` int(11) NOT NULL DEFAULT '0',
  `stg1w4` int(11) NOT NULL DEFAULT '0',
  `stg1c` tinyint(1) NOT NULL DEFAULT '0',
  `stg2fee` int(11) NOT NULL DEFAULT '0',
  `stg2w1` int(11) NOT NULL DEFAULT '0',
  `stg2w2` int(11) NOT NULL DEFAULT '0',
  `stg2w3` int(11) NOT NULL DEFAULT '0',
  `stg2w4` int(11) NOT NULL DEFAULT '0',
  `stg2w5` int(11) NOT NULL DEFAULT '0',
  `stg2c` tinyint(1) NOT NULL DEFAULT '0',
  `stg1w1_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg1w2_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg1w3_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg1w4_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg2w1_applied_for` int(11) DEFAULT '0',
  `stg2w2_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg2w3_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg2w4_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg2w5_applied_for` int(11) NOT NULL DEFAULT '0',
  `edited_by` text NOT NULL,
  `last_edited_at` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cutabove_council`
--

CREATE TABLE `cutabove_council` (
  `council_id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `permission` varchar(10) NOT NULL,
  `theme` text NOT NULL,
  `phone` bigint(20) NOT NULL DEFAULT '0',
  `phone_whatsapp` bigint(20) NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL,
  `comments` text NOT NULL,
  `edited_by` text NOT NULL,
  `last_edited_at` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cutabove_deletion_log`
--

CREATE TABLE `cutabove_deletion_log` (
  `id` int(11) NOT NULL,
  `log` longtext NOT NULL,
  `council_id` int(11) NOT NULL,
  `comments` text NOT NULL,
  `datetime` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cutabove_fee`
--

CREATE TABLE `cutabove_fee` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `stage` varchar(10) NOT NULL,
  `old_value` int(11) NOT NULL,
  `new_value` int(11) NOT NULL,
  `edited_by` text NOT NULL,
  `last_edited_at` varchar(30) NOT NULL,
  `comments` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cutabove_misc`
--

CREATE TABLE `cutabove_misc` (
  `id` int(11) NOT NULL,
  `property` text NOT NULL,
  `value` text NOT NULL,
  `help` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cutabove_workshop`
--

CREATE TABLE `cutabove_workshop` (
  `workshop_id` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  `level_name` varchar(20) NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `memorisable_name` text NOT NULL,
  `supervisor_id` int(11) NOT NULL COMMENT 'the one who completed the workshop',
  `feedback_link` text NOT NULL,
  `bonus_files` text NOT NULL,
  `member_id_1` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_2` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_3` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_4` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_5` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_6` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_7` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_8` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_9` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_10` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_11` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_12` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_13` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_14` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_15` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_16` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_17` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_18` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_19` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_20` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id_1a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_2a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_3a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_4a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_5a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_6a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_7a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_8a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_9a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_10a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_11a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_12a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_13a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_14a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_15a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_16a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_17a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_18a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_19a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_20a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_1` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_2` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_3` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_4` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_5` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_6` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_7` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_8` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_9` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_10` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_11` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_12` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_13` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_14` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_15` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_16` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_17` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_18` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_19` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_20` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_1a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_2a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_3a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_4a` int(11) NOT NULL DEFAULT '0',
  `supervisor_id_5a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_6a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_7a` tinyint(4) DEFAULT '0',
  `supervisor_id_8a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_9a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_10a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_11a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_12a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_13a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_14a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_15a` tinyint(4) DEFAULT '0',
  `supervisor_id_16a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_17a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_18a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_19a` tinyint(4) NOT NULL DEFAULT '0',
  `supervisor_id_20a` tinyint(4) NOT NULL DEFAULT '0',
  `comments` text NOT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cutabove_workshop_supervisors_applied_fo`
--

CREATE TABLE `cutabove_workshop_supervisors_applied_fo` (
  `id` int(11) NOT NULL,
  `workshop_level` text NOT NULL,
  `workshop_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `supervisor_attendance` int(11) NOT NULL,
  `attendance_updated_by` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cutabove`
--
ALTER TABLE `cutabove`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clg_reg` (`clg_reg`);

--
-- Indexes for table `cutabove_council`
--
ALTER TABLE `cutabove_council`
  ADD PRIMARY KEY (`council_id`);

--
-- Indexes for table `cutabove_deletion_log`
--
ALTER TABLE `cutabove_deletion_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cutabove_fee`
--
ALTER TABLE `cutabove_fee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cutabove_misc`
--
ALTER TABLE `cutabove_misc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cutabove_workshop`
--
ALTER TABLE `cutabove_workshop`
  ADD PRIMARY KEY (`workshop_id`);

--
-- Indexes for table `cutabove_workshop_supervisors_applied_fo`
--
ALTER TABLE `cutabove_workshop_supervisors_applied_fo`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cutabove`
--
ALTER TABLE `cutabove`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cutabove_council`
--
ALTER TABLE `cutabove_council`
  MODIFY `council_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cutabove_deletion_log`
--
ALTER TABLE `cutabove_deletion_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cutabove_fee`
--
ALTER TABLE `cutabove_fee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cutabove_misc`
--
ALTER TABLE `cutabove_misc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cutabove_workshop`
--
ALTER TABLE `cutabove_workshop`
  MODIFY `workshop_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cutabove_workshop_supervisors_applied_fo`
--
ALTER TABLE `cutabove_workshop_supervisors_applied_fo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
