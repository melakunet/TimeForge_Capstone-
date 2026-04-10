-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 10, 2026 at 04:28 PM
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
(24, 4, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 12:22:16'),
(25, 0, 'login_failed_invalid_user', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 13:52:25'),
(26, 9, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 13:52:39'),
(27, 4, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 14:06:36'),
(28, 9, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 12:54:19'),
(29, 13, 'user_registered', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 13:40:07'),
(30, 13, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 13:40:46'),
(31, 4, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 14:18:41'),
(32, 8, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 14:18:50');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
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

INSERT INTO `clients` (`id`, `company_id`, `client_name`, `company_name`, `email`, `phone`, `address`, `user_id`, `created_by`, `created_at`, `updated_at`, `is_active`) VALUES
(3, 1, 'Bob The Client', NULL, 'bob@timeforge.local', NULL, NULL, 3, 1, '2026-02-20 15:08:08', '2026-04-10 14:04:10', 1),
(6, 1, 'Sample Client', NULL, 'client1@example.com', NULL, NULL, 6, 1, '2026-02-20 15:08:08', '2026-04-10 14:04:10', 1),
(9, 1, 'Rose Etef', NULL, 'rose@timeforge.com', NULL, NULL, 9, 1, '2026-02-20 15:08:08', '2026-04-10 14:04:10', 1),
(10, 1, 'Azi go', 'az-flowers', 'azibeletu@gmail.com', '+164750043333', '200 dawntown , toronto', NULL, 11, '2026-02-27 13:53:58', '2026-04-10 14:04:10', 1),
(11, 4, 'Yetayal belay', 'menet education', 'ment@menet.com', '+251911963627', 'addis ababa ethiopia', NULL, 12, '2026-02-27 15:56:39', '2026-04-10 14:04:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `created_at`) VALUES
(1, 'Super Admin', '2026-04-10 14:04:10'),
(2, 'Administrator', '2026-04-10 14:04:10'),
(3, 'Etefworkie Melaku', '2026-04-10 14:04:10'),
(4, 'George ETEF', '2026-04-10 14:04:10'),
(5, 'Abegaile Ademe', '2026-04-10 14:04:10');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `tax_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Percentage applied at generation time',
  `subtotal` decimal(10,2) NOT NULL COMMENT 'Sum of all line items before tax',
  `tax_amount` decimal(10,2) NOT NULL COMMENT 'Computed tax on the subtotal',
  `total_amount` decimal(10,2) NOT NULL COMMENT 'subtotal + tax_amount',
  `notes` text DEFAULT NULL,
  `status` enum('draft','sent','viewed','overdue','partial','paid','completed','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `template` varchar(20) NOT NULL DEFAULT 'classic' COMMENT 'Visual template chosen at generation time',
  `sent_at` datetime DEFAULT NULL,
  `sent_to_email` varchar(100) DEFAULT NULL COMMENT 'Email address the invoice PDF was sent to',
  `email_sent_at` datetime DEFAULT NULL COMMENT 'Timestamp of the most recent email send',
  `viewed_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `partial_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_notes` text DEFAULT NULL,
  `client_feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `company_id`, `project_id`, `client_id`, `invoice_number`, `issue_date`, `due_date`, `tax_rate`, `subtotal`, `tax_amount`, `total_amount`, `notes`, `status`, `created_by`, `created_at`, `updated_at`, `template`, `sent_at`, `sent_to_email`, `email_sent_at`, `viewed_at`, `paid_at`, `partial_amount`, `payment_method`, `payment_reference`, `payment_notes`, `client_feedback`) VALUES
(1, 2, 4, 9, 'INV-202603-0004', '2026-03-27', '2026-04-26', 13.00, 11642.73, 1513.55, 13156.28, 'please see last month fee', 'draft', 4, '2026-03-27 14:58:42', '2026-04-10 14:21:51', 'classic', NULL, NULL, NULL, NULL, NULL, NULL, 'PayPal', NULL, 'please send me email when you pay', 'we are developed the frontend , i how you can see the scalability and the stability of the app'),
(6, 2, 4, 9, 'INV-202603-0004-R2', '2026-03-27', '2026-04-26', 13.00, 11642.73, 1513.55, 13156.28, NULL, 'draft', 4, '2026-03-27 15:40:50', '2026-04-10 14:04:10', 'modern', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
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
  `budget_alert_100` tinyint(1) DEFAULT 0,
  `tax_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Default tax percentage for invoices on this project'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `company_id`, `project_name`, `description`, `client_id`, `created_by`, `hourly_rate`, `budget`, `deadline`, `status`, `stage`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`, `deletion_requested`, `deletion_requested_by`, `deletion_requested_at`, `deletion_reason`, `progress_percentage`, `budget_alert_75`, `budget_alert_90`, `budget_alert_100`, `tax_rate`) VALUES
(1, 1, 'Website Redesign', NULL, 3, NULL, 50.00, NULL, NULL, 'active', 'planning', '2026-02-20 15:07:00', '2026-04-10 14:04:10', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 0.00),
(2, 1, 'SEO Audit', NULL, 3, NULL, 75.00, NULL, NULL, 'active', 'planning', '2026-02-20 15:07:00', '2026-04-10 14:04:10', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 0.00),
(4, 1, 'Melaku Digital Inc.', 'Melaku digital inc need website design. we must show luxury colors and font styles. and the landing page need to have animations.', 9, 9, 35.00, 20000.00, '2026-03-20', 'active', 'planning', '2026-02-20 15:47:42', '2026-04-10 14:04:10', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 0.00);

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
  `company_id` int(11) DEFAULT NULL,
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

INSERT INTO `time_entries` (`id`, `company_id`, `project_id`, `user_id`, `start_time`, `end_time`, `total_seconds`, `description`, `is_billable`, `is_idle_detected`, `status`, `entry_type`, `reviewed_by`, `reviewed_at`, `rejection_reason`, `created_at`, `updated_at`, `idle_seconds`, `discarded_idle_seconds`, `activity_score_avg`, `close_reason`) VALUES
(1, 1, 1, NULL, '2025-10-20 09:00:00', '2025-10-20 12:00:00', 10800, 'Initial layout design', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-02-27 14:16:21', '2026-04-10 14:04:10', 0, 0, NULL, 'manual'),
(2, 1, 1, NULL, '2025-10-21 14:00:00', '2025-10-21 16:30:00', 9000, 'Fixing navigation bar bug', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-02-27 14:16:21', '2026-04-10 14:04:10', 0, 0, NULL, 'manual'),
(3, 1, 4, 4, '2026-03-06 10:49:46', '2026-03-27 08:24:38', 1805692, 'Melaku Digital Inc.', 1, 0, 'abandoned', 'timer', NULL, NULL, NULL, '2026-03-06 15:49:46', '2026-04-10 14:04:10', 0, 0, NULL, 'abandoned'),
(4, 1, 4, 2, '2026-03-13 11:45:40', '2026-03-27 08:24:38', 1197538, 'Freelance work', 1, 0, 'approved', 'timer', 4, '2026-03-27 10:55:32', NULL, '2026-03-13 15:45:40', '2026-04-10 14:04:10', 0, 0, NULL, 'abandoned');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','freelancer','client') NOT NULL DEFAULT 'freelancer',
  `full_name` varchar(100) NOT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `business_tagline` varchar(200) DEFAULT NULL,
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

INSERT INTO `users` (`id`, `company_id`, `username`, `email`, `password`, `role`, `full_name`, `company_name`, `business_tagline`, `is_active`, `last_login`, `last_active_at`, `current_project_id`, `password_reset_token`, `password_reset_expires`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin_user', 'admin@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'admin', 'Super Admin', NULL, NULL, 1, '2026-03-13 11:23:25', NULL, NULL, NULL, NULL, '2026-01-30 13:57:53', '2026-04-10 14:04:10'),
(2, 1, 'dev_sarah', 'sarah@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'freelancer', 'Sarah Developer', NULL, NULL, 1, '2026-03-13 11:38:14', '2026-03-13 12:23:51', 4, NULL, NULL, '2026-01-30 13:57:53', '2026-04-10 14:04:10'),
(3, 1, 'client_bob', 'bob@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'client', 'Bob The Client', NULL, NULL, 1, '2026-03-13 11:19:05', NULL, NULL, NULL, NULL, '2026-01-30 13:57:53', '2026-04-10 14:04:10'),
(4, 2, 'admin', 'admin@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'admin', 'Administrator', NULL, NULL, 1, '2026-03-27 10:06:36', '2026-03-06 14:40:38', 4, NULL, NULL, '2026-02-06 14:47:00', '2026-04-10 14:04:10'),
(5, 1, 'freelancer1', 'freelancer1@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'freelancer', 'Sample Freelancer', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '2026-02-06 14:47:00', '2026-04-10 14:04:10'),
(6, 1, 'client1', 'client1@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'client', 'Sample Client', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '2026-02-06 14:47:00', '2026-04-10 14:04:10'),
(7, 1, 'sara', 'sarakey@timeforge.com', '$2y$10$gM4HuEs1G1zlqIFsoQEHIe0IpYEnig.Omygc4jJtONKBXMzvx/btG', 'freelancer', 'sara key', NULL, NULL, 1, '2026-02-13 11:27:27', NULL, NULL, NULL, NULL, '2026-02-13 14:10:55', '2026-04-10 14:04:10'),
(8, 3, 'Etef', 'etefmelaku@gmail.com', '$2y$10$zQe9n49AM0U3zq6S.O9uHOcxJpPRkkd719./6b6faaDJs0f1YPjIG', 'admin', 'Etefworkie Melaku', NULL, NULL, 1, '2026-04-10 10:18:50', NULL, NULL, NULL, NULL, '2026-02-13 15:58:57', '2026-04-10 14:18:50'),
(9, 1, 'Rose', 'rose@timeforge.com', '$2y$10$HQxSMJiCwzmnIV3x9TPDf.VvzwvgnZf7B1A8KNLgxwE4rtsN8aXYW', 'client', 'Rose Etef', NULL, NULL, 1, '2026-04-10 08:54:19', NULL, NULL, NULL, NULL, '2026-02-13 16:07:33', '2026-04-10 14:04:10'),
(10, 1, 'ademe', 'abelconltd@gmail.com', '$2y$10$Src9cEOBTf1n1zdRp3tANO9MaXg5XxzucgO2mBFsKhO5zlD5o7aeO', 'freelancer', 'abel', NULL, NULL, 1, '2026-02-20 08:14:09', NULL, NULL, NULL, NULL, '2026-02-20 13:13:52', '2026-04-10 14:04:10'),
(11, 1, 'Abi', 'gizieart@gmail.com', '$2y$10$afzkYYWImgw5/3VkSx0yYuMB9L0l6aiBlnLIjhOTlRV7r0yKyFNR.', 'freelancer', 'Abegaile', NULL, NULL, 1, '2026-02-27 08:06:31', NULL, NULL, NULL, NULL, '2026-02-27 13:06:12', '2026-04-10 14:04:10'),
(12, 4, 'George', 'wodebetf@gmail.com', '$2y$10$lK6lQtyW1sIncnFiPs7TKugzX1QCaPXSYQBHlIKFttRAj2el1DYGu', 'admin', 'George ETEF', NULL, NULL, 1, '2026-02-27 10:54:12', NULL, NULL, NULL, NULL, '2026-02-27 15:53:55', '2026-04-10 14:04:10'),
(13, 5, 'Abe', 'melakuetf@gmail.com', '$2y$10$lmKJzt1hDNuydhJApmK.FuKRNwvU3/cUMK3lfZMooiP7MN6z3kBSm', 'admin', 'Abegaile Ademe', NULL, NULL, 1, '2026-04-10 09:40:46', NULL, NULL, NULL, NULL, '2026-04-10 13:40:07', '2026-04-10 14:04:10');

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
  ADD KEY `is_active` (`is_active`),
  ADD KEY `clients_company_id` (`company_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_client` (`client_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `invoices_company_id` (`company_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `projects_ibfk_1` (`client_id`),
  ADD KEY `status` (`status`),
  ADD KEY `deleted_at` (`deleted_at`),
  ADD KEY `projects_company_id` (`company_id`);

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
  ADD KEY `time_entries_fk_reviewer` (`reviewed_by`),
  ADD KEY `time_entries_company_id` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `users_fk_current_project` (`current_project_id`),
  ADD KEY `company_id` (`company_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `clients_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
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
  ADD CONSTRAINT `time_entries_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `time_entries_fk_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `time_entries_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `time_entries_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_fk_current_project` FOREIGN KEY (`current_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
