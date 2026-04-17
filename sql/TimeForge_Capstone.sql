-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 17, 2026 at 04:09 PM
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
(32, 8, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 14:18:50'),
(33, 8, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 14:59:20'),
(34, 8, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-11 14:45:28'),
(35, 10, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-11 14:46:00'),
(36, 4, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-11 14:46:15'),
(37, 8, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 12:30:37'),
(38, 9, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 12:34:00'),
(39, 11, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 12:34:20'),
(40, 8, 'login_failed_wrong_password', '192.168.2.12', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 12:39:10'),
(41, 8, 'login_success', '192.168.2.12', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 12:39:17');

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
(11, 4, 'Yetayal belay', 'menet education', 'ment@menet.com', '+251911963627', 'addis ababa ethiopia', NULL, 12, '2026-02-27 15:56:39', '2026-04-10 14:04:10', 1),
(12, 3, 'Abegaile', 'Novelnet', 'melakuetf@gmail.com', '+16477650078', '100 Gamble ave, Toronto,ON. 2H2 K4M', NULL, 8, '2026-04-10 15:02:47', '2026-04-10 15:02:47', 1);

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
(1, 2, 4, 9, 'INV-202603-0004', '2026-03-27', '2026-04-26', 13.00, 11642.73, 1513.55, 13156.28, 'please see last month fee', 'sent', 4, '2026-03-27 14:58:42', '2026-04-11 14:57:59', 'classic', '2026-04-11 10:56:48', 'gizieart@gmail.com', '2026-04-11 10:57:59', NULL, NULL, NULL, 'PayPal', NULL, 'please send me email when you pay', 'we are developed the frontend , i how you can see the scalability and the stability of the app'),
(6, 2, 4, 9, 'INV-202603-0004-R2', '2026-03-27', '2026-04-26', 13.00, 11642.73, 1513.55, 13156.28, NULL, 'draft', 4, '2026-03-27 15:40:50', '2026-04-11 15:07:22', 'classic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'we have done the UI/UX'),
(7, NULL, 5, 12, 'INV-202604-0005', '2026-04-10', '2026-05-10', 5.00, 9.57, 0.48, 10.05, 'please confirm this invoice', 'draft', 8, '2026-04-10 15:37:25', '2026-04-10 15:37:25', 'corporate', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, NULL, 5, 12, 'INV-202604-0005-R2', '2026-04-10', '2026-05-10', 5.00, 9.57, 0.48, 10.05, 'confirm please you have received this.', 'draft', 8, '2026-04-10 15:48:28', '2026-04-10 15:48:28', 'corporate', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
  `screenshots_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = auto-capture screenshots while timer runs, 0 = disabled',
  `tax_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Default tax percentage for invoices on this project'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `company_id`, `project_name`, `description`, `client_id`, `created_by`, `hourly_rate`, `budget`, `deadline`, `status`, `stage`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`, `deletion_requested`, `deletion_requested_by`, `deletion_requested_at`, `deletion_reason`, `progress_percentage`, `budget_alert_75`, `budget_alert_90`, `budget_alert_100`, `screenshots_enabled`, `tax_rate`) VALUES
(1, 1, 'Website Redesign', NULL, 3, NULL, 50.00, NULL, NULL, 'active', 'planning', '2026-02-20 15:07:00', '2026-04-10 14:04:10', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 1, 0.00),
(2, 1, 'SEO Audit', NULL, 3, NULL, 75.00, NULL, NULL, 'active', 'planning', '2026-02-20 15:07:00', '2026-04-10 14:04:10', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 1, 0.00),
(4, 1, 'Melaku Digital Inc.', 'Melaku digital inc need website design. we must show luxury colors and font styles. and the landing page need to have animations.', 9, 9, 35.00, 20000.00, '2026-03-20', 'active', 'planning', '2026-02-20 15:47:42', '2026-04-10 14:04:10', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 1, 0.00),
(5, 3, 'WEbsite design', 'we will redesign the novelnet website ', 12, 8, 24.00, 5000.00, '2026-04-30', 'active', 'planning', '2026-04-10 15:09:34', '2026-04-10 15:09:34', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `screenshots`
--

DROP TABLE IF EXISTS `screenshots`;
CREATE TABLE `screenshots` (
  `id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL COMMENT 'Relative path under uploads/screenshots/',
  `file_size_kb` int(11) NOT NULL DEFAULT 0,
  `activity_score_at_capture` int(11) NOT NULL DEFAULT 0 COMMENT 'Mouse+key events recorded at time of capture',
  `captured_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(15, 4, 2, '2026-03-13 12:23:51', 0, 0, 0),
(16, 5, 8, '2026-04-10 11:11:07', 152, 0, 152),
(17, 5, 8, '2026-04-10 11:12:07', 0, 0, 0),
(18, 5, 8, '2026-04-10 11:13:07', 0, 0, 0),
(19, 5, 8, '2026-04-10 11:14:07', 0, 0, 0),
(20, 5, 8, '2026-04-10 11:15:07', 0, 0, 0),
(21, 5, 8, '2026-04-10 11:16:07', 0, 0, 0),
(22, 5, 8, '2026-04-10 11:17:07', 123, 0, 123),
(23, 5, 8, '2026-04-10 11:18:37', 0, 0, 0),
(24, 5, 8, '2026-04-10 11:19:07', 0, 0, 0),
(25, 5, 8, '2026-04-10 11:20:07', 0, 0, 0),
(26, 5, 8, '2026-04-10 11:21:07', 0, 0, 0),
(27, 5, 8, '2026-04-10 11:22:07', 0, 0, 0),
(28, 5, 8, '2026-04-10 11:23:07', 0, 0, 0),
(29, 5, 8, '2026-04-10 11:24:07', 0, 0, 0),
(30, 5, 8, '2026-04-10 11:25:07', 0, 0, 0),
(31, 5, 8, '2026-04-10 11:26:07', 0, 0, 0),
(32, 5, 8, '2026-04-10 11:27:37', 0, 0, 0),
(33, 5, 8, '2026-04-10 11:28:37', 0, 0, 0),
(34, 5, 8, '2026-04-10 11:29:37', 0, 0, 0),
(35, 5, 8, '2026-04-10 11:30:37', 0, 0, 0),
(36, 5, 8, '2026-04-10 11:31:37', 0, 0, 0),
(37, 5, 8, '2026-04-10 11:32:37', 0, 0, 0),
(38, 5, 8, '2026-04-10 11:33:22', 0, 0, 0),
(39, 6, 11, '2026-04-17 08:36:28', 522, 0, 522),
(40, 6, 11, '2026-04-17 08:37:28', 4, 0, 4),
(41, 6, 11, '2026-04-17 08:38:28', 1, 0, 1),
(42, 6, 11, '2026-04-17 08:39:28', 0, 0, 0),
(43, 6, 11, '2026-04-17 08:40:29', 1, 0, 1),
(44, 7, 11, '2026-04-17 08:41:32', 505, 0, 505),
(45, 7, 11, '2026-04-17 08:42:32', 91, 0, 91),
(46, 7, 11, '2026-04-17 08:43:32', 50, 0, 50),
(47, 7, 11, '2026-04-17 08:44:32', 0, 0, 0),
(48, 7, 11, '2026-04-17 08:45:32', 52, 0, 52),
(49, 7, 11, '2026-04-17 08:46:32', 1, 0, 1),
(50, 7, 11, '2026-04-17 08:47:32', 1, 0, 1),
(51, 7, 11, '2026-04-17 08:48:32', 0, 0, 0),
(52, 7, 11, '2026-04-17 08:49:32', 4, 0, 4),
(53, 7, 11, '2026-04-17 08:50:32', 0, 0, 0),
(54, 7, 11, '2026-04-17 08:51:32', 0, 0, 0),
(55, 7, 11, '2026-04-17 08:52:32', 0, 0, 0),
(56, 7, 11, '2026-04-17 08:53:32', 0, 0, 0),
(57, 7, 11, '2026-04-17 08:54:32', 0, 0, 0),
(58, 7, 11, '2026-04-17 08:55:32', 0, 0, 0),
(59, 7, 11, '2026-04-17 08:56:32', 0, 0, 0),
(60, 7, 11, '2026-04-17 08:57:32', 0, 0, 0),
(61, 7, 11, '2026-04-17 08:58:32', 0, 0, 0),
(62, 7, 11, '2026-04-17 08:59:32', 0, 0, 0),
(63, 7, 11, '2026-04-17 09:00:32', 2, 0, 2),
(64, 7, 11, '2026-04-17 09:01:32', 2, 0, 2),
(65, 7, 11, '2026-04-17 09:02:32', 0, 0, 0),
(66, 7, 11, '2026-04-17 09:03:32', 4, 0, 4),
(67, 7, 11, '2026-04-17 09:04:32', 2, 0, 2),
(68, 7, 11, '2026-04-17 09:05:32', 2, 0, 2),
(69, 7, 11, '2026-04-17 09:06:32', 0, 0, 0),
(70, 7, 11, '2026-04-17 09:07:32', 0, 0, 0),
(71, 7, 11, '2026-04-17 09:08:32', 0, 0, 0),
(72, 7, 11, '2026-04-17 09:09:32', 0, 0, 0),
(73, 7, 11, '2026-04-17 09:10:32', 0, 0, 0),
(74, 7, 11, '2026-04-17 09:11:32', 0, 0, 0),
(75, 7, 11, '2026-04-17 09:12:32', 0, 0, 0),
(76, 7, 11, '2026-04-17 09:13:32', 0, 0, 0),
(77, 7, 11, '2026-04-17 09:14:32', 0, 0, 0),
(78, 7, 11, '2026-04-17 09:15:32', 0, 0, 0),
(79, 7, 11, '2026-04-17 09:16:32', 0, 0, 0),
(80, 7, 11, '2026-04-17 09:17:32', 0, 0, 0),
(81, 7, 11, '2026-04-17 09:18:32', 0, 0, 0),
(82, 7, 11, '2026-04-17 09:19:32', 0, 0, 0),
(83, 7, 11, '2026-04-17 09:20:32', 0, 0, 0),
(84, 7, 11, '2026-04-17 09:21:32', 0, 0, 0),
(85, 7, 11, '2026-04-17 09:22:32', 0, 0, 0),
(86, 7, 11, '2026-04-17 09:23:32', 0, 0, 0),
(87, 7, 11, '2026-04-17 09:24:32', 0, 0, 0),
(88, 7, 11, '2026-04-17 09:25:32', 426, 0, 426),
(89, 7, 11, '2026-04-17 09:27:09', 0, 0, 0),
(90, 7, 11, '2026-04-17 09:27:32', 0, 0, 0),
(91, 7, 11, '2026-04-17 09:28:32', 0, 0, 0),
(92, 7, 11, '2026-04-17 09:29:32', 0, 0, 0),
(93, 7, 11, '2026-04-17 09:30:32', 0, 0, 0),
(94, 7, 11, '2026-04-17 09:31:32', 0, 0, 0),
(95, 7, 11, '2026-04-17 09:32:32', 0, 0, 0),
(96, 7, 11, '2026-04-17 09:33:32', 0, 0, 0),
(97, 7, 11, '2026-04-17 09:34:32', 0, 0, 0),
(98, 7, 11, '2026-04-17 09:36:09', 0, 0, 0),
(99, 7, 11, '2026-04-17 09:37:09', 0, 0, 0),
(100, 7, 11, '2026-04-17 09:38:09', 0, 0, 0),
(101, 7, 11, '2026-04-17 09:39:09', 0, 0, 0),
(102, 7, 11, '2026-04-17 09:40:09', 0, 0, 0),
(103, 7, 11, '2026-04-17 09:41:09', 0, 0, 0),
(104, 7, 11, '2026-04-17 09:42:09', 0, 0, 0),
(105, 7, 11, '2026-04-17 09:43:09', 0, 0, 0),
(106, 7, 11, '2026-04-17 09:44:09', 0, 0, 0),
(107, 7, 11, '2026-04-17 09:45:09', 0, 0, 0),
(108, 7, 11, '2026-04-17 09:46:09', 0, 0, 0),
(109, 7, 11, '2026-04-17 09:47:09', 0, 0, 0),
(110, 7, 11, '2026-04-17 09:48:09', 0, 0, 0),
(111, 7, 11, '2026-04-17 09:49:09', 0, 0, 0),
(112, 7, 11, '2026-04-17 09:50:09', 0, 0, 0),
(113, 7, 11, '2026-04-17 09:51:09', 0, 0, 0),
(114, 7, 11, '2026-04-17 09:52:09', 0, 0, 0),
(115, 7, 11, '2026-04-17 09:53:09', 0, 0, 0),
(116, 7, 11, '2026-04-17 09:54:09', 0, 0, 0),
(117, 7, 11, '2026-04-17 09:55:09', 0, 0, 0),
(118, 7, 11, '2026-04-17 09:56:09', 0, 0, 0),
(119, 7, 11, '2026-04-17 09:57:09', 0, 0, 0),
(120, 7, 11, '2026-04-17 09:58:09', 0, 0, 0),
(121, 7, 11, '2026-04-17 09:59:09', 0, 0, 0),
(122, 7, 11, '2026-04-17 10:00:09', 0, 0, 0),
(123, 7, 11, '2026-04-17 10:01:09', 0, 0, 0),
(124, 7, 11, '2026-04-17 10:02:09', 0, 0, 0),
(125, 7, 11, '2026-04-17 10:03:09', 0, 0, 0),
(126, 7, 11, '2026-04-17 10:04:09', 0, 0, 0),
(127, 7, 11, '2026-04-17 10:05:09', 0, 0, 0),
(128, 7, 11, '2026-04-17 10:06:09', 0, 0, 0),
(129, 7, 11, '2026-04-17 10:07:09', 0, 0, 0),
(130, 7, 11, '2026-04-17 10:08:09', 0, 0, 0),
(131, 7, 11, '2026-04-17 10:09:09', 0, 0, 0);

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
(4, 1, 4, 2, '2026-03-13 11:45:40', '2026-03-27 08:24:38', 1197538, 'Freelance work', 1, 0, 'approved', 'timer', 4, '2026-03-27 10:55:32', NULL, '2026-03-13 15:45:40', '2026-04-10 14:04:10', 0, 0, NULL, 'abandoned'),
(5, 3, 5, 8, '2026-04-10 11:10:07', '2026-04-10 11:34:03', 1436, 'Design: start phase 1', 1, 0, 'approved', 'timer', 8, '2026-04-10 11:34:28', NULL, '2026-04-10 15:10:07', '2026-04-10 15:34:28', 0, 0, NULL, 'manual'),
(6, 1, 4, 11, '2026-04-17 08:35:28', '2026-04-17 08:40:29', 301, 'General work', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-04-17 12:35:28', '2026-04-17 12:40:29', 0, 0, NULL, 'manual'),
(7, 1, 4, 11, '2026-04-17 08:40:32', NULL, 0, 'General work', 1, 0, 'running', 'timer', NULL, NULL, NULL, '2026-04-17 12:40:32', '2026-04-17 12:40:32', 0, 0, NULL, 'manual');

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
  `company_logo` varchar(255) DEFAULT NULL,
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

INSERT INTO `users` (`id`, `company_id`, `username`, `email`, `password`, `role`, `full_name`, `company_name`, `business_tagline`, `company_logo`, `is_active`, `last_login`, `last_active_at`, `current_project_id`, `password_reset_token`, `password_reset_expires`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin_user', 'admin@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'admin', 'Super Admin', NULL, NULL, NULL, 1, '2026-03-13 11:23:25', NULL, NULL, NULL, NULL, '2026-01-30 13:57:53', '2026-04-10 14:04:10'),
(2, 1, 'dev_sarah', 'sarah@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'freelancer', 'Sarah Developer', NULL, NULL, NULL, 1, '2026-03-13 11:38:14', '2026-03-13 12:23:51', 4, NULL, NULL, '2026-01-30 13:57:53', '2026-04-10 14:04:10'),
(3, 1, 'client_bob', 'bob@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'client', 'Bob The Client', NULL, NULL, NULL, 1, '2026-03-13 11:19:05', NULL, NULL, NULL, NULL, '2026-01-30 13:57:53', '2026-04-10 14:04:10'),
(4, 2, 'admin', 'admin@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'admin', 'Administrator', 'Melaku Digital Inc.', NULL, 'images/logos/4_logo.png', 1, '2026-04-11 10:46:15', '2026-03-06 14:40:38', 4, NULL, NULL, '2026-02-06 14:47:00', '2026-04-11 15:05:12'),
(5, 1, 'freelancer1', 'freelancer1@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'freelancer', 'Sample Freelancer', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '2026-02-06 14:47:00', '2026-04-10 14:04:10'),
(6, 1, 'client1', 'client1@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'client', 'Sample Client', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '2026-02-06 14:47:00', '2026-04-10 14:04:10'),
(7, 1, 'sara', 'sarakey@timeforge.com', '$2y$10$gM4HuEs1G1zlqIFsoQEHIe0IpYEnig.Omygc4jJtONKBXMzvx/btG', 'freelancer', 'sara key', NULL, NULL, NULL, 1, '2026-02-13 11:27:27', NULL, NULL, NULL, NULL, '2026-02-13 14:10:55', '2026-04-10 14:04:10'),
(8, 3, 'Etef', 'etefmelaku@gmail.com', '$2y$10$zQe9n49AM0U3zq6S.O9uHOcxJpPRkkd719./6b6faaDJs0f1YPjIG', 'admin', 'Etefworkie Melaku', 'Melaku Digital Inc.', 'Web Design and software development', NULL, 1, '2026-04-17 08:39:17', '2026-04-10 11:33:22', 5, NULL, NULL, '2026-02-13 15:58:57', '2026-04-17 12:39:17'),
(9, 1, 'Rose', 'rose@timeforge.com', '$2y$10$HQxSMJiCwzmnIV3x9TPDf.VvzwvgnZf7B1A8KNLgxwE4rtsN8aXYW', 'client', 'Rose Etef', NULL, NULL, NULL, 1, '2026-04-10 08:54:19', NULL, NULL, NULL, NULL, '2026-02-13 16:07:33', '2026-04-10 14:04:10'),
(10, 1, 'ademe', 'abelconltd@gmail.com', '$2y$10$Src9cEOBTf1n1zdRp3tANO9MaXg5XxzucgO2mBFsKhO5zlD5o7aeO', 'freelancer', 'abel', NULL, NULL, NULL, 1, '2026-02-20 08:14:09', NULL, NULL, NULL, NULL, '2026-02-20 13:13:52', '2026-04-10 14:04:10'),
(11, 1, 'Abi', 'gizieart@gmail.com', '$2y$10$afzkYYWImgw5/3VkSx0yYuMB9L0l6aiBlnLIjhOTlRV7r0yKyFNR.', 'freelancer', 'Abegaile', NULL, NULL, NULL, 1, '2026-04-17 08:34:20', '2026-04-17 10:09:09', 4, NULL, NULL, '2026-02-27 13:06:12', '2026-04-17 14:09:09'),
(12, 4, 'George', 'wodebetf@gmail.com', '$2y$10$lK6lQtyW1sIncnFiPs7TKugzX1QCaPXSYQBHlIKFttRAj2el1DYGu', 'admin', 'George ETEF', NULL, NULL, NULL, 1, '2026-02-27 10:54:12', NULL, NULL, NULL, NULL, '2026-02-27 15:53:55', '2026-04-10 14:04:10'),
(13, 5, 'Abe', 'melakuetf@gmail.com', '$2y$10$lmKJzt1hDNuydhJApmK.FuKRNwvU3/cUMK3lfZMooiP7MN6z3kBSm', 'admin', 'Abegaile Ademe', NULL, NULL, NULL, 1, '2026-04-10 09:40:46', NULL, NULL, NULL, NULL, '2026-04-10 13:40:07', '2026-04-10 14:04:10');

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
-- Indexes for table `screenshots`
--
ALTER TABLE `screenshots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entry` (`entry_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_project` (`project_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `screenshots`
--
ALTER TABLE `screenshots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_activity`
--
ALTER TABLE `session_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `time_entries`
--
ALTER TABLE `time_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Constraints for table `screenshots`
--
ALTER TABLE `screenshots`
  ADD CONSTRAINT `screenshots_fk_entry` FOREIGN KEY (`entry_id`) REFERENCES `time_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `screenshots_fk_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `screenshots_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
