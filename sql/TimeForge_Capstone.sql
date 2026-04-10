-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: TimeForge_Capstone
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'login_success','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)','2026-02-06 15:30:00'),(2,2,'login_success','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)','2026-02-06 15:35:00'),(5,1,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 14:36:32'),(6,1,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 14:48:41'),(7,9,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-27 15:27:33'),(8,0,'login_failed_invalid_user','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-27 15:43:58'),(9,0,'login_failed_invalid_user','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-27 15:44:15'),(10,12,'user_registered','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-27 15:53:55'),(11,12,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-27 15:54:12'),(12,4,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-06 15:40:14'),(13,4,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-06 15:41:02'),(14,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-06 15:49:14'),(15,12,'login_failed_wrong_password','192.168.2.11','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-06 15:55:14'),(16,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-07 14:48:28'),(17,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 13:40:57'),(18,6,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:06:57'),(19,0,'login_failed_invalid_user','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:07:19'),(20,3,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:08:06'),(21,3,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:19:05'),(22,1,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:23:25'),(23,2,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:38:14'),(24,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-27 12:22:16'),(25,0,'login_failed_invalid_user','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-27 13:52:25'),(26,9,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-27 13:52:39'),(27,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-27 14:06:36'),(28,9,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-10 12:54:19');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(100) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `user_id` (`user_id`),
  KEY `created_by` (`created_by`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clients_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (3,'Bob The Client',NULL,'bob@timeforge.local',NULL,NULL,3,1,'2026-02-20 15:08:08','2026-02-20 15:08:08',1),(6,'Sample Client',NULL,'client1@example.com',NULL,NULL,6,1,'2026-02-20 15:08:08','2026-02-20 15:08:08',1),(9,'Rose Etef',NULL,'rose@timeforge.com',NULL,NULL,9,1,'2026-02-20 15:08:08','2026-02-20 15:08:08',1),(10,'Azi go','az-flowers','azibeletu@gmail.com','+164750043333','200 dawntown , toronto',NULL,11,'2026-02-27 13:53:58','2026-02-27 13:53:58',1),(11,'Yetayal belay','menet education','ment@menet.com','+251911963627','addis ababa ethiopia',NULL,12,'2026-02-27 15:56:39','2026-02-27 15:56:39',1);
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `viewed_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `partial_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_notes` text DEFAULT NULL,
  `client_feedback` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `created_by` (`created_by`),
  KEY `idx_project` (`project_id`),
  KEY `idx_client` (`client_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,4,9,'INV-202603-0004','2026-03-27','2026-04-26',13.00,11642.73,1513.55,13156.28,'please see last month fee','draft',4,'2026-03-27 14:58:42','2026-03-27 14:58:42','classic',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,4,9,'INV-202603-0004-R2','2026-03-27','2026-04-26',13.00,11642.73,1513.55,13156.28,NULL,'draft',4,'2026-03-27 15:40:50','2026-03-27 15:40:50','modern',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `tax_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Default tax percentage for invoices on this project',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `projects_ibfk_1` (`client_id`),
  KEY `status` (`status`),
  KEY `deleted_at` (`deleted_at`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,'Website Redesign',NULL,3,NULL,50.00,NULL,NULL,'active','planning','2026-02-20 15:07:00','2026-02-20 15:07:00',NULL,NULL,0,NULL,NULL,NULL,0,0,0,0,0.00),(2,'SEO Audit',NULL,3,NULL,75.00,NULL,NULL,'active','planning','2026-02-20 15:07:00','2026-02-20 15:07:00',NULL,NULL,0,NULL,NULL,NULL,0,0,0,0,0.00),(4,'Melaku Digital Inc.','Melaku digital inc need website design. we must show luxury colors and font styles. and the landing page need to have animations.',9,9,35.00,20000.00,'2026-03-20','active','planning','2026-02-20 15:47:42','2026-02-20 15:47:42',NULL,NULL,0,NULL,NULL,NULL,0,0,0,0,0.00);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_activity`
--

DROP TABLE IF EXISTS `session_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time_entry_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recorded_at` datetime NOT NULL,
  `mouse_events` int(11) DEFAULT 0 COMMENT 'Mouse moves + clicks in this minute',
  `key_events` int(11) DEFAULT 0 COMMENT 'Keystrokes in this minute',
  `activity_score` int(11) DEFAULT 0 COMMENT 'Total events (mouse + key) in this minute',
  PRIMARY KEY (`id`),
  KEY `idx_entry` (`time_entry_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_time` (`recorded_at`),
  CONSTRAINT `session_activity_ibfk_1` FOREIGN KEY (`time_entry_id`) REFERENCES `time_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `session_activity_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_activity`
--

LOCK TABLES `session_activity` WRITE;
/*!40000 ALTER TABLE `session_activity` DISABLE KEYS */;
INSERT INTO `session_activity` VALUES (1,4,2,'2026-03-13 11:46:40',154,0,154),(2,4,2,'2026-03-13 11:47:40',0,0,0),(3,4,2,'2026-03-13 11:49:56',165,0,165),(4,4,2,'2026-03-13 11:50:56',0,0,0),(5,4,2,'2026-03-13 11:51:56',0,0,0),(6,4,2,'2026-03-13 11:52:56',0,0,0),(7,4,2,'2026-03-13 11:53:56',0,0,0),(8,4,2,'2026-03-13 11:54:56',0,0,0),(9,4,2,'2026-03-13 11:55:56',0,0,0),(10,4,2,'2026-03-13 11:56:56',0,0,0),(11,4,2,'2026-03-13 11:57:56',0,0,0),(12,4,2,'2026-03-13 11:58:56',0,0,0),(13,4,2,'2026-03-13 12:00:08',0,0,0),(14,4,2,'2026-03-13 12:01:08',0,0,0),(15,4,2,'2026-03-13 12:23:51',0,0,0);
/*!40000 ALTER TABLE `session_activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_entries`
--

DROP TABLE IF EXISTS `time_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `time_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `close_reason` enum('manual','auto','abandoned') DEFAULT 'manual' COMMENT 'How the session was closed',
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `time_entries_fk_reviewer` (`reviewed_by`),
  CONSTRAINT `time_entries_fk_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `time_entries_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `time_entries_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_entries`
--

LOCK TABLES `time_entries` WRITE;
/*!40000 ALTER TABLE `time_entries` DISABLE KEYS */;
INSERT INTO `time_entries` VALUES (1,1,NULL,'2025-10-20 09:00:00','2025-10-20 12:00:00',10800,'Initial layout design',1,0,'completed','timer',NULL,NULL,NULL,'2026-02-27 14:16:21','2026-03-06 14:30:13',0,0,NULL,'manual'),(2,1,NULL,'2025-10-21 14:00:00','2025-10-21 16:30:00',9000,'Fixing navigation bar bug',1,0,'completed','timer',NULL,NULL,NULL,'2026-02-27 14:16:21','2026-03-06 14:30:13',0,0,NULL,'manual'),(3,4,4,'2026-03-06 10:49:46','2026-03-27 08:24:38',1805692,'Melaku Digital Inc.',1,0,'abandoned','timer',NULL,NULL,NULL,'2026-03-06 15:49:46','2026-03-27 12:24:38',0,0,NULL,'abandoned'),(4,4,2,'2026-03-13 11:45:40','2026-03-27 08:24:38',1197538,'Freelance work',1,0,'approved','timer',4,'2026-03-27 10:55:32',NULL,'2026-03-13 15:45:40','2026-03-27 14:55:32',0,0,NULL,'abandoned');
/*!40000 ALTER TABLE `time_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `users_fk_current_project` (`current_project_id`),
  CONSTRAINT `users_fk_current_project` FOREIGN KEY (`current_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin_user','admin@timeforge.local','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','admin','Super Admin',NULL,NULL,1,'2026-03-13 11:23:25',NULL,NULL,NULL,NULL,'2026-01-30 13:57:53','2026-03-13 15:23:25'),(2,'dev_sarah','sarah@timeforge.local','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','freelancer','Sarah Developer',NULL,NULL,1,'2026-03-13 11:38:14','2026-03-13 12:23:51',4,NULL,NULL,'2026-01-30 13:57:53','2026-03-13 16:23:51'),(3,'client_bob','bob@timeforge.local','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','client','Bob The Client',NULL,NULL,1,'2026-03-13 11:19:05',NULL,NULL,NULL,NULL,'2026-01-30 13:57:53','2026-03-13 15:19:05'),(4,'admin','admin@example.com','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','admin','Administrator',NULL,NULL,1,'2026-03-27 10:06:36','2026-03-06 14:40:38',4,NULL,NULL,'2026-02-06 14:47:00','2026-03-27 14:06:36'),(5,'freelancer1','freelancer1@example.com','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','freelancer','Sample Freelancer',NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2026-02-06 14:47:00','2026-03-13 15:17:32'),(6,'client1','client1@example.com','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','client','Sample Client',NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2026-02-06 14:47:00','2026-03-13 15:17:32'),(7,'sara','sarakey@timeforge.com','$2y$10$gM4HuEs1G1zlqIFsoQEHIe0IpYEnig.Omygc4jJtONKBXMzvx/btG','freelancer','sara key',NULL,NULL,1,'2026-02-13 11:27:27',NULL,NULL,NULL,NULL,'2026-02-13 14:10:55','2026-02-13 16:27:27'),(8,'Etef','etefmelaku@gmail.com','$2y$10$zQe9n49AM0U3zq6S.O9uHOcxJpPRkkd719./6b6faaDJs0f1YPjIG','admin','Etefworkie Melaku',NULL,NULL,1,'2026-02-20 10:48:13',NULL,NULL,NULL,NULL,'2026-02-13 15:58:57','2026-02-20 15:48:13'),(9,'Rose','rose@timeforge.com','$2y$10$HQxSMJiCwzmnIV3x9TPDf.VvzwvgnZf7B1A8KNLgxwE4rtsN8aXYW','client','Rose Etef',NULL,NULL,1,'2026-04-10 08:54:19',NULL,NULL,NULL,NULL,'2026-02-13 16:07:33','2026-04-10 12:54:19'),(10,'ademe','abelconltd@gmail.com','$2y$10$Src9cEOBTf1n1zdRp3tANO9MaXg5XxzucgO2mBFsKhO5zlD5o7aeO','freelancer','abel',NULL,NULL,1,'2026-02-20 08:14:09',NULL,NULL,NULL,NULL,'2026-02-20 13:13:52','2026-02-20 13:14:09'),(11,'Abi','gizieart@gmail.com','$2y$10$afzkYYWImgw5/3VkSx0yYuMB9L0l6aiBlnLIjhOTlRV7r0yKyFNR.','freelancer','Abegaile',NULL,NULL,1,'2026-02-27 08:06:31',NULL,NULL,NULL,NULL,'2026-02-27 13:06:12','2026-02-27 13:06:31'),(12,'George','wodebetf@gmail.com','$2y$10$lK6lQtyW1sIncnFiPs7TKugzX1QCaPXSYQBHlIKFttRAj2el1DYGu','admin','George ETEF',NULL,NULL,1,'2026-02-27 10:54:12',NULL,NULL,NULL,NULL,'2026-02-27 15:53:55','2026-02-27 15:54:12');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-10  9:24:39
