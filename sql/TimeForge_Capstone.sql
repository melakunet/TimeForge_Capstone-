-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 08, 2026 at 04:07 PM
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
CREATE DATABASE IF NOT EXISTS `TimeForge_Capstone` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `TimeForge_Capstone`;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `company_id`, `action`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 1, 'login_success', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', '2026-02-06 15:30:00'),
(2, 2, 1, 'login_success', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', '2026-02-06 15:35:00'),
(5, 1, 1, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 14:36:32'),
(6, 1, 1, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 14:48:41'),
(7, 9, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 15:27:33'),
(8, 0, NULL, 'login_failed_invalid_user', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 15:43:58'),
(9, 0, NULL, 'login_failed_invalid_user', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 15:44:15'),
(10, 12, 4, 'user_registered', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 15:53:55'),
(11, 12, 4, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 15:54:12'),
(12, 4, 2, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 15:40:14'),
(13, 4, 2, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 15:41:02'),
(14, 4, 2, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 15:49:14'),
(15, 12, 4, 'login_failed_wrong_password', '192.168.2.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 15:55:14'),
(16, 4, 2, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 14:48:28'),
(17, 4, 2, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 13:40:57'),
(18, 6, 1, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:06:57'),
(19, 0, NULL, 'login_failed_invalid_user', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:07:19'),
(20, 3, 1, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:08:06'),
(21, 3, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:19:05'),
(22, 1, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:23:25'),
(23, 2, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-13 15:38:14'),
(24, 4, 2, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 12:22:16'),
(25, 0, NULL, 'login_failed_invalid_user', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 13:52:25'),
(26, 9, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 13:52:39'),
(27, 4, 2, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 14:06:36'),
(28, 9, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 12:54:19'),
(29, 13, 5, 'user_registered', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 13:40:07'),
(30, 13, 5, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 13:40:46'),
(31, 4, 2, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 14:18:41'),
(32, 8, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 14:18:50'),
(33, 8, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-10 14:59:20'),
(34, 8, 1, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-11 14:45:28'),
(35, 10, 1, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-11 14:46:00'),
(36, 4, 2, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-11 14:46:15'),
(37, 8, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 12:30:37'),
(38, 9, 1, 'login_failed_wrong_password', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 12:34:00'),
(39, 11, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 12:34:20'),
(40, 8, 1, 'login_failed_wrong_password', '192.168.2.12', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 12:39:10'),
(41, 8, 1, 'login_success', '192.168.2.12', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 12:39:17'),
(42, 8, 1, 'login_success', '192.168.2.12', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-17 14:42:56'),
(43, 4, 2, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 12:14:52'),
(44, 11, 1, 'login_success', '192.168.2.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 12:19:04'),
(45, 8, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 12:34:25'),
(46, 8, 1, 'login_success', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 12:44:17'),
(47, 11, 1, 'login_success', '192.168.2.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 12:45:45'),
(48, 14, 6, 'user_registered', '192.168.2.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 13:31:47'),
(49, 14, 6, 'login_success', '192.168.2.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 13:32:03'),
(50, 0, NULL, 'System auto-abandoned 1 stale timers', '127.0.0.1', NULL, '2026-05-01 13:50:01'),
(51, 0, NULL, 'System auto-abandoned 1 stale timers', '127.0.0.1', NULL, '2026-05-01 14:50:00'),
(52, 11, 1, 'login_success', '192.168.2.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 15:15:55'),
(53, 0, NULL, 'login_failed_invalid_user', '192.168.2.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 15:28:01'),
(54, 0, NULL, 'login_failed_invalid_user', '192.168.2.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 15:28:26'),
(55, 9, 1, 'login_success', '192.168.2.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 15:28:54'),
(56, 0, NULL, 'System auto-abandoned 1 stale timers', '127.0.0.1', NULL, '2026-05-01 16:00:00'),
(57, 11, 1, 'login_success', '192.168.2.12', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-08 12:46:25');

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
(12, 3, 'Abegaile', 'Novelnet', 'melakuetf@gmail.com', '+16477650078', '100 Gamble ave, Toronto,ON. 2H2 K4M', NULL, 8, '2026-04-10 15:02:47', '2026-04-10 15:02:47', 1),
(13, 6, 'Melaku digital inc', 'MDI', 'info@melakudigitalinc.com', '16477650078', '200 Gateways blvd', NULL, 14, '2026-05-01 13:37:29', '2026-05-01 13:37:29', 1);

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
(5, 'Abegaile Ademe', '2026-04-10 14:04:10'),
(6, 'WubNet', '2026-05-01 13:31:47');

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

DROP TABLE IF EXISTS `company_settings`;
CREATE TABLE `company_settings` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `setting_key` varchar(80) NOT NULL,
  `setting_val` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_id`, `setting_key`, `setting_val`, `updated_at`) VALUES
(1, 1, 'company_display_name', 'Super Admin', '2026-05-01 09:34:37'),
(2, 2, 'company_display_name', 'Administrator', '2026-05-01 09:34:37'),
(3, 3, 'company_display_name', 'Etefworkie Melaku', '2026-05-01 09:34:37'),
(4, 4, 'company_display_name', 'George ETEF', '2026-05-01 09:34:37'),
(5, 5, 'company_display_name', 'Abegaile Ademe', '2026-05-01 09:34:37'),
(6, 6, 'company_display_name', 'WubNet', '2026-05-01 09:41:08'),
(8, 1, 'idle_threshold_minutes', '10', '2026-05-01 09:34:37'),
(9, 2, 'idle_threshold_minutes', '10', '2026-05-01 09:34:37'),
(10, 3, 'idle_threshold_minutes', '10', '2026-05-01 09:34:37'),
(11, 4, 'idle_threshold_minutes', '10', '2026-05-01 09:34:37'),
(12, 5, 'idle_threshold_minutes', '10', '2026-05-01 09:34:37'),
(13, 6, 'idle_threshold_minutes', '10', '2026-05-01 09:41:08'),
(15, 1, 'stale_threshold_minutes', '30', '2026-05-01 09:34:37'),
(16, 2, 'stale_threshold_minutes', '30', '2026-05-01 09:34:37'),
(17, 3, 'stale_threshold_minutes', '30', '2026-05-01 09:34:37'),
(18, 4, 'stale_threshold_minutes', '30', '2026-05-01 09:34:37'),
(19, 5, 'stale_threshold_minutes', '30', '2026-05-01 09:34:37'),
(20, 6, 'stale_threshold_minutes', '30', '2026-05-01 09:41:08'),
(22, 1, 'default_currency', 'CAD', '2026-05-01 09:34:37'),
(23, 2, 'default_currency', 'CAD', '2026-05-01 09:34:37'),
(24, 3, 'default_currency', 'CAD', '2026-05-01 09:34:37'),
(25, 4, 'default_currency', 'CAD', '2026-05-01 09:34:37'),
(26, 5, 'default_currency', 'CAD', '2026-05-01 09:34:37'),
(27, 6, 'default_currency', 'CAD', '2026-05-01 09:41:08'),
(29, 1, 'invoice_due_days', '30', '2026-05-01 09:34:37'),
(30, 2, 'invoice_due_days', '30', '2026-05-01 09:34:37'),
(31, 3, 'invoice_due_days', '30', '2026-05-01 09:34:37'),
(32, 4, 'invoice_due_days', '30', '2026-05-01 09:34:37'),
(33, 5, 'invoice_due_days', '30', '2026-05-01 09:34:37'),
(34, 6, 'invoice_due_days', '30', '2026-05-01 09:41:08'),
(36, 1, 'invoice_tax_rate', '13', '2026-05-01 09:34:37'),
(37, 2, 'invoice_tax_rate', '13', '2026-05-01 09:34:37'),
(38, 3, 'invoice_tax_rate', '13', '2026-05-01 09:34:37'),
(39, 4, 'invoice_tax_rate', '13', '2026-05-01 09:34:37'),
(40, 5, 'invoice_tax_rate', '13', '2026-05-01 09:34:37'),
(41, 6, 'invoice_tax_rate', '13', '2026-05-01 09:41:08'),
(43, 1, 'invoice_footer_note', 'Thank you for your business.', '2026-05-01 09:34:37'),
(44, 2, 'invoice_footer_note', 'Thank you for your business.', '2026-05-01 09:34:37'),
(45, 3, 'invoice_footer_note', 'Thank you for your business.', '2026-05-01 09:34:37'),
(46, 4, 'invoice_footer_note', 'Thank you for your business.', '2026-05-01 09:34:37'),
(47, 5, 'invoice_footer_note', 'Thank you for your business.', '2026-05-01 09:34:37'),
(48, 6, 'invoice_footer_note', 'Thank you for your business.', '2026-05-01 09:41:08'),
(50, 1, 'screenshots_default_on', '1', '2026-05-01 09:34:37'),
(51, 2, 'screenshots_default_on', '1', '2026-05-01 09:34:37'),
(52, 3, 'screenshots_default_on', '1', '2026-05-01 09:34:37'),
(53, 4, 'screenshots_default_on', '1', '2026-05-01 09:34:37'),
(54, 5, 'screenshots_default_on', '1', '2026-05-01 09:34:37'),
(55, 6, 'screenshots_default_on', '1', '2026-05-01 09:41:08'),
(57, 1, 'presence_active_window', '180', '2026-05-01 09:34:37'),
(58, 2, 'presence_active_window', '180', '2026-05-01 09:34:37'),
(59, 3, 'presence_active_window', '180', '2026-05-01 09:34:37'),
(60, 4, 'presence_active_window', '180', '2026-05-01 09:34:37'),
(61, 5, 'presence_active_window', '180', '2026-05-01 09:34:37'),
(62, 6, 'presence_active_window', '180', '2026-05-01 09:41:08');

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
(1, 2, 4, 9, 'INV-202603-0004', '2026-03-27', '2026-04-26', 13.00, 11642.73, 1513.55, 13156.28, 'please see last month fee', 'overdue', 4, '2026-03-27 14:58:42', '2026-05-01 15:41:57', 'classic', '2026-04-11 10:56:48', 'gizieart@gmail.com', '2026-04-11 10:57:59', NULL, NULL, NULL, 'PayPal', NULL, 'please send me email when you pay', 'we are developed the frontend , i how you can see the scalability and the stability of the app'),
(6, 2, 4, 9, 'INV-202603-0004-R2', '2026-03-27', '2026-04-26', 13.00, 11642.73, 1513.55, 13156.28, NULL, 'draft', 4, '2026-03-27 15:40:50', '2026-05-01 15:41:19', 'classic', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'please do not change the images, only the format will be enough'),
(7, NULL, 5, 12, 'INV-202604-0005', '2026-04-10', '2026-05-10', 5.00, 9.57, 0.48, 10.05, 'please confirm this invoice', 'draft', 8, '2026-04-10 15:37:25', '2026-04-10 15:37:25', 'corporate', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, NULL, 5, 12, 'INV-202604-0005-R2', '2026-04-10', '2026-05-10', 5.00, 9.57, 0.48, 10.05, 'confirm please you have received this.', 'draft', 8, '2026-04-10 15:48:28', '2026-04-10 15:48:28', 'corporate', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(60) NOT NULL,
  `message` varchar(500) NOT NULL,
  `link` varchar(300) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `screenshot_min_interval` tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Min minutes between screenshots (1–120)',
  `screenshot_max_interval` tinyint(3) UNSIGNED NOT NULL DEFAULT 15 COMMENT 'Max minutes between screenshots (1–120, >= min)',
  `tax_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Default tax percentage for invoices on this project'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `company_id`, `project_name`, `description`, `client_id`, `created_by`, `hourly_rate`, `budget`, `deadline`, `status`, `stage`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`, `deletion_requested`, `deletion_requested_by`, `deletion_requested_at`, `deletion_reason`, `progress_percentage`, `budget_alert_75`, `budget_alert_90`, `budget_alert_100`, `screenshots_enabled`, `screenshot_min_interval`, `screenshot_max_interval`, `tax_rate`) VALUES
(1, 1, 'Website Redesign', NULL, 3, NULL, 50.00, NULL, NULL, 'active', 'planning', '2026-02-20 15:07:00', '2026-04-10 14:04:10', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 1, 5, 15, 0.00),
(2, 1, 'SEO Audit', NULL, 3, NULL, 75.00, NULL, NULL, 'active', 'planning', '2026-02-20 15:07:00', '2026-04-10 14:04:10', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 1, 5, 15, 0.00),
(4, 1, 'Melaku Digital Inc.', 'Melaku digital inc need website design. we must show luxury colors and font styles. and the landing page need to have animations.', 9, 9, 35.00, 20000.00, '2026-03-20', 'active', 'planning', '2026-02-20 15:47:42', '2026-04-10 14:04:10', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 1, 5, 15, 0.00),
(5, 3, 'WEbsite design', 'we will redesign the novelnet website ', 12, 8, 24.00, 5000.00, '2026-04-30', 'active', 'planning', '2026-04-10 15:09:34', '2026-04-10 15:09:34', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 1, 5, 15, 0.00),
(6, 6, 'ResuMatch', 'testing and upgrading the platform', 13, 14, 22.00, 5000.00, '2026-07-01', 'active', 'planning', '2026-05-01 13:39:01', '2026-05-01 13:39:01', NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 0, 1, 5, 15, 0.00);

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

--
-- Dumping data for table `screenshots`
--

INSERT INTO `screenshots` (`id`, `entry_id`, `user_id`, `project_id`, `company_id`, `file_path`, `file_size_kb`, `activity_score_at_capture`, `captured_at`) VALUES
(1, 11, 11, 4, 1, 'uploads/screenshots/1/11/11/20260417174645_953.jpg', 31, 68, '2026-04-17 11:46:45'),
(2, 11, 11, 4, 1, 'uploads/screenshots/1/11/11/20260417174657_366.jpg', 31, 68, '2026-04-17 11:46:57'),
(3, 11, 11, 4, 1, 'uploads/screenshots/1/11/11/20260417174709_185.jpg', 31, 68, '2026-04-17 11:47:09'),
(4, 11, 11, 4, 1, 'uploads/screenshots/1/11/11/20260417174721_174.jpg', 31, 68, '2026-04-17 11:47:21'),
(5, 11, 11, 4, 1, 'uploads/screenshots/1/11/11/20260417174733_226.jpg', 31, 0, '2026-04-17 11:47:33'),
(6, 11, 11, 4, 1, 'uploads/screenshots/1/11/11/20260417174745_327.jpg', 31, 0, '2026-04-17 11:47:45'),
(7, 11, 11, 4, 1, 'uploads/screenshots/1/11/11/20260417174757_303.jpg', 31, 0, '2026-04-17 11:47:57'),
(8, 12, 11, 4, 1, 'uploads/screenshots/1/11/12/20260417174832_212.jpg', 35, 40, '2026-04-17 11:48:32'),
(9, 12, 11, 4, 1, 'uploads/screenshots/1/11/12/20260417174842_278.jpg', 35, 45, '2026-04-17 11:48:42');

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
(131, 7, 11, '2026-04-17 10:09:09', 0, 0, 0),
(132, 7, 11, '2026-04-17 10:10:09', 0, 0, 0),
(133, 7, 11, '2026-04-17 10:11:09', 0, 0, 0),
(134, 7, 11, '2026-04-17 10:12:09', 0, 0, 0),
(135, 7, 11, '2026-04-17 10:13:09', 0, 0, 0),
(136, 7, 11, '2026-04-17 10:14:09', 0, 0, 0),
(137, 7, 11, '2026-04-17 10:15:09', 0, 0, 0),
(138, 7, 11, '2026-04-17 10:16:09', 0, 0, 0),
(139, 7, 11, '2026-04-17 10:17:09', 0, 0, 0),
(140, 7, 11, '2026-04-17 10:18:09', 0, 0, 0),
(141, 7, 11, '2026-04-17 10:19:09', 0, 0, 0),
(142, 7, 11, '2026-04-17 10:20:09', 0, 0, 0),
(143, 7, 11, '2026-04-17 10:21:09', 0, 0, 0),
(144, 7, 11, '2026-04-17 10:22:09', 0, 0, 0),
(145, 7, 11, '2026-04-17 10:23:09', 0, 0, 0),
(146, 7, 11, '2026-04-17 10:24:09', 0, 0, 0),
(147, 7, 11, '2026-04-17 10:25:09', 0, 0, 0),
(148, 7, 11, '2026-04-17 10:26:09', 0, 0, 0),
(149, 7, 11, '2026-04-17 10:27:09', 0, 0, 0),
(150, 7, 11, '2026-04-17 10:28:09', 0, 0, 0),
(151, 7, 11, '2026-04-17 10:29:09', 0, 0, 0),
(152, 7, 11, '2026-04-17 10:30:09', 0, 0, 0),
(153, 7, 11, '2026-04-17 10:31:09', 0, 0, 0),
(154, 7, 11, '2026-04-17 10:32:09', 0, 0, 0),
(155, 7, 11, '2026-04-17 10:33:09', 0, 0, 0),
(156, 7, 11, '2026-04-17 10:34:09', 0, 0, 0),
(157, 7, 11, '2026-04-17 10:35:09', 0, 0, 0),
(158, 7, 11, '2026-04-17 10:36:09', 0, 0, 0),
(159, 7, 11, '2026-04-17 10:37:09', 0, 0, 0),
(160, 7, 11, '2026-04-17 10:38:09', 0, 0, 0),
(161, 7, 11, '2026-04-17 10:39:09', 0, 0, 0),
(162, 7, 11, '2026-04-17 10:40:09', 0, 0, 0),
(163, 7, 11, '2026-04-17 10:41:09', 0, 0, 0),
(164, 7, 11, '2026-04-17 10:41:39', 0, 0, 0),
(165, 8, 11, '2026-04-17 10:43:25', 755, 0, 755),
(166, 9, 8, '2026-04-17 10:44:21', 222, 0, 222),
(167, 8, 11, '2026-04-17 10:44:25', 0, 0, 0),
(168, 9, 8, '2026-04-17 10:45:21', 0, 0, 0),
(169, 8, 11, '2026-04-17 10:45:25', 0, 0, 0),
(170, 9, 8, '2026-04-17 10:46:21', 0, 0, 0),
(171, 8, 11, '2026-04-17 10:46:25', 0, 0, 0),
(172, 9, 8, '2026-04-17 10:47:22', 40, 0, 40),
(173, 8, 11, '2026-04-17 10:47:24', 413, 0, 413),
(174, 9, 8, '2026-04-17 10:48:22', 0, 0, 0),
(175, 8, 11, '2026-04-17 10:48:24', 34, 0, 34),
(176, 9, 8, '2026-04-17 10:49:22', 0, 0, 0),
(177, 8, 11, '2026-04-17 10:49:24', 0, 0, 0),
(178, 9, 8, '2026-04-17 10:50:22', 0, 0, 0),
(179, 8, 11, '2026-04-17 10:50:59', 132, 0, 132),
(180, 9, 8, '2026-04-17 10:51:22', 0, 0, 0),
(181, 8, 11, '2026-04-17 10:51:59', 0, 0, 0),
(182, 9, 8, '2026-04-17 10:52:22', 0, 0, 0),
(183, 8, 11, '2026-04-17 10:52:59', 41, 0, 41),
(184, 9, 8, '2026-04-17 10:53:22', 0, 0, 0),
(185, 8, 11, '2026-04-17 10:53:59', 0, 0, 0),
(186, 9, 8, '2026-04-17 10:54:22', 0, 0, 0),
(187, 8, 11, '2026-04-17 10:54:59', 0, 0, 0),
(188, 9, 8, '2026-04-17 10:55:22', 0, 0, 0),
(189, 8, 11, '2026-04-17 10:55:59', 0, 0, 0),
(190, 9, 8, '2026-04-17 10:56:22', 0, 0, 0),
(191, 8, 11, '2026-04-17 10:56:59', 0, 0, 0),
(192, 8, 11, '2026-04-17 10:57:59', 0, 0, 0),
(193, 9, 8, '2026-04-17 10:58:09', 0, 0, 0),
(194, 8, 11, '2026-04-17 10:58:59', 0, 0, 0),
(195, 9, 8, '2026-04-17 10:59:09', 0, 0, 0),
(196, 8, 11, '2026-04-17 10:59:59', 0, 0, 0),
(197, 9, 8, '2026-04-17 11:00:09', 0, 0, 0),
(198, 8, 11, '2026-04-17 11:00:59', 0, 0, 0),
(199, 9, 8, '2026-04-17 11:01:09', 0, 0, 0),
(200, 8, 11, '2026-04-17 11:01:59', 0, 0, 0),
(201, 9, 8, '2026-04-17 11:02:09', 0, 0, 0),
(202, 9, 8, '2026-04-17 11:03:09', 0, 0, 0),
(203, 8, 11, '2026-04-17 11:03:09', 0, 0, 0),
(204, 9, 8, '2026-04-17 11:04:09', 0, 0, 0),
(205, 8, 11, '2026-04-17 11:04:09', 0, 0, 0),
(206, 9, 8, '2026-04-17 11:05:09', 0, 0, 0),
(207, 8, 11, '2026-04-17 11:05:09', 0, 0, 0),
(208, 9, 8, '2026-04-17 11:06:09', 0, 0, 0),
(209, 8, 11, '2026-04-17 11:06:09', 0, 0, 0),
(210, 8, 11, '2026-04-17 11:07:09', 0, 0, 0),
(211, 9, 8, '2026-04-17 11:07:09', 0, 0, 0),
(212, 8, 11, '2026-04-17 11:08:09', 0, 0, 0),
(213, 9, 8, '2026-04-17 11:08:09', 0, 0, 0),
(214, 9, 8, '2026-04-17 11:09:09', 0, 0, 0),
(215, 8, 11, '2026-04-17 11:09:09', 0, 0, 0),
(216, 9, 8, '2026-04-17 11:10:09', 0, 0, 0),
(217, 8, 11, '2026-04-17 11:10:09', 0, 0, 0),
(218, 8, 11, '2026-04-17 11:11:09', 0, 0, 0),
(219, 9, 8, '2026-04-17 11:11:09', 0, 0, 0),
(220, 9, 8, '2026-04-17 11:12:09', 0, 0, 0),
(221, 8, 11, '2026-04-17 11:12:09', 0, 0, 0),
(222, 8, 11, '2026-04-17 11:13:09', 0, 0, 0),
(223, 9, 8, '2026-04-17 11:13:09', 0, 0, 0),
(224, 9, 8, '2026-04-17 11:14:09', 0, 0, 0),
(225, 8, 11, '2026-04-17 11:14:09', 0, 0, 0),
(226, 8, 11, '2026-04-17 11:15:09', 0, 0, 0),
(227, 9, 8, '2026-04-17 11:15:09', 0, 0, 0),
(228, 8, 11, '2026-04-17 11:16:09', 0, 0, 0),
(229, 9, 8, '2026-04-17 11:16:09', 0, 0, 0),
(230, 9, 8, '2026-04-17 11:17:09', 0, 0, 0),
(231, 8, 11, '2026-04-17 11:17:09', 0, 0, 0),
(232, 8, 11, '2026-04-17 11:18:09', 0, 0, 0),
(233, 9, 8, '2026-04-17 11:18:09', 0, 0, 0),
(234, 8, 11, '2026-04-17 11:19:09', 0, 0, 0),
(235, 9, 8, '2026-04-17 11:19:09', 0, 0, 0),
(236, 8, 11, '2026-04-17 11:20:09', 0, 0, 0),
(237, 9, 8, '2026-04-17 11:20:09', 0, 0, 0),
(238, 9, 8, '2026-04-17 11:21:09', 0, 0, 0),
(239, 8, 11, '2026-04-17 11:21:09', 0, 0, 0),
(240, 8, 11, '2026-04-17 11:22:09', 0, 0, 0),
(241, 9, 8, '2026-04-17 11:22:09', 0, 0, 0),
(242, 9, 8, '2026-04-17 11:23:09', 0, 0, 0),
(243, 8, 11, '2026-04-17 11:23:09', 0, 0, 0),
(244, 9, 8, '2026-04-17 11:24:09', 0, 0, 0),
(245, 8, 11, '2026-04-17 11:24:09', 0, 0, 0),
(246, 8, 11, '2026-04-17 11:25:09', 0, 0, 0),
(247, 9, 8, '2026-04-17 11:25:09', 0, 0, 0),
(248, 9, 8, '2026-04-17 11:26:09', 0, 0, 0),
(249, 8, 11, '2026-04-17 11:26:09', 0, 0, 0),
(250, 9, 8, '2026-04-17 11:27:09', 0, 0, 0),
(251, 8, 11, '2026-04-17 11:27:09', 0, 0, 0),
(252, 8, 11, '2026-04-17 11:28:09', 0, 0, 0),
(253, 9, 8, '2026-04-17 11:28:09', 0, 0, 0),
(254, 8, 11, '2026-04-17 11:29:09', 0, 0, 0),
(255, 9, 8, '2026-04-17 11:29:09', 0, 0, 0),
(256, 8, 11, '2026-04-17 11:30:09', 0, 0, 0),
(257, 9, 8, '2026-04-17 11:30:09', 0, 0, 0),
(258, 9, 8, '2026-04-17 11:31:09', 0, 0, 0),
(259, 9, 8, '2026-04-17 11:32:09', 0, 0, 0),
(260, 9, 8, '2026-04-17 11:33:09', 0, 0, 0),
(261, 9, 8, '2026-04-17 11:33:50', 0, 0, 0),
(262, 10, 11, '2026-04-17 11:33:54', 576, 0, 576),
(263, 10, 11, '2026-04-17 11:34:54', 0, 0, 0),
(264, 10, 11, '2026-04-17 11:35:54', 0, 0, 0),
(265, 10, 11, '2026-04-17 11:36:54', 0, 0, 0),
(266, 10, 11, '2026-04-17 11:38:14', 1409, 0, 1409),
(267, 10, 11, '2026-04-17 11:39:14', 0, 0, 0),
(268, 10, 11, '2026-04-17 11:40:14', 0, 0, 0),
(269, 10, 11, '2026-04-17 11:41:14', 0, 0, 0),
(270, 10, 11, '2026-04-17 11:42:14', 0, 0, 0),
(271, 10, 11, '2026-04-17 11:43:14', 0, 0, 0),
(272, 10, 11, '2026-04-17 11:44:14', 0, 0, 0),
(273, 11, 11, '2026-04-17 11:46:22', 125, 2, 127),
(274, 11, 11, '2026-04-17 11:47:23', 68, 0, 68),
(275, 14, 8, '2026-05-01 08:45:17', 164, 0, 164),
(276, 14, 8, '2026-05-01 08:46:17', 136, 0, 136),
(277, 15, 11, '2026-05-01 08:47:16', 58, 0, 58),
(278, 14, 8, '2026-05-01 08:48:04', 1236, 4, 1240),
(279, 15, 11, '2026-05-01 08:48:16', 16, 0, 16),
(280, 14, 8, '2026-05-01 08:49:04', 0, 0, 0),
(281, 15, 11, '2026-05-01 08:49:16', 0, 0, 0),
(282, 14, 8, '2026-05-01 08:50:04', 0, 0, 0),
(283, 15, 11, '2026-05-01 08:50:16', 0, 0, 0),
(284, 14, 8, '2026-05-01 08:51:04', 0, 0, 0),
(285, 15, 11, '2026-05-01 08:51:16', 91, 0, 91),
(286, 14, 8, '2026-05-01 08:52:04', 0, 0, 0),
(287, 15, 11, '2026-05-01 08:52:16', 0, 0, 0),
(288, 15, 11, '2026-05-01 08:53:16', 0, 0, 0),
(289, 15, 11, '2026-05-01 08:54:16', 184, 0, 184),
(290, 15, 11, '2026-05-01 08:55:16', 21, 0, 21),
(291, 15, 11, '2026-05-01 08:56:16', 0, 0, 0),
(292, 15, 11, '2026-05-01 08:57:16', 0, 0, 0),
(293, 15, 11, '2026-05-01 08:58:16', 0, 0, 0),
(294, 15, 11, '2026-05-01 08:59:16', 16, 0, 16),
(295, 15, 11, '2026-05-01 09:00:16', 0, 0, 0),
(296, 15, 11, '2026-05-01 09:01:16', 0, 0, 0),
(297, 15, 11, '2026-05-01 09:02:16', 0, 0, 0),
(298, 15, 11, '2026-05-01 09:03:16', 0, 0, 0),
(299, 15, 11, '2026-05-01 09:04:16', 0, 0, 0),
(300, 15, 11, '2026-05-01 09:05:16', 0, 0, 0),
(301, 15, 11, '2026-05-01 09:06:16', 78, 0, 78),
(302, 14, 8, '2026-05-01 09:06:59', 24, 0, 24),
(303, 15, 11, '2026-05-01 09:07:16', 2, 0, 2),
(304, 14, 8, '2026-05-01 09:07:59', 0, 0, 0),
(305, 15, 11, '2026-05-01 09:08:16', 0, 0, 0),
(306, 14, 8, '2026-05-01 09:08:59', 0, 0, 0),
(307, 15, 11, '2026-05-01 09:09:16', 0, 0, 0),
(308, 14, 8, '2026-05-01 09:09:59', 2, 0, 2),
(309, 15, 11, '2026-05-01 09:10:16', 0, 0, 0),
(310, 14, 8, '2026-05-01 09:10:59', 0, 0, 0),
(311, 15, 11, '2026-05-01 09:11:16', 0, 0, 0),
(312, 14, 8, '2026-05-01 09:11:59', 0, 0, 0),
(313, 15, 11, '2026-05-01 09:12:16', 0, 0, 0),
(314, 14, 8, '2026-05-01 09:12:59', 0, 0, 0),
(315, 15, 11, '2026-05-01 09:13:16', 0, 0, 0),
(316, 14, 8, '2026-05-01 09:14:00', 44, 0, 44),
(317, 15, 11, '2026-05-01 09:14:16', 4, 0, 4),
(318, 15, 11, '2026-05-01 09:15:16', 65, 0, 65),
(319, 14, 8, '2026-05-01 09:15:27', 0, 0, 0),
(320, 14, 8, '2026-05-01 09:16:00', 0, 0, 0),
(321, 15, 11, '2026-05-01 09:16:16', 0, 0, 0),
(322, 14, 8, '2026-05-01 09:17:00', 0, 0, 0),
(323, 15, 11, '2026-05-01 09:17:16', 0, 0, 0),
(324, 14, 8, '2026-05-01 09:18:00', 0, 0, 0),
(325, 15, 11, '2026-05-01 09:18:16', 0, 0, 0),
(326, 14, 8, '2026-05-01 09:19:00', 0, 0, 0),
(327, 14, 8, '2026-05-01 09:20:00', 0, 0, 0),
(328, 14, 8, '2026-05-01 09:21:00', 0, 0, 0),
(329, 14, 8, '2026-05-01 09:22:00', 0, 0, 0),
(330, 14, 8, '2026-05-01 09:23:00', 0, 0, 0),
(331, 14, 8, '2026-05-01 09:24:27', 0, 0, 0),
(332, 14, 8, '2026-05-01 09:25:27', 0, 0, 0),
(333, 14, 8, '2026-05-01 09:26:27', 0, 0, 0),
(334, 14, 8, '2026-05-01 09:27:27', 0, 0, 0),
(335, 14, 8, '2026-05-01 09:28:27', 0, 0, 0),
(336, 14, 8, '2026-05-01 09:29:27', 0, 0, 0),
(337, 14, 8, '2026-05-01 09:30:27', 0, 0, 0),
(338, 14, 8, '2026-05-01 09:31:27', 0, 0, 0),
(339, 14, 8, '2026-05-01 09:32:27', 0, 0, 0),
(340, 14, 8, '2026-05-01 09:32:59', 166, 0, 166),
(341, 14, 8, '2026-05-01 09:34:06', 185, 2, 187),
(342, 14, 8, '2026-05-01 09:35:06', 0, 0, 0),
(343, 14, 8, '2026-05-01 09:36:06', 0, 0, 0),
(344, 14, 8, '2026-05-01 09:37:06', 0, 0, 0),
(345, 14, 8, '2026-05-01 09:38:06', 0, 0, 0),
(346, 14, 8, '2026-05-01 09:39:06', 0, 0, 0),
(347, 14, 8, '2026-05-01 09:54:28', 598, 4, 602),
(348, 14, 8, '2026-05-01 09:55:28', 0, 0, 0),
(349, 14, 8, '2026-05-01 09:56:28', 0, 0, 0),
(350, 14, 8, '2026-05-01 09:57:28', 0, 0, 0),
(351, 14, 8, '2026-05-01 09:58:28', 0, 0, 0),
(352, 16, 14, '2026-05-01 09:58:36', 957, 14, 971),
(353, 14, 8, '2026-05-01 09:59:28', 0, 0, 0),
(354, 14, 8, '2026-05-01 10:02:41', 79, 2, 81),
(355, 17, 14, '2026-05-01 10:04:04', 128, 0, 128),
(356, 14, 8, '2026-05-01 10:04:26', 1368, 2, 1370),
(357, 17, 14, '2026-05-01 10:05:04', 10, 0, 10),
(358, 14, 8, '2026-05-01 10:05:26', 0, 0, 0),
(359, 17, 14, '2026-05-01 10:06:19', 1064, 0, 1064),
(360, 14, 8, '2026-05-01 10:06:26', 0, 0, 0),
(361, 17, 14, '2026-05-01 10:07:19', 145, 0, 145),
(362, 14, 8, '2026-05-01 10:07:26', 0, 0, 0),
(363, 17, 14, '2026-05-01 10:08:19', 0, 0, 0),
(364, 14, 8, '2026-05-01 10:08:26', 0, 0, 0),
(365, 17, 14, '2026-05-01 10:09:19', 0, 0, 0),
(366, 14, 8, '2026-05-01 10:09:26', 0, 0, 0),
(367, 17, 14, '2026-05-01 10:10:19', 63, 0, 63),
(368, 17, 14, '2026-05-01 10:11:19', 32, 0, 32),
(369, 14, 8, '2026-05-01 10:11:30', 74, 2, 76),
(370, 17, 14, '2026-05-01 10:12:19', 0, 0, 0),
(371, 14, 8, '2026-05-01 10:12:30', 0, 0, 0),
(372, 17, 14, '2026-05-01 10:13:19', 0, 0, 0),
(373, 14, 8, '2026-05-01 10:13:30', 0, 0, 0),
(374, 17, 14, '2026-05-01 10:14:19', 0, 0, 0),
(375, 14, 8, '2026-05-01 10:14:30', 0, 0, 0),
(376, 17, 14, '2026-05-01 10:15:19', 0, 0, 0),
(377, 14, 8, '2026-05-01 10:15:30', 0, 0, 0),
(378, 17, 14, '2026-05-01 10:16:19', 0, 0, 0),
(379, 14, 8, '2026-05-01 10:16:30', 0, 0, 0),
(380, 17, 14, '2026-05-01 10:17:19', 0, 0, 0),
(381, 14, 8, '2026-05-01 10:17:30', 0, 0, 0),
(382, 17, 14, '2026-05-01 10:18:19', 0, 0, 0),
(383, 14, 8, '2026-05-01 10:18:30', 0, 0, 0),
(384, 14, 8, '2026-05-01 10:19:30', 0, 0, 0),
(385, 14, 8, '2026-05-01 10:20:30', 0, 0, 0),
(386, 17, 14, '2026-05-01 10:54:00', 1026, 0, 1026),
(387, 17, 14, '2026-05-01 10:55:17', 110, 0, 110),
(388, 14, 8, '2026-05-01 10:56:06', 2055, 0, 2055),
(389, 17, 14, '2026-05-01 10:56:17', 0, 0, 0),
(390, 17, 14, '2026-05-01 10:57:17', 5, 0, 5),
(391, 17, 14, '2026-05-01 10:58:17', 17, 0, 17),
(392, 14, 8, '2026-05-01 10:58:37', 118, 0, 118),
(393, 14, 8, '2026-05-01 10:59:37', 2, 0, 2),
(394, 14, 8, '2026-05-01 11:00:37', 0, 0, 0),
(395, 14, 8, '2026-05-01 11:01:37', 0, 0, 0),
(396, 18, 14, '2026-05-01 11:02:19', 1205, 0, 1205),
(397, 14, 8, '2026-05-01 11:02:37', 0, 0, 0),
(398, 18, 14, '2026-05-01 11:03:19', 273, 0, 273),
(399, 14, 8, '2026-05-01 11:03:37', 0, 0, 0),
(400, 18, 14, '2026-05-01 11:05:10', 78, 0, 78),
(401, 14, 8, '2026-05-01 11:05:16', 264, 2, 266),
(402, 18, 14, '2026-05-01 11:06:10', 0, 0, 0),
(403, 18, 14, '2026-05-01 11:07:10', 40, 0, 40),
(404, 14, 8, '2026-05-01 11:07:25', 1534, 47, 1581),
(405, 18, 14, '2026-05-01 11:08:10', 0, 0, 0),
(406, 18, 14, '2026-05-01 11:09:10', 2, 0, 2),
(407, 14, 8, '2026-05-01 11:09:21', 253, 2, 255),
(408, 18, 14, '2026-05-01 11:10:10', 544, 0, 544),
(409, 14, 8, '2026-05-01 11:10:21', 0, 0, 0),
(410, 14, 8, '2026-05-01 11:11:21', 0, 0, 0),
(411, 14, 8, '2026-05-01 11:12:21', 0, 0, 0),
(412, 14, 8, '2026-05-01 11:13:21', 0, 0, 0),
(413, 14, 8, '2026-05-01 11:15:54', 519, 0, 519),
(414, 14, 8, '2026-05-01 11:16:54', 0, 0, 0),
(415, 14, 8, '2026-05-01 11:17:54', 0, 0, 0),
(416, 14, 8, '2026-05-01 11:18:54', 0, 0, 0),
(417, 19, 11, '2026-05-01 11:19:08', 838, 0, 838),
(418, 14, 8, '2026-05-01 11:19:54', 0, 0, 0),
(419, 19, 11, '2026-05-01 11:20:08', 91, 0, 91),
(420, 14, 8, '2026-05-01 11:20:54', 0, 0, 0),
(421, 19, 11, '2026-05-01 11:21:34', 656, 0, 656),
(422, 14, 8, '2026-05-01 11:21:54', 0, 0, 0),
(423, 19, 11, '2026-05-01 11:22:34', 0, 0, 0),
(424, 14, 8, '2026-05-01 11:22:54', 0, 0, 0),
(425, 19, 11, '2026-05-01 11:23:34', 0, 0, 0),
(426, 14, 8, '2026-05-01 11:23:54', 0, 0, 0),
(427, 19, 11, '2026-05-01 11:24:34', 0, 0, 0),
(428, 14, 8, '2026-05-01 11:24:54', 0, 0, 0),
(429, 19, 11, '2026-05-01 11:25:34', 0, 1, 1),
(430, 14, 8, '2026-05-01 11:25:54', 0, 0, 0),
(431, 14, 8, '2026-05-01 11:26:54', 0, 0, 0),
(432, 14, 8, '2026-05-01 11:27:54', 0, 0, 0),
(433, 14, 8, '2026-05-01 11:28:54', 0, 0, 0),
(434, 14, 8, '2026-05-01 11:29:54', 0, 0, 0),
(435, 14, 8, '2026-05-01 11:30:54', 0, 0, 0),
(436, 14, 8, '2026-05-01 11:31:54', 0, 0, 0),
(437, 14, 8, '2026-05-01 11:32:54', 0, 0, 0),
(438, 14, 8, '2026-05-01 11:33:54', 0, 0, 0),
(439, 14, 8, '2026-05-01 11:34:54', 2, 0, 2),
(440, 14, 8, '2026-05-01 11:35:54', 0, 0, 0),
(441, 14, 8, '2026-05-01 11:36:54', 2, 0, 2),
(442, 14, 8, '2026-05-01 11:37:54', 0, 0, 0),
(443, 14, 8, '2026-05-01 11:38:54', 0, 0, 0),
(444, 14, 8, '2026-05-01 11:39:54', 2, 0, 2),
(445, 14, 8, '2026-05-01 11:40:54', 0, 0, 0),
(446, 14, 8, '2026-05-01 11:41:54', 2, 0, 2),
(447, 14, 8, '2026-05-01 11:42:54', 0, 0, 0),
(448, 14, 8, '2026-05-01 11:43:54', 0, 0, 0),
(449, 14, 8, '2026-05-01 11:44:54', 0, 0, 0),
(450, 14, 8, '2026-05-01 11:45:54', 4, 0, 4),
(451, 14, 8, '2026-05-01 11:46:54', 3, 0, 3),
(452, 14, 8, '2026-05-01 11:47:54', 0, 0, 0),
(453, 14, 8, '2026-05-01 11:48:54', 0, 0, 0),
(454, 14, 8, '2026-05-01 11:49:54', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('open','in_progress','done') NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `estimated_hours` decimal(6,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `project_id`, `company_id`, `assigned_to`, `title`, `description`, `status`, `priority`, `estimated_hours`, `due_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 'Design homepage wireframes', 'Create low-fi wireframes for the new homepage layout', 'done', 'high', 8.00, '2026-04-20', 1, '2026-05-01 09:46:06', '2026-05-01 09:46:06'),
(2, 1, 1, 2, 'Build responsive navigation', 'Implement sticky nav with mobile hamburger menu', 'done', 'high', 5.00, '2026-04-25', 1, '2026-05-01 09:46:06', '2026-05-01 09:46:06'),
(3, 1, 1, 11, 'Hero section animation', 'CSS/JS animation for the hero section scroll effect', 'in_progress', 'medium', 4.00, '2026-05-05', 1, '2026-05-01 09:46:06', '2026-05-01 09:46:06'),
(4, 1, 1, 11, 'Contact form backend', 'PHP form handler with email validation and spam check', 'in_progress', 'high', 6.00, '2026-05-08', 1, '2026-05-01 09:46:06', '2026-05-01 09:46:06'),
(5, 1, 1, 5, 'SEO meta tags pass', 'Add proper og:tags, title, description to all pages', 'open', 'medium', 3.00, '2026-05-15', 1, '2026-05-01 09:46:06', '2026-05-01 09:46:06'),
(6, 1, 1, NULL, 'Cross-browser testing', 'Test on Chrome, Firefox, Safari, Edge — fix breakage', 'open', 'low', 4.00, '2026-05-20', 1, '2026-05-01 09:46:06', '2026-05-01 09:46:06'),
(7, 1, 1, 2, 'Accessibility audit', 'Run axe-core audit, fix WCAG AA violations', 'open', 'medium', 5.00, '2026-05-22', 1, '2026-05-01 09:46:06', '2026-05-01 09:46:06'),
(8, 2, 1, 11, 'Crawl site with Screaming Frog', 'Export full crawl, identify 4xx/5xx, duplicate titles', 'done', 'high', 3.00, '2026-04-18', 1, '2026-05-01 09:46:06', '2026-05-01 09:46:06'),
(9, 2, 1, 11, 'Keyword gap analysis', 'Compare client vs top 3 competitors in Ahrefs', 'done', 'high', 6.00, '2026-04-22', 1, '2026-05-01 09:46:06', '2026-05-01 09:46:06'),
(10, 2, 1, 5, 'On-page optimisation report', 'Title tags, H1s, internal linking recommendations', 'in_progress', 'high', 8.00, '2026-05-06', 1, '2026-05-01 09:46:06', '2026-05-01 09:46:06'),
(11, 2, 1, NULL, 'Core Web Vitals remediation', 'Fix LCP, CLS, INP issues identified in PageSpeed', 'in_progress', 'high', 10.00, '2026-05-18', 1, '2026-05-01 09:46:06', '2026-05-01 10:10:30'),
(12, 2, 1, 5, 'Backlink profile report', 'Export from Ahrefs, identify toxic links for disavow', 'open', 'medium', 4.00, '2026-05-25', 1, '2026-05-01 09:46:06', '2026-05-01 09:46:06'),
(13, 6, 6, 14, 'security test', 'please test and fix the security test  results after you use the zap platform. zap test, login security', 'open', 'high', 8.00, '2026-05-04', 14, '2026-05-01 09:59:32', '2026-05-01 11:01:12'),
(14, 4, 1, 11, 'UX heartscene', 'test and fix the performance. make sure to change the assets images from jpg and png to wbhtm format, for lazzy loading to be fast', 'in_progress', 'high', 4.00, '2026-05-03', 8, '2026-05-01 11:08:21', '2026-05-01 11:30:46');

-- --------------------------------------------------------

--
-- Table structure for table `task_comments`
--

DROP TABLE IF EXISTS `task_comments`;
CREATE TABLE `task_comments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('note','problem','solution','feedback') NOT NULL DEFAULT 'note',
  `body` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_comments`
--

INSERT INTO `task_comments` (`id`, `task_id`, `company_id`, `user_id`, `type`, `body`, `created_at`) VALUES
(1, 14, 1, 11, 'note', 'i found the ux are to slow', '2026-05-01 11:31:11'),
(2, 14, 1, 11, 'solution', 'we should change the jpj and png formats to web format images', '2026-05-01 11:31:49'),
(3, 14, 1, 9, 'note', 'how is my ux status', '2026-05-01 11:45:52'),
(4, 14, 1, 8, 'note', 'rose yours project going well we will change only the images format as abe said', '2026-05-01 11:50:58'),
(5, 14, 1, 11, 'note', 'yes only the format', '2026-05-01 11:51:34');

-- --------------------------------------------------------

--
-- Table structure for table `time_entries`
--

DROP TABLE IF EXISTS `time_entries`;
CREATE TABLE `time_entries` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
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

INSERT INTO `time_entries` (`id`, `company_id`, `project_id`, `task_id`, `user_id`, `start_time`, `end_time`, `total_seconds`, `description`, `is_billable`, `is_idle_detected`, `status`, `entry_type`, `reviewed_by`, `reviewed_at`, `rejection_reason`, `created_at`, `updated_at`, `idle_seconds`, `discarded_idle_seconds`, `activity_score_avg`, `close_reason`) VALUES
(1, 1, 1, NULL, NULL, '2025-10-20 09:00:00', '2025-10-20 12:00:00', 10800, 'Initial layout design', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-02-27 14:16:21', '2026-04-10 14:04:10', 0, 0, NULL, 'manual'),
(2, 1, 1, NULL, NULL, '2025-10-21 14:00:00', '2025-10-21 16:30:00', 9000, 'Fixing navigation bar bug', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-02-27 14:16:21', '2026-04-10 14:04:10', 0, 0, NULL, 'manual'),
(3, 1, 4, NULL, 4, '2026-03-06 10:49:46', '2026-03-27 08:24:38', 1805692, 'Melaku Digital Inc.', 1, 0, 'abandoned', 'timer', NULL, NULL, NULL, '2026-03-06 15:49:46', '2026-04-10 14:04:10', 0, 0, NULL, 'abandoned'),
(4, 1, 4, NULL, 2, '2026-03-13 11:45:40', '2026-03-27 08:24:38', 1197538, 'Freelance work', 1, 0, 'approved', 'timer', 4, '2026-03-27 10:55:32', NULL, '2026-03-13 15:45:40', '2026-04-10 14:04:10', 0, 0, NULL, 'abandoned'),
(5, 3, 5, NULL, 8, '2026-04-10 11:10:07', '2026-04-10 11:34:03', 1436, 'Design: start phase 1', 1, 0, 'approved', 'timer', 8, '2026-04-10 11:34:28', NULL, '2026-04-10 15:10:07', '2026-04-10 15:34:28', 0, 0, NULL, 'manual'),
(6, 1, 4, NULL, 11, '2026-04-17 08:35:28', '2026-04-17 08:40:29', 301, 'General work', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-04-17 12:35:28', '2026-04-17 12:40:29', 0, 0, NULL, 'manual'),
(7, 1, 4, NULL, 11, '2026-04-17 08:40:32', '2026-04-17 10:42:21', 7309, 'General work', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-04-17 12:40:32', '2026-04-17 14:42:21', 0, 0, NULL, 'manual'),
(8, 1, 2, NULL, 11, '2026-04-17 10:42:24', '2026-04-17 11:30:24', 2880, 'SEO Audit', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-04-17 14:42:24', '2026-04-17 15:30:24', 0, 0, NULL, 'manual'),
(9, 3, 5, NULL, 8, '2026-04-17 10:43:21', '2026-04-17 11:33:56', 3035, 'Testing: security testing', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-04-17 14:43:21', '2026-04-17 15:33:56', 0, 0, NULL, 'manual'),
(10, 1, 2, NULL, 11, '2026-04-17 11:32:37', '2026-04-17 11:45:19', 762, 'SEO Audit', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-04-17 15:32:37', '2026-04-17 15:45:19', 0, 0, NULL, 'manual'),
(11, 1, 4, NULL, 11, '2026-04-17 11:45:22', '2026-04-17 11:48:08', 166, 'Melaku Digital Inc.', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-04-17 15:45:22', '2026-04-17 15:48:08', 0, 0, NULL, 'manual'),
(12, 1, 4, NULL, 11, '2026-04-17 11:48:22', '2026-04-17 11:48:51', 29, 'Melaku Digital Inc.', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-04-17 15:48:22', '2026-04-17 15:48:51', 0, 0, NULL, 'manual'),
(13, 1, 4, NULL, 11, '2026-05-01 08:19:47', '2026-05-01 08:45:01', 1514, 'Coding: digimarkt enhancement', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-05-01 12:19:47', '2026-05-01 12:45:01', 0, 0, NULL, 'manual'),
(14, 3, 5, NULL, 8, '2026-05-01 08:34:52', '2026-05-01 12:07:05', 12733, 'Coding: working', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-05-01 12:34:52', '2026-05-01 16:07:05', 0, 0, NULL, 'manual'),
(15, 1, 4, NULL, 11, '2026-05-01 08:46:16', '2026-05-01 09:18:16', 1920, 'Coding: digimark upgrading', 1, 0, 'abandoned', 'timer', NULL, NULL, NULL, '2026-05-01 12:46:16', '2026-05-01 13:50:01', 0, 0, 16.7188, 'abandoned'),
(16, 6, 6, NULL, 14, '2026-05-01 09:41:30', '2026-05-01 09:59:40', 1090, 'Testing: security test', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-05-01 13:41:30', '2026-05-01 13:59:40', 0, 0, NULL, 'manual'),
(17, 6, 6, NULL, 14, '2026-05-01 10:03:04', '2026-05-01 10:18:19', 915, 'Testing: security test', 1, 0, 'abandoned', 'timer', NULL, NULL, NULL, '2026-05-01 14:03:04', '2026-05-01 14:50:00', 0, 0, 96.1333, 'abandoned'),
(18, 6, 6, 13, 14, '2026-05-01 11:00:17', '2026-05-01 11:13:45', 808, 'ResuMatch', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-05-01 15:00:17', '2026-05-01 15:13:45', 0, 0, NULL, 'manual'),
(19, 1, 4, 14, 11, '2026-05-01 11:16:54', '2026-05-01 11:25:34', 520, 'Melaku Digital Inc.', 1, 0, 'abandoned', 'timer', NULL, NULL, NULL, '2026-05-01 15:16:54', '2026-05-01 16:00:00', 0, 0, 226.571, 'abandoned'),
(20, 1, 4, 14, 11, '2026-05-01 12:32:11', '2026-05-01 12:32:18', 7, 'images loading', 1, 0, 'completed', 'timer', NULL, NULL, NULL, '2026-05-01 16:32:11', '2026-05-01 16:32:18', 0, 0, NULL, 'manual');

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
(1, 1, 'admin_user', 'admin@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'admin', 'Super Admin', NULL, NULL, NULL, 1, '2026-03-13 11:23:25', '2026-03-13 11:23:25', NULL, NULL, NULL, '2026-01-30 13:57:53', '2026-05-01 13:58:43'),
(2, 1, 'dev_sarah', 'sarah@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'freelancer', 'Sarah Developer', NULL, NULL, NULL, 1, '2026-03-13 11:38:14', '2026-03-13 12:23:51', 4, NULL, NULL, '2026-01-30 13:57:53', '2026-04-10 14:04:10'),
(3, 1, 'client_bob', 'bob@timeforge.local', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'client', 'Bob The Client', NULL, NULL, NULL, 1, '2026-03-13 11:19:05', '2026-03-13 11:19:05', NULL, NULL, NULL, '2026-01-30 13:57:53', '2026-05-01 13:58:43'),
(4, 2, 'admin', 'admin@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'admin', 'Administrator', 'Melaku Digital Inc.', NULL, 'images/logos/4_logo.png', 1, '2026-05-01 08:14:52', '2026-05-01 08:14:52', 4, NULL, NULL, '2026-02-06 14:47:00', '2026-05-01 13:58:43'),
(5, 1, 'freelancer1', 'freelancer1@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'freelancer', 'Sample Freelancer', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '2026-02-06 14:47:00', '2026-05-01 13:58:51'),
(6, 1, 'client1', 'client1@example.com', '$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube', 'client', 'Sample Client', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '2026-02-06 14:47:00', '2026-05-01 13:58:51'),
(7, 1, 'sara', 'sarakey@timeforge.com', '$2y$10$gM4HuEs1G1zlqIFsoQEHIe0IpYEnig.Omygc4jJtONKBXMzvx/btG', 'freelancer', 'sara key', NULL, NULL, NULL, 1, '2026-02-13 11:27:27', '2026-02-13 11:27:27', NULL, NULL, NULL, '2026-02-13 14:10:55', '2026-05-01 13:58:43'),
(8, 1, 'Etef', 'etefmelaku@gmail.com', '$2y$10$zQe9n49AM0U3zq6S.O9uHOcxJpPRkkd719./6b6faaDJs0f1YPjIG', 'admin', 'Etefworkie Melaku', 'Melaku Digital Inc.', 'Web Design and software development', NULL, 1, '2026-05-01 08:44:17', '2026-05-01 11:49:54', 5, NULL, NULL, '2026-02-13 15:58:57', '2026-05-01 15:49:54'),
(9, 1, 'Rose', 'rose@timeforge.com', '$2y$10$HQxSMJiCwzmnIV3x9TPDf.VvzwvgnZf7B1A8KNLgxwE4rtsN8aXYW', 'client', 'Rose Etef', NULL, NULL, NULL, 1, '2026-05-01 11:28:54', '2026-05-01 11:28:54', NULL, NULL, NULL, '2026-02-13 16:07:33', '2026-05-01 15:28:54'),
(10, 1, 'ademe', 'abelconltd@gmail.com', '$2y$10$Src9cEOBTf1n1zdRp3tANO9MaXg5XxzucgO2mBFsKhO5zlD5o7aeO', 'freelancer', 'abel', NULL, NULL, NULL, 1, '2026-02-20 08:14:09', '2026-02-20 08:14:09', NULL, NULL, NULL, '2026-02-20 13:13:52', '2026-05-01 13:58:43'),
(11, 1, 'Abi', 'gizieart@gmail.com', '$2y$10$afzkYYWImgw5/3VkSx0yYuMB9L0l6aiBlnLIjhOTlRV7r0yKyFNR.', 'freelancer', 'Abegaile', NULL, NULL, NULL, 1, '2026-05-08 08:46:25', '2026-05-08 08:46:25', 4, NULL, NULL, '2026-02-27 13:06:12', '2026-05-08 12:46:25'),
(12, 4, 'George', 'wodebetf@gmail.com', '$2y$10$lK6lQtyW1sIncnFiPs7TKugzX1QCaPXSYQBHlIKFttRAj2el1DYGu', 'admin', 'George ETEF', NULL, NULL, NULL, 1, '2026-02-27 10:54:12', '2026-02-27 10:54:12', NULL, NULL, NULL, '2026-02-27 15:53:55', '2026-05-01 13:58:43'),
(13, 5, 'Abe', 'melakuetf@gmail.com', '$2y$10$lmKJzt1hDNuydhJApmK.FuKRNwvU3/cUMK3lfZMooiP7MN6z3kBSm', 'admin', 'Abegaile Ademe', NULL, NULL, NULL, 1, '2026-04-10 09:40:46', '2026-04-10 09:40:46', NULL, NULL, NULL, '2026-04-10 13:40:07', '2026-05-01 13:58:43'),
(14, 6, 'wub', 'melakunetdigital@gmail.com', '$2y$10$imeveVxDBp5bl82qqyzOOOoVQAmrPK93OCDRWTaeOn.uvv0DV2Ije', 'admin', 'bro', NULL, NULL, 'images/logos/14_logo.png', 1, '2026-05-01 09:32:03', '2026-05-01 11:10:10', 6, NULL, NULL, '2026-05-01 13:31:47', '2026-05-01 15:10:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_audit_company` (`company_id`);

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
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_company_setting` (`company_id`,`setting_key`);

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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_user` (`user_id`,`is_read`);

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
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_project_id` (`project_id`),
  ADD KEY `tasks_assigned_to` (`assigned_to`),
  ADD KEY `tasks_company_id` (`company_id`);

--
-- Indexes for table `task_comments`
--
ALTER TABLE `task_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tc_task_id` (`task_id`),
  ADD KEY `tc_company_id` (`company_id`),
  ADD KEY `tc_user_id` (`user_id`);

--
-- Indexes for table `time_entries`
--
ALTER TABLE `time_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `time_entries_fk_reviewer` (`reviewed_by`),
  ADD KEY `time_entries_company_id` (`company_id`),
  ADD KEY `te_task_id` (`task_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `screenshots`
--
ALTER TABLE `screenshots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `session_activity`
--
ALTER TABLE `session_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=455;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `task_comments`
--
ALTER TABLE `task_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `time_entries`
--
ALTER TABLE `time_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
-- Constraints for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD CONSTRAINT `fk_cs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_fk_assignee` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_fk_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_comments`
--
ALTER TABLE `task_comments`
  ADD CONSTRAINT `tc_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tc_fk_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tc_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `time_entries`
--
ALTER TABLE `time_entries`
  ADD CONSTRAINT `te_fk_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL,
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
