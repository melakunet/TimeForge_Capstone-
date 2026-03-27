-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 27, 2026 at 02:06 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `TimeForge_Capstone`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login_success', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', '2026-02-06 15:30:00'),
(2, 2, 'login_success', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', '2026-02-06 15:35:00'),
(5, 1, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 14:36:32'),
(6, 1, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 14:48:41'),
(7, 9, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 15:27:33'),
(8, 0, 'login_failed_invalid_user', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 15:43:58'),
(9, 0, 'login_failed_invalid_user', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 15:44:15'),
(10, 12, 'user_registered', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 15:53:55'),
(11, 12, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 15:54:12'),
(12, 4, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 15:40:14'),
(13, 4, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 15:41:02'),
(14, 4, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 15:49:14'),
(15, 12, 'login_failed_wrong_password', '192.168.2.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 15:55:14'),
(16, 4, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:48:28'),
(17, 4, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 13:40:57'),
(18, 6, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:06:57'),
(19, 0, 'login_failed_invalid_user', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:07:19'),
(20, 3, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:08:06'),
(21, 3, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:19:05'),
(22, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:23:25'),
(23, 2, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:38:14'),
(24, 4, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 12:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `client_name`, `company_name`, `email`, `phone`, `address`, `user_id`, `created_by`, `created_at`, `updated_at`, `is_active`) VALUES
(3, 'Bob The Client', NULL, 'bob@timeforge.local', NULL, NULL, 3, 1, '2026-02-20 15:08:08', '2026-02-20 15:08:08', 1),
(6, 'Sample Client', NULL, 'client1@example.com', NULL, NULL, 6, 1, '2026-02-20 15:08:08', '2026-02-20 15:08:08', 1),
(9, 'Rose Etef', NULL, 'rose@timeforge.com', NULL, NULL, 9, 1, '2026-02-20 15:08:08', '2026-02-20 15:08:08', 1),
(10, 'Azi go', 'az-flowers', 'azibeletu@gmail.com', '+164750043333', '200 dawntown , toronto', NULL, 11, '2026-02-27 13:53:58', '2026-02-27 13:53:58', 1),
(11, 'Yetayal belay', 'menet education', 'ment@menet.com', '+251911963627', 'addis ababa ethiopia', NULL, 12, '2026-02-27 15:56:39', '2026-02-27 15:56:39', 1);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `project_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT 0.00,
  `budget` decimal(10,2) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('active','completed','archived') DEFAULT 'active',
  `stage` enum('planning','in_progress','review','testing','on_hold','completed','archived') DEFAULT 'planning',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deletion_requested` tinyint(1) DEFAULT 0,
  `deletion_requested_by` int(11) DEFAULT NULL,
  `deletion_requested_at` timestamp NULL DEFAULT NULL,
  `deletion_reason` text DEFAULT NULL,
  `progress_percentage` int(11) DEFAULT 0,
  `budget_alert_75` tinyint(1) DEFAULT 0,
  `budget_alert_90` tinyint(1) DEFAULT 0,
  `budget_alert_100` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_name`, `description`, `client_id`, `created_by`, `hourly_rate`, `budget`, `deadline`, `status`, `stage`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`, `deletion_requested`, `deletion_requested_by`, `deletion_requested_at`, `deletion_reason`, `progress_percentage`, `budget_alert_75`, `budget_alert_90`, `budget_alert_100`) VALUES
(1, 'Website Redesign', NULL, 3, NULL, 50.00, NULL, NULL, 'active', 'planning', '2026-02-20 15:07:00', '2026-02-20 15:07:00', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0),
(2, 'SEO Audit', NULL, 3, NULL, 75.00, NULL, NULL, 'active', 'planning', '2026-02-20 15:07:00', '2026-02-20 15:07:00', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0),
(4, 'Melaku Digital Inc.', 'Melaku digital inc need website design. we must show luxury colors and font styles. and the landing page need to have animations.', 9, 9, 35.00, 20000.00, '2026-03-20', 'active', 'planning', '2026-02-20 15:47:42', '2026-02-20 15:47:42', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `session_activity`
--

DROP TABLE IF EXISTS `session_activity`;
CREATE TABLE `session_activity` (
  `id` int(11) NOT NULL,
  `time_entry_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recorded_at` datetime NOT NULL,
  `mouse_events` int(11) DEFAULT 0 COMMENT 'Mouse moves + clicks in this minute',
  `key_events` int(11) DEFAULT 0 COMMENT 'Keystrokes in this minute',
  `activity_score` int(11) DEFAULT 0 COMMENT 'Total events (mouse + key) in this minute'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session_activity`
--

INSERT INTO `session_activity` (`id`, `time_entry_id`, `user_id`, `recorded_at`, `mouse_events`, `key_events`, `activity_score`) VALUES
(1, 4, 2, '2026-03-13 11:46:40', 154, 0, 154),
(2, 4, 2, '2026-03-13 11:47:40', 0, 0, 0),
(3, 4, 2, '2026-03-13 11:49:56', 165, 0, 165),
(4, 4, 2, '2026-03-13 11:50:56', 0, 0, 0),
(5, 4, 2, '2026-03-13 11:51:56', 0, 0, 0),
(6, 4, 2, '2026-03-13 11:52:56', 0, 0, 0),
(7, 4, 2, '2026-03-13 11:53:56', 0, 0, 0),
(8, 4, 2, '2026-03-13 11:54:56', 0, 0, 0),
(9, 4, 2, '2026-03-13 11:55:56', 0, 0, 0),
(10, 4, 2, '2026-03-13 11:56:56', 0, 0, 0),
(11, 4, 2, '2026-03-13 11:57:56', 0, 0, 0),
(12, 4, 2, '2026-03-13 11:58:56', 0, 0, 0),
(13, 4, 2, '2026-03-13 12:00:08', 0, 0, 0),
(14, 4, 2, '2026-03-13 12:01:08', 0, 0, 0),
(15, 4, 2, '2026-03-13 12:23:51', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `time_entries`
--

DROP TABLE IF EXISTS `time_entries`;
CREATE TABLE `time_entries` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `total_seconds` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `is_billable` tinyint(1) DEFAULT 1,
  `is_idle_detected` tinyint(1) DEFAULT 0,
  `status` enum('running','paused','completed','pending','approved','rejected','abandoned') DEFAULT 'completed',
  `entry_type` enum('timer','manual') DEFAULT 'timer',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `rejection_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `idle_seconds` int(11) DEFAULT 0 COMMENT 'Total idle seconds detected during this session',
  `discarded_idle_seconds` int(11) DEFAULT 0 COMMENT 'Idle seconds the user chose to discard',
  `activity_score_avg` float DEFAULT NULL COMMENT 'Average activity events per heartbeat minute',
  `close_reason` enum('manual','auto','abandoned') DEFAULT 'manual' COMMENT 'How the session was closed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_entries`
--

INSERT INTO `time_entries` (`id`, `project_id`, `user_id`, `start_time`, `end_time`, `total_seconds`, `description`, `is_billable`, `is_idle_detected`, `status`, `entry_type`, `reviewed_by`, `reviewed_at`, `rejection_reason`, `created_at`, `updated_at`, `idle_seconds`, `discarded_idle_seconds`, `activity_score_avg`, `close_reason`) VALUES
(1, 1, NULL, '2025-10-20 09:00:00', '2025-10-20 12:00:00', 10800, 'Initial layout design', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-02-27 14:16:21', '2026-03-06 14:30:13', 0, 0, NULL, 'manual'),
(2, 1, NULL, '2025-10-21 14:00:00', '2025-10-21 16:30:00', 9000, 'Fixing navigation bar bug', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-02-27 14:16:21', '2026-03-06 14:30:13', 0, 0, NULL, 'manual'),
(3, 4, 4, '2026-03-06 10:49:46', '2026-03-27 08:24:38', 1805692, 'Melaku Digital Inc.', 1, 0, 'abandoned', 'timer', NULL, NULL, NULL, '2026-03-06 15:49:46', '2026-03-27 12:24:38', 0, 0, NULL, 'abandoned'),
(4, 4, 2, '2026-03-13 11:45:40', '2026-03-27 08:24:38', 1197538, 'Freelance work', 1, 0, 'abandoned', 'timer', NULL, NULL, NULL, '2026-03-13 15:45:40', '2026-03-27 12:24:38', 0, 0, NULL, 'abandoned');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','freelancer','client') NOT NULL DEFAULT 'freelancer',
  `full_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `last_active_at` datetime DEFAULT NULL,
  `current_project_id` int(11) DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `full_name`, `is_active`, `last_login`, `last_active_at`, `current_project_id`, `password_reset_token`, `password_reset_expires`, `created_at`, `updated_at`) VALUES
(1, 'admin_user', 'admin@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'admin', 'Super Admin', 1, '2026-03-13 11:23:25', NULL, NULL, NULL, NULL, '2026-01-30 13:57:53', '2026-03-13 15:23:25'),
(2, 'dev_sarah', 'sarah@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'freelancer', 'Sarah Developer', 1, '2026-03-13 11:38:14', '2026-03-13 12:23:51', 4, NULL, NULL, '2026-01-30 13:57:53', '2026-03-13 16:23:51'),
(3, 'client_bob', 'bob@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'client', 'Bob The Client', 1, '2026-03-13 11:19:05', NULL, NULL, NULL, NULL, '2026-01-30 13:57:53', '2026-03-13 15:19:05'),
(4, 'admin', 'admin@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'admin', 'Administrator', 1, '2026-03-27 08:22:16', '2026-03-06 14:40:38', 4, NULL, NULL, '2026-02-06 14:47:00', '2026-03-27 12:22:16'),
(5, 'freelancer1', 'freelancer1@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'freelancer', 'Sample Freelancer', 1, NULL, NULL, NULL, NULL, NULL, '2026-02-06 14:47:00', '2026-03-13 15:17:32'),
(6, 'client1', 'client1@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'client', 'Sample Client', 1, NULL, NULL, NULL, NULL, NULL, '2026-02-06 14:47:00', '2026-03-13 15:17:32'),
(7, 'sara', 'sarakey@timeforge.com', '$2y$10$gM4HuEs1G1zlqIFsoQEHIe0IpYEnig.Omygc4jJtONKBXMzvx/btG', 'freelancer', 'sara key', 1, '2026-02-13 11:27:27', NULL, NULL, NULL, NULL, '2026-02-13 14:10:55', '2026-02-13 16:27:27'),
(8, 'Etef', 'etefmelaku@gmail.com', '$2y$10$zQe9n49AM0U3zq6S.O9uHOcxJpPRkkd719./6b6faaDJs0f1YPjIG', 'admin', 'Etefworkie Melaku', 1, '2026-02-20 10:48:13', NULL, NULL, NULL, NULL, '2026-02-13 15:58:57', '2026-02-20 15:48:13'),
(9, 'Rose', 'rose@timeforge.com', '$2y$10$HQxSMJiCwzmnIV3x9TPDf.VvzwvgnZf7B1A8KNLgxwE4rtsN8aXYW', 'client', 'Rose Etef', 1, '2026-02-27 10:27:33', NULL, NULL, NULL, NULL, '2026-02-13 16:07:33', '2026-02-27 15:27:33'),
(10, 'ademe', 'abelconltd@gmail.com', '$2y$10$Src9cEOBTf1n1zdRp3tANO9MaXg5XxzucgO2mBFsKhO5zlD5o7aeO', 'freelancer', 'abel', 1, '2026-02-20 08:14:09', NULL, NULL, NULL, NULL, '2026-02-20 13:13:52', '2026-02-20 13:14:09'),
(11, 'Abi', 'gizieart@gmail.com', '$2y$10$afzkYYWImgw5/3VkSx0yYuMB9L0l6aiBlnLIjhOTlRV7r0yKyFNR.', 'freelancer', 'Abegaile', 1, '2026-02-27 08:06:31', NULL, NULL, NULL, NULL, '2026-02-27 13:06:12', '2026-02-27 13:06:31'),
(12, 'George', 'wodebetf@gmail.com', '$2y$10$lK6lQtyW1sIncnFiPs7TKugzX1QCaPXSYQBHlIKFttRAj2el1DYGu', 'admin', 'George ETEF', 1, '2026-02-27 10:54:12', NULL, NULL, NULL, NULL, '2026-02-27 15:53:55', '2026-02-27 15:54:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `projects_ibfk_1` (`client_id`),
  ADD KEY `status` (`status`),
  ADD KEY `deleted_at` (`deleted_at`);

--
-- Indexes for table `session_activity`
--
ALTER TABLE `session_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entry` (`time_entry_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_time` (`recorded_at`);

--
-- Indexes for table `time_entries`
--
ALTER TABLE `time_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `time_entries_fk_reviewer` (`reviewed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `users_fk_current_project` (`current_project_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `session_activity`
--
ALTER TABLE `session_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `time_entries`
--
ALTER TABLE `time_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `clients_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `session_activity`
--
ALTER TABLE `session_activity`
  ADD CONSTRAINT `session_activity_ibfk_1` FOREIGN KEY (`time_entry_id`) REFERENCES `time_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `session_activity_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `time_entries`
--
ALTER TABLE `time_entries`
  ADD CONSTRAINT `time_entries_fk_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `time_entries_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `time_entries_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_fk_current_project` FOREIGN KEY (`current_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
