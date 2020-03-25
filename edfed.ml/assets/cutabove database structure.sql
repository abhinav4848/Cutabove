-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 14, 2019 at 08:20 PM
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
  `dob` date NOT NULL,
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
  `stg2fee` int(11) NOT NULL DEFAULT '0',
  `stg2w1` int(11) NOT NULL DEFAULT '0',
  `stg2w2` int(11) NOT NULL DEFAULT '0',
  `stg2w3` int(11) NOT NULL DEFAULT '0',
  `stg2w4` int(11) NOT NULL DEFAULT '0',
  `stg2w5` int(11) NOT NULL DEFAULT '0',
  `stg3fee` int(11) NOT NULL DEFAULT '0',
  `stg3w1` int(11) NOT NULL DEFAULT '0',
  `stg3w2` int(11) NOT NULL DEFAULT '0',
  `stg3w3` int(11) NOT NULL DEFAULT '0',
  `stg3w4` int(11) NOT NULL DEFAULT '0',
  `stg3w5` int(11) NOT NULL DEFAULT '0',
  `stg4fee` int(11) NOT NULL DEFAULT '0',
  `stg4w1` int(11) NOT NULL DEFAULT '0',
  `stg4w2` int(11) NOT NULL DEFAULT '0',
  `stg4w3` int(11) NOT NULL DEFAULT '0',
  `stg4w4` int(11) NOT NULL DEFAULT '0',
  `stg4w5` int(11) NOT NULL DEFAULT '0',
  `stg5w1fee` int(11) NOT NULL DEFAULT '0',
  `stg5w1` int(11) NOT NULL DEFAULT '0',
  `stg5w2fee` int(11) NOT NULL DEFAULT '0',
  `stg5w2` int(11) NOT NULL DEFAULT '0',
  `stg5w3fee` int(11) NOT NULL DEFAULT '0',
  `stg5w3` int(11) NOT NULL DEFAULT '0',
  `stg5w4fee` int(11) NOT NULL DEFAULT '0',
  `stg5w4` int(11) NOT NULL DEFAULT '0',
  `stg5w5fee` int(11) NOT NULL DEFAULT '0',
  `stg5w5` int(11) NOT NULL DEFAULT '0',
  `stg1w1_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg1w2_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg1w3_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg1w4_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg2w1_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg2w2_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg2w3_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg2w4_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg2w5_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg3w1_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg3w2_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg3w3_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg3w4_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg3w5_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg4w1_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg4w2_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg4w3_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg4w4_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg4w5_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg5w1_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg5w2_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg5w3_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg5w4_applied_for` int(11) NOT NULL DEFAULT '0',
  `stg5w5_applied_for` int(11) NOT NULL DEFAULT '0',
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
  `datetime` datetime NOT NULL
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
  `last_edited_at` datetime NOT NULL,
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

--
-- Dumping data for table `cutabove_misc`
--

INSERT INTO `cutabove_misc` (`id`, `property`, `value`, `help`) VALUES
(1, 'fee', '934', 'code abandoned. Value doesn\'t matter anymore.'),
(2, 'stg1fee', '500', ''),
(3, 'stg2fee', '434', ''),
(4, 'stg3fee', '200', ''),
(5, 'stg4fee', '200', ''),
(6, 'stg5w1fee', '800', ''),
(7, 'stg5w2fee', '800', ''),
(8, 'stg5w3fee', '800', ''),
(9, 'stg5w4fee', '400', ''),
(10, 'stg5w5fee', '800', ''),
(11, 'stg1w1_feedback_link', 'https://abhinavkr.ga/contact.htm', 'Changes affect only the new workshops. Older workshops retain their values. This applies to all feedback links.'),
(12, 'stg1w2_feedback_link', '', ''),
(13, 'stg1w3_feedback_link', '', ''),
(14, 'stg1w4_feedback_link', '', ''),
(15, 'stg2w1_feedback_link', '', ''),
(16, 'stg2w2_feedback_link', '', ''),
(17, 'stg2w3_feedback_link', '', ''),
(18, 'stg2w4_feedback_link', '', ''),
(19, 'stg2w5_feedback_link', '', ''),
(20, 'stg3w1_feedback_link', '', ''),
(21, 'stg3w2_feedback_link', '', ''),
(22, 'stg3w3_feedback_link', '', ''),
(23, 'stg3w4_feedback_link', '', ''),
(24, 'stg3w5_feedback_link', '', ''),
(25, 'stg4w1_feedback_link', '', ''),
(26, 'stg4w2_feedback_link', '', ''),
(27, 'stg4w3_feedback_link', '', ''),
(28, 'stg4w4_feedback_link', '', ''),
(29, 'stg4w5_feedback_link', '', ''),
(30, 'stg5w1_feedback_link', '', ''),
(31, 'stg5w2_feedback_link', '', ''),
(32, 'stg5w3_feedback_link', '', ''),
(33, 'stg5w4_feedback_link', '', ''),
(34, 'stg5w5_feedback_link', '', ''),
(35, 'stg1w1_bonus_files', 'http://spsdarj.org', 'Changes affect only the new workshops. Older workshops retain their values. This applies to all bonus file links.'),
(36, 'stg1w2_bonus_files', '', ''),
(37, 'stg1w3_bonus_files', '', ''),
(38, 'stg1w4_bonus_files', '', ''),
(39, 'stg2w1_bonus_files', '', ''),
(40, 'stg2w2_bonus_files', '', ''),
(41, 'stg2w3_bonus_files', '', ''),
(42, 'stg2w4_bonus_files', '', ''),
(43, 'stg2w5_bonus_files', '', ''),
(44, 'stg3w1_bonus_files', '', ''),
(45, 'stg3w2_bonus_files', '', ''),
(46, 'stg3w3_bonus_files', '', ''),
(47, 'stg3w4_bonus_files', '', ''),
(48, 'stg3w5_bonus_files', '', ''),
(49, 'stg4w1_bonus_files', '', ''),
(50, 'stg4w2_bonus_files', '', ''),
(51, 'stg4w3_bonus_files', '', ''),
(52, 'stg4w4_bonus_files', '', ''),
(53, 'stg4w5_bonus_files', '', ''),
(54, 'stg5w1_bonus_files', '', ''),
(55, 'stg5w2_bonus_files', '', ''),
(56, 'stg5w3_bonus_files', '', ''),
(57, 'stg5w4_bonus_files', '', ''),
(58, 'stg5w5_bonus_files', '', ''),
(59, 'registrations', '1', 'Set to 0 if you want to disable registrations. Otherwise any other number to enable it. That number holds no other significance other than to say it is not 0.'),
(60, 'login_page_notice', 'Hello World! Site is Live!!! <i>You\'re all invited</i>', 'Message to display on the login page.');

-- --------------------------------------------------------

--
-- Table structure for table `cutabove_workshop`
--

CREATE TABLE `cutabove_workshop` (
  `workshop_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `level_name` varchar(20) NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `memorisable_name` text NOT NULL,
  `supervisor_id` int(11) NOT NULL DEFAULT '0' COMMENT 'the one who completed the workshop',
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
  `member_id_21` int(11) NOT NULL DEFAULT '0',
  `member_id_22` int(11) NOT NULL DEFAULT '0',
  `member_id_23` int(11) NOT NULL DEFAULT '0',
  `member_id_24` int(11) NOT NULL DEFAULT '0',
  `member_id_25` int(11) NOT NULL DEFAULT '0',
  `member_id_26` int(11) NOT NULL DEFAULT '0',
  `member_id_27` int(11) NOT NULL DEFAULT '0',
  `member_id_28` int(11) NOT NULL DEFAULT '0',
  `member_id_29` int(11) NOT NULL DEFAULT '0',
  `member_id_30` int(11) NOT NULL DEFAULT '0',
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
  `member_id_21a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_22a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_23a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_24a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_25a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_26a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_27a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_28a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_29a` tinyint(1) NOT NULL DEFAULT '0',
  `member_id_30a` tinyint(1) NOT NULL DEFAULT '0',
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
  `supervisor_id_1a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_2a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_3a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_4a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_5a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_6a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_7a` tinyint(1) DEFAULT '0',
  `supervisor_id_8a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_9a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_10a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_11a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_12a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_13a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_14a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_15a` tinyint(1) DEFAULT '0',
  `supervisor_id_16a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_17a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_18a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_19a` tinyint(1) NOT NULL DEFAULT '0',
  `supervisor_id_20a` tinyint(1) NOT NULL DEFAULT '0',
  `comments` text NOT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cutabove_workshop`
--

INSERT INTO `cutabove_workshop` (`workshop_id`, `date`, `level_name`, `completed`, `memorisable_name`, `supervisor_id`, `feedback_link`, `bonus_files`, `member_id_1`, `member_id_2`, `member_id_3`, `member_id_4`, `member_id_5`, `member_id_6`, `member_id_7`, `member_id_8`, `member_id_9`, `member_id_10`, `member_id_11`, `member_id_12`, `member_id_13`, `member_id_14`, `member_id_15`, `member_id_16`, `member_id_17`, `member_id_18`, `member_id_19`, `member_id_20`, `member_id_21`, `member_id_22`, `member_id_23`, `member_id_24`, `member_id_25`, `member_id_26`, `member_id_27`, `member_id_28`, `member_id_29`, `member_id_30`, `member_id_1a`, `member_id_2a`, `member_id_3a`, `member_id_4a`, `member_id_5a`, `member_id_6a`, `member_id_7a`, `member_id_8a`, `member_id_9a`, `member_id_10a`, `member_id_11a`, `member_id_12a`, `member_id_13a`, `member_id_14a`, `member_id_15a`, `member_id_16a`, `member_id_17a`, `member_id_18a`, `member_id_19a`, `member_id_20a`, `member_id_21a`, `member_id_22a`, `member_id_23a`, `member_id_24a`, `member_id_25a`, `member_id_26a`, `member_id_27a`, `member_id_28a`, `member_id_29a`, `member_id_30a`, `supervisor_id_1`, `supervisor_id_2`, `supervisor_id_3`, `supervisor_id_4`, `supervisor_id_5`, `supervisor_id_6`, `supervisor_id_7`, `supervisor_id_8`, `supervisor_id_9`, `supervisor_id_10`, `supervisor_id_11`, `supervisor_id_12`, `supervisor_id_13`, `supervisor_id_14`, `supervisor_id_15`, `supervisor_id_16`, `supervisor_id_17`, `supervisor_id_18`, `supervisor_id_19`, `supervisor_id_20`, `supervisor_id_1a`, `supervisor_id_2a`, `supervisor_id_3a`, `supervisor_id_4a`, `supervisor_id_5a`, `supervisor_id_6a`, `supervisor_id_7a`, `supervisor_id_8a`, `supervisor_id_9a`, `supervisor_id_10a`, `supervisor_id_11a`, `supervisor_id_12a`, `supervisor_id_13a`, `supervisor_id_14a`, `supervisor_id_15a`, `supervisor_id_16a`, `supervisor_id_17a`, `supervisor_id_18a`, `supervisor_id_19a`, `supervisor_id_20a`, `comments`, `archived`) VALUES
(1, '2000-01-01 12:00:00', 'Genesis Workshop', 1, 'Default Workshop', 1, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'If you see this being assigned to someone, it was added via admin from the user edit page. A real workshop id (anything other than \"1\") has not been allotted.  <br /><small>[06-09-2018 08:29:16pm] Abhinav Kumar (ID# 1):</small>  <br /><small>[01-11-2018 02:32:26pm] Abhinav Kumar (ID# 1):</small>   <br /><small>[27-04-2019 10:34:43am] Abhinav Kumar (ID# 1):</small>   <br /><small>[27-04-2019 10:35:14am] Abhinav Kumar (ID# 1):</small>   <br /><small>[01-09-2019 01:40:16am] Abhinav Kumar (ID# 1):</small>   <br /><small>[02-09-2019 02:35:57pm] Abhinav Kumar (ID# 1):</small>  ', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

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
