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
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'login_success','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)','2026-02-06 15:30:00'),(2,2,'login_success','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)','2026-02-06 15:35:00'),(5,1,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 14:36:32'),(6,1,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-06 14:48:41'),(7,9,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-27 15:27:33'),(8,0,'login_failed_invalid_user','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-27 15:43:58'),(9,0,'login_failed_invalid_user','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-27 15:44:15'),(10,12,'user_registered','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-27 15:53:55'),(11,12,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-27 15:54:12'),(12,4,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-06 15:40:14'),(13,4,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-06 15:41:02'),(14,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-06 15:49:14'),(15,12,'login_failed_wrong_password','192.168.2.11','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-06 15:55:14'),(16,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-07 14:48:28'),(17,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 13:40:57'),(18,6,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:06:57'),(19,0,'login_failed_invalid_user','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:07:19'),(20,3,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:08:06'),(21,3,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:19:05'),(22,1,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:23:25'),(23,2,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-13 15:38:14'),(24,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-27 12:22:16'),(25,0,'login_failed_invalid_user','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-27 13:52:25'),(26,9,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-27 13:52:39'),(27,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-27 14:06:36'),(28,9,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-10 12:54:19'),(29,13,'user_registered','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-10 13:40:07'),(30,13,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-10 13:40:46'),(31,4,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-10 14:18:41'),(32,8,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-10 14:18:50'),(33,8,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-10 14:59:20'),(34,8,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-11 14:45:28'),(35,10,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-11 14:46:00'),(36,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-11 14:46:15'),(37,8,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-17 12:30:37'),(38,9,'login_failed_wrong_password','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-17 12:34:00'),(39,11,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-17 12:34:20'),(40,8,'login_failed_wrong_password','192.168.2.12','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-17 12:39:10'),(41,8,'login_success','192.168.2.12','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-17 12:39:17'),(42,8,'login_success','192.168.2.12','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-17 14:42:56'),(43,4,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-01 12:14:52'),(44,11,'login_success','192.168.2.11','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-01 12:19:04'),(45,8,'login_success','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-01 12:34:25');
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
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `user_id` (`user_id`),
  KEY `created_by` (`created_by`),
  KEY `is_active` (`is_active`),
  KEY `clients_company_id` (`company_id`),
  CONSTRAINT `clients_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clients_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (3,1,'Bob The Client',NULL,'bob@timeforge.local',NULL,NULL,3,1,'2026-02-20 15:08:08','2026-04-10 14:04:10',1),(6,1,'Sample Client',NULL,'client1@example.com',NULL,NULL,6,1,'2026-02-20 15:08:08','2026-04-10 14:04:10',1),(9,1,'Rose Etef',NULL,'rose@timeforge.com',NULL,NULL,9,1,'2026-02-20 15:08:08','2026-04-10 14:04:10',1),(10,1,'Azi go','az-flowers','azibeletu@gmail.com','+164750043333','200 dawntown , toronto',NULL,11,'2026-02-27 13:53:58','2026-04-10 14:04:10',1),(11,4,'Yetayal belay','menet education','ment@menet.com','+251911963627','addis ababa ethiopia',NULL,12,'2026-02-27 15:56:39','2026-04-10 14:04:10',1),(12,3,'Abegaile','Novelnet','melakuetf@gmail.com','+16477650078','100 Gamble ave, Toronto,ON. 2H2 K4M',NULL,8,'2026-04-10 15:02:47','2026-04-10 15:02:47',1);
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'Super Admin','2026-04-10 14:04:10'),(2,'Administrator','2026-04-10 14:04:10'),(3,'Etefworkie Melaku','2026-04-10 14:04:10'),(4,'George ETEF','2026-04-10 14:04:10'),(5,'Abegaile Ademe','2026-04-10 14:04:10');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `client_feedback` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `created_by` (`created_by`),
  KEY `idx_project` (`project_id`),
  KEY `idx_client` (`client_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`),
  KEY `invoices_company_id` (`company_id`),
  CONSTRAINT `invoices_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,2,4,9,'INV-202603-0004','2026-03-27','2026-04-26',13.00,11642.73,1513.55,13156.28,'please see last month fee','sent',4,'2026-03-27 14:58:42','2026-04-11 14:57:59','classic','2026-04-11 10:56:48','gizieart@gmail.com','2026-04-11 10:57:59',NULL,NULL,NULL,'PayPal',NULL,'please send me email when you pay','we are developed the frontend , i how you can see the scalability and the stability of the app'),(6,2,4,9,'INV-202603-0004-R2','2026-03-27','2026-04-26',13.00,11642.73,1513.55,13156.28,NULL,'draft',4,'2026-03-27 15:40:50','2026-04-11 15:07:22','classic',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'we have done the UI/UX'),(7,NULL,5,12,'INV-202604-0005','2026-04-10','2026-05-10',5.00,9.57,0.48,10.05,'please confirm this invoice','draft',8,'2026-04-10 15:37:25','2026-04-10 15:37:25','corporate',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,NULL,5,12,'INV-202604-0005-R2','2026-04-10','2026-05-10',5.00,9.57,0.48,10.05,'confirm please you have received this.','draft',8,'2026-04-10 15:48:28','2026-04-10 15:48:28','corporate',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
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
  `tax_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Default tax percentage for invoices on this project',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `projects_ibfk_1` (`client_id`),
  KEY `status` (`status`),
  KEY `deleted_at` (`deleted_at`),
  KEY `projects_company_id` (`company_id`),
  CONSTRAINT `projects_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,1,'Website Redesign',NULL,3,NULL,50.00,NULL,NULL,'active','planning','2026-02-20 15:07:00','2026-04-10 14:04:10',NULL,NULL,0,NULL,NULL,NULL,0,0,0,0,1,0.00),(2,1,'SEO Audit',NULL,3,NULL,75.00,NULL,NULL,'active','planning','2026-02-20 15:07:00','2026-04-10 14:04:10',NULL,NULL,0,NULL,NULL,NULL,0,0,0,0,1,0.00),(4,1,'Melaku Digital Inc.','Melaku digital inc need website design. we must show luxury colors and font styles. and the landing page need to have animations.',9,9,35.00,20000.00,'2026-03-20','active','planning','2026-02-20 15:47:42','2026-04-10 14:04:10',NULL,NULL,0,NULL,NULL,NULL,0,0,0,0,1,0.00),(5,3,'WEbsite design','we will redesign the novelnet website ',12,8,24.00,5000.00,'2026-04-30','active','planning','2026-04-10 15:09:34','2026-04-10 15:09:34',NULL,NULL,0,NULL,NULL,NULL,0,0,0,0,1,0.00);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `screenshots`
--

DROP TABLE IF EXISTS `screenshots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `screenshots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL COMMENT 'Relative path under uploads/screenshots/',
  `file_size_kb` int(11) NOT NULL DEFAULT 0,
  `activity_score_at_capture` int(11) NOT NULL DEFAULT 0 COMMENT 'Mouse+key events recorded at time of capture',
  `captured_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_entry` (`entry_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_project` (`project_id`),
  CONSTRAINT `screenshots_fk_entry` FOREIGN KEY (`entry_id`) REFERENCES `time_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `screenshots_fk_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `screenshots_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `screenshots`
--

LOCK TABLES `screenshots` WRITE;
/*!40000 ALTER TABLE `screenshots` DISABLE KEYS */;
INSERT INTO `screenshots` VALUES (1,11,11,4,1,'uploads/screenshots/1/11/11/20260417174645_953.jpg',31,68,'2026-04-17 11:46:45'),(2,11,11,4,1,'uploads/screenshots/1/11/11/20260417174657_366.jpg',31,68,'2026-04-17 11:46:57'),(3,11,11,4,1,'uploads/screenshots/1/11/11/20260417174709_185.jpg',31,68,'2026-04-17 11:47:09'),(4,11,11,4,1,'uploads/screenshots/1/11/11/20260417174721_174.jpg',31,68,'2026-04-17 11:47:21'),(5,11,11,4,1,'uploads/screenshots/1/11/11/20260417174733_226.jpg',31,0,'2026-04-17 11:47:33'),(6,11,11,4,1,'uploads/screenshots/1/11/11/20260417174745_327.jpg',31,0,'2026-04-17 11:47:45'),(7,11,11,4,1,'uploads/screenshots/1/11/11/20260417174757_303.jpg',31,0,'2026-04-17 11:47:57'),(8,12,11,4,1,'uploads/screenshots/1/11/12/20260417174832_212.jpg',35,40,'2026-04-17 11:48:32'),(9,12,11,4,1,'uploads/screenshots/1/11/12/20260417174842_278.jpg',35,45,'2026-04-17 11:48:42');
/*!40000 ALTER TABLE `screenshots` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=275 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_activity`
--

LOCK TABLES `session_activity` WRITE;
/*!40000 ALTER TABLE `session_activity` DISABLE KEYS */;
INSERT INTO `session_activity` VALUES (1,4,2,'2026-03-13 11:46:40',154,0,154),(2,4,2,'2026-03-13 11:47:40',0,0,0),(3,4,2,'2026-03-13 11:49:56',165,0,165),(4,4,2,'2026-03-13 11:50:56',0,0,0),(5,4,2,'2026-03-13 11:51:56',0,0,0),(6,4,2,'2026-03-13 11:52:56',0,0,0),(7,4,2,'2026-03-13 11:53:56',0,0,0),(8,4,2,'2026-03-13 11:54:56',0,0,0),(9,4,2,'2026-03-13 11:55:56',0,0,0),(10,4,2,'2026-03-13 11:56:56',0,0,0),(11,4,2,'2026-03-13 11:57:56',0,0,0),(12,4,2,'2026-03-13 11:58:56',0,0,0),(13,4,2,'2026-03-13 12:00:08',0,0,0),(14,4,2,'2026-03-13 12:01:08',0,0,0),(15,4,2,'2026-03-13 12:23:51',0,0,0),(16,5,8,'2026-04-10 11:11:07',152,0,152),(17,5,8,'2026-04-10 11:12:07',0,0,0),(18,5,8,'2026-04-10 11:13:07',0,0,0),(19,5,8,'2026-04-10 11:14:07',0,0,0),(20,5,8,'2026-04-10 11:15:07',0,0,0),(21,5,8,'2026-04-10 11:16:07',0,0,0),(22,5,8,'2026-04-10 11:17:07',123,0,123),(23,5,8,'2026-04-10 11:18:37',0,0,0),(24,5,8,'2026-04-10 11:19:07',0,0,0),(25,5,8,'2026-04-10 11:20:07',0,0,0),(26,5,8,'2026-04-10 11:21:07',0,0,0),(27,5,8,'2026-04-10 11:22:07',0,0,0),(28,5,8,'2026-04-10 11:23:07',0,0,0),(29,5,8,'2026-04-10 11:24:07',0,0,0),(30,5,8,'2026-04-10 11:25:07',0,0,0),(31,5,8,'2026-04-10 11:26:07',0,0,0),(32,5,8,'2026-04-10 11:27:37',0,0,0),(33,5,8,'2026-04-10 11:28:37',0,0,0),(34,5,8,'2026-04-10 11:29:37',0,0,0),(35,5,8,'2026-04-10 11:30:37',0,0,0),(36,5,8,'2026-04-10 11:31:37',0,0,0),(37,5,8,'2026-04-10 11:32:37',0,0,0),(38,5,8,'2026-04-10 11:33:22',0,0,0),(39,6,11,'2026-04-17 08:36:28',522,0,522),(40,6,11,'2026-04-17 08:37:28',4,0,4),(41,6,11,'2026-04-17 08:38:28',1,0,1),(42,6,11,'2026-04-17 08:39:28',0,0,0),(43,6,11,'2026-04-17 08:40:29',1,0,1),(44,7,11,'2026-04-17 08:41:32',505,0,505),(45,7,11,'2026-04-17 08:42:32',91,0,91),(46,7,11,'2026-04-17 08:43:32',50,0,50),(47,7,11,'2026-04-17 08:44:32',0,0,0),(48,7,11,'2026-04-17 08:45:32',52,0,52),(49,7,11,'2026-04-17 08:46:32',1,0,1),(50,7,11,'2026-04-17 08:47:32',1,0,1),(51,7,11,'2026-04-17 08:48:32',0,0,0),(52,7,11,'2026-04-17 08:49:32',4,0,4),(53,7,11,'2026-04-17 08:50:32',0,0,0),(54,7,11,'2026-04-17 08:51:32',0,0,0),(55,7,11,'2026-04-17 08:52:32',0,0,0),(56,7,11,'2026-04-17 08:53:32',0,0,0),(57,7,11,'2026-04-17 08:54:32',0,0,0),(58,7,11,'2026-04-17 08:55:32',0,0,0),(59,7,11,'2026-04-17 08:56:32',0,0,0),(60,7,11,'2026-04-17 08:57:32',0,0,0),(61,7,11,'2026-04-17 08:58:32',0,0,0),(62,7,11,'2026-04-17 08:59:32',0,0,0),(63,7,11,'2026-04-17 09:00:32',2,0,2),(64,7,11,'2026-04-17 09:01:32',2,0,2),(65,7,11,'2026-04-17 09:02:32',0,0,0),(66,7,11,'2026-04-17 09:03:32',4,0,4),(67,7,11,'2026-04-17 09:04:32',2,0,2),(68,7,11,'2026-04-17 09:05:32',2,0,2),(69,7,11,'2026-04-17 09:06:32',0,0,0),(70,7,11,'2026-04-17 09:07:32',0,0,0),(71,7,11,'2026-04-17 09:08:32',0,0,0),(72,7,11,'2026-04-17 09:09:32',0,0,0),(73,7,11,'2026-04-17 09:10:32',0,0,0),(74,7,11,'2026-04-17 09:11:32',0,0,0),(75,7,11,'2026-04-17 09:12:32',0,0,0),(76,7,11,'2026-04-17 09:13:32',0,0,0),(77,7,11,'2026-04-17 09:14:32',0,0,0),(78,7,11,'2026-04-17 09:15:32',0,0,0),(79,7,11,'2026-04-17 09:16:32',0,0,0),(80,7,11,'2026-04-17 09:17:32',0,0,0),(81,7,11,'2026-04-17 09:18:32',0,0,0),(82,7,11,'2026-04-17 09:19:32',0,0,0),(83,7,11,'2026-04-17 09:20:32',0,0,0),(84,7,11,'2026-04-17 09:21:32',0,0,0),(85,7,11,'2026-04-17 09:22:32',0,0,0),(86,7,11,'2026-04-17 09:23:32',0,0,0),(87,7,11,'2026-04-17 09:24:32',0,0,0),(88,7,11,'2026-04-17 09:25:32',426,0,426),(89,7,11,'2026-04-17 09:27:09',0,0,0),(90,7,11,'2026-04-17 09:27:32',0,0,0),(91,7,11,'2026-04-17 09:28:32',0,0,0),(92,7,11,'2026-04-17 09:29:32',0,0,0),(93,7,11,'2026-04-17 09:30:32',0,0,0),(94,7,11,'2026-04-17 09:31:32',0,0,0),(95,7,11,'2026-04-17 09:32:32',0,0,0),(96,7,11,'2026-04-17 09:33:32',0,0,0),(97,7,11,'2026-04-17 09:34:32',0,0,0),(98,7,11,'2026-04-17 09:36:09',0,0,0),(99,7,11,'2026-04-17 09:37:09',0,0,0),(100,7,11,'2026-04-17 09:38:09',0,0,0),(101,7,11,'2026-04-17 09:39:09',0,0,0),(102,7,11,'2026-04-17 09:40:09',0,0,0),(103,7,11,'2026-04-17 09:41:09',0,0,0),(104,7,11,'2026-04-17 09:42:09',0,0,0),(105,7,11,'2026-04-17 09:43:09',0,0,0),(106,7,11,'2026-04-17 09:44:09',0,0,0),(107,7,11,'2026-04-17 09:45:09',0,0,0),(108,7,11,'2026-04-17 09:46:09',0,0,0),(109,7,11,'2026-04-17 09:47:09',0,0,0),(110,7,11,'2026-04-17 09:48:09',0,0,0),(111,7,11,'2026-04-17 09:49:09',0,0,0),(112,7,11,'2026-04-17 09:50:09',0,0,0),(113,7,11,'2026-04-17 09:51:09',0,0,0),(114,7,11,'2026-04-17 09:52:09',0,0,0),(115,7,11,'2026-04-17 09:53:09',0,0,0),(116,7,11,'2026-04-17 09:54:09',0,0,0),(117,7,11,'2026-04-17 09:55:09',0,0,0),(118,7,11,'2026-04-17 09:56:09',0,0,0),(119,7,11,'2026-04-17 09:57:09',0,0,0),(120,7,11,'2026-04-17 09:58:09',0,0,0),(121,7,11,'2026-04-17 09:59:09',0,0,0),(122,7,11,'2026-04-17 10:00:09',0,0,0),(123,7,11,'2026-04-17 10:01:09',0,0,0),(124,7,11,'2026-04-17 10:02:09',0,0,0),(125,7,11,'2026-04-17 10:03:09',0,0,0),(126,7,11,'2026-04-17 10:04:09',0,0,0),(127,7,11,'2026-04-17 10:05:09',0,0,0),(128,7,11,'2026-04-17 10:06:09',0,0,0),(129,7,11,'2026-04-17 10:07:09',0,0,0),(130,7,11,'2026-04-17 10:08:09',0,0,0),(131,7,11,'2026-04-17 10:09:09',0,0,0),(132,7,11,'2026-04-17 10:10:09',0,0,0),(133,7,11,'2026-04-17 10:11:09',0,0,0),(134,7,11,'2026-04-17 10:12:09',0,0,0),(135,7,11,'2026-04-17 10:13:09',0,0,0),(136,7,11,'2026-04-17 10:14:09',0,0,0),(137,7,11,'2026-04-17 10:15:09',0,0,0),(138,7,11,'2026-04-17 10:16:09',0,0,0),(139,7,11,'2026-04-17 10:17:09',0,0,0),(140,7,11,'2026-04-17 10:18:09',0,0,0),(141,7,11,'2026-04-17 10:19:09',0,0,0),(142,7,11,'2026-04-17 10:20:09',0,0,0),(143,7,11,'2026-04-17 10:21:09',0,0,0),(144,7,11,'2026-04-17 10:22:09',0,0,0),(145,7,11,'2026-04-17 10:23:09',0,0,0),(146,7,11,'2026-04-17 10:24:09',0,0,0),(147,7,11,'2026-04-17 10:25:09',0,0,0),(148,7,11,'2026-04-17 10:26:09',0,0,0),(149,7,11,'2026-04-17 10:27:09',0,0,0),(150,7,11,'2026-04-17 10:28:09',0,0,0),(151,7,11,'2026-04-17 10:29:09',0,0,0),(152,7,11,'2026-04-17 10:30:09',0,0,0),(153,7,11,'2026-04-17 10:31:09',0,0,0),(154,7,11,'2026-04-17 10:32:09',0,0,0),(155,7,11,'2026-04-17 10:33:09',0,0,0),(156,7,11,'2026-04-17 10:34:09',0,0,0),(157,7,11,'2026-04-17 10:35:09',0,0,0),(158,7,11,'2026-04-17 10:36:09',0,0,0),(159,7,11,'2026-04-17 10:37:09',0,0,0),(160,7,11,'2026-04-17 10:38:09',0,0,0),(161,7,11,'2026-04-17 10:39:09',0,0,0),(162,7,11,'2026-04-17 10:40:09',0,0,0),(163,7,11,'2026-04-17 10:41:09',0,0,0),(164,7,11,'2026-04-17 10:41:39',0,0,0),(165,8,11,'2026-04-17 10:43:25',755,0,755),(166,9,8,'2026-04-17 10:44:21',222,0,222),(167,8,11,'2026-04-17 10:44:25',0,0,0),(168,9,8,'2026-04-17 10:45:21',0,0,0),(169,8,11,'2026-04-17 10:45:25',0,0,0),(170,9,8,'2026-04-17 10:46:21',0,0,0),(171,8,11,'2026-04-17 10:46:25',0,0,0),(172,9,8,'2026-04-17 10:47:22',40,0,40),(173,8,11,'2026-04-17 10:47:24',413,0,413),(174,9,8,'2026-04-17 10:48:22',0,0,0),(175,8,11,'2026-04-17 10:48:24',34,0,34),(176,9,8,'2026-04-17 10:49:22',0,0,0),(177,8,11,'2026-04-17 10:49:24',0,0,0),(178,9,8,'2026-04-17 10:50:22',0,0,0),(179,8,11,'2026-04-17 10:50:59',132,0,132),(180,9,8,'2026-04-17 10:51:22',0,0,0),(181,8,11,'2026-04-17 10:51:59',0,0,0),(182,9,8,'2026-04-17 10:52:22',0,0,0),(183,8,11,'2026-04-17 10:52:59',41,0,41),(184,9,8,'2026-04-17 10:53:22',0,0,0),(185,8,11,'2026-04-17 10:53:59',0,0,0),(186,9,8,'2026-04-17 10:54:22',0,0,0),(187,8,11,'2026-04-17 10:54:59',0,0,0),(188,9,8,'2026-04-17 10:55:22',0,0,0),(189,8,11,'2026-04-17 10:55:59',0,0,0),(190,9,8,'2026-04-17 10:56:22',0,0,0),(191,8,11,'2026-04-17 10:56:59',0,0,0),(192,8,11,'2026-04-17 10:57:59',0,0,0),(193,9,8,'2026-04-17 10:58:09',0,0,0),(194,8,11,'2026-04-17 10:58:59',0,0,0),(195,9,8,'2026-04-17 10:59:09',0,0,0),(196,8,11,'2026-04-17 10:59:59',0,0,0),(197,9,8,'2026-04-17 11:00:09',0,0,0),(198,8,11,'2026-04-17 11:00:59',0,0,0),(199,9,8,'2026-04-17 11:01:09',0,0,0),(200,8,11,'2026-04-17 11:01:59',0,0,0),(201,9,8,'2026-04-17 11:02:09',0,0,0),(202,9,8,'2026-04-17 11:03:09',0,0,0),(203,8,11,'2026-04-17 11:03:09',0,0,0),(204,9,8,'2026-04-17 11:04:09',0,0,0),(205,8,11,'2026-04-17 11:04:09',0,0,0),(206,9,8,'2026-04-17 11:05:09',0,0,0),(207,8,11,'2026-04-17 11:05:09',0,0,0),(208,9,8,'2026-04-17 11:06:09',0,0,0),(209,8,11,'2026-04-17 11:06:09',0,0,0),(210,8,11,'2026-04-17 11:07:09',0,0,0),(211,9,8,'2026-04-17 11:07:09',0,0,0),(212,8,11,'2026-04-17 11:08:09',0,0,0),(213,9,8,'2026-04-17 11:08:09',0,0,0),(214,9,8,'2026-04-17 11:09:09',0,0,0),(215,8,11,'2026-04-17 11:09:09',0,0,0),(216,9,8,'2026-04-17 11:10:09',0,0,0),(217,8,11,'2026-04-17 11:10:09',0,0,0),(218,8,11,'2026-04-17 11:11:09',0,0,0),(219,9,8,'2026-04-17 11:11:09',0,0,0),(220,9,8,'2026-04-17 11:12:09',0,0,0),(221,8,11,'2026-04-17 11:12:09',0,0,0),(222,8,11,'2026-04-17 11:13:09',0,0,0),(223,9,8,'2026-04-17 11:13:09',0,0,0),(224,9,8,'2026-04-17 11:14:09',0,0,0),(225,8,11,'2026-04-17 11:14:09',0,0,0),(226,8,11,'2026-04-17 11:15:09',0,0,0),(227,9,8,'2026-04-17 11:15:09',0,0,0),(228,8,11,'2026-04-17 11:16:09',0,0,0),(229,9,8,'2026-04-17 11:16:09',0,0,0),(230,9,8,'2026-04-17 11:17:09',0,0,0),(231,8,11,'2026-04-17 11:17:09',0,0,0),(232,8,11,'2026-04-17 11:18:09',0,0,0),(233,9,8,'2026-04-17 11:18:09',0,0,0),(234,8,11,'2026-04-17 11:19:09',0,0,0),(235,9,8,'2026-04-17 11:19:09',0,0,0),(236,8,11,'2026-04-17 11:20:09',0,0,0),(237,9,8,'2026-04-17 11:20:09',0,0,0),(238,9,8,'2026-04-17 11:21:09',0,0,0),(239,8,11,'2026-04-17 11:21:09',0,0,0),(240,8,11,'2026-04-17 11:22:09',0,0,0),(241,9,8,'2026-04-17 11:22:09',0,0,0),(242,9,8,'2026-04-17 11:23:09',0,0,0),(243,8,11,'2026-04-17 11:23:09',0,0,0),(244,9,8,'2026-04-17 11:24:09',0,0,0),(245,8,11,'2026-04-17 11:24:09',0,0,0),(246,8,11,'2026-04-17 11:25:09',0,0,0),(247,9,8,'2026-04-17 11:25:09',0,0,0),(248,9,8,'2026-04-17 11:26:09',0,0,0),(249,8,11,'2026-04-17 11:26:09',0,0,0),(250,9,8,'2026-04-17 11:27:09',0,0,0),(251,8,11,'2026-04-17 11:27:09',0,0,0),(252,8,11,'2026-04-17 11:28:09',0,0,0),(253,9,8,'2026-04-17 11:28:09',0,0,0),(254,8,11,'2026-04-17 11:29:09',0,0,0),(255,9,8,'2026-04-17 11:29:09',0,0,0),(256,8,11,'2026-04-17 11:30:09',0,0,0),(257,9,8,'2026-04-17 11:30:09',0,0,0),(258,9,8,'2026-04-17 11:31:09',0,0,0),(259,9,8,'2026-04-17 11:32:09',0,0,0),(260,9,8,'2026-04-17 11:33:09',0,0,0),(261,9,8,'2026-04-17 11:33:50',0,0,0),(262,10,11,'2026-04-17 11:33:54',576,0,576),(263,10,11,'2026-04-17 11:34:54',0,0,0),(264,10,11,'2026-04-17 11:35:54',0,0,0),(265,10,11,'2026-04-17 11:36:54',0,0,0),(266,10,11,'2026-04-17 11:38:14',1409,0,1409),(267,10,11,'2026-04-17 11:39:14',0,0,0),(268,10,11,'2026-04-17 11:40:14',0,0,0),(269,10,11,'2026-04-17 11:41:14',0,0,0),(270,10,11,'2026-04-17 11:42:14',0,0,0),(271,10,11,'2026-04-17 11:43:14',0,0,0),(272,10,11,'2026-04-17 11:44:14',0,0,0),(273,11,11,'2026-04-17 11:46:22',125,2,127),(274,11,11,'2026-04-17 11:47:23',68,0,68);
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
  `close_reason` enum('manual','auto','abandoned') DEFAULT 'manual' COMMENT 'How the session was closed',
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `time_entries_fk_reviewer` (`reviewed_by`),
  KEY `time_entries_company_id` (`company_id`),
  CONSTRAINT `time_entries_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `time_entries_fk_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `time_entries_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `time_entries_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_entries`
--

LOCK TABLES `time_entries` WRITE;
/*!40000 ALTER TABLE `time_entries` DISABLE KEYS */;
INSERT INTO `time_entries` VALUES (1,1,1,NULL,'2025-10-20 09:00:00','2025-10-20 12:00:00',10800,'Initial layout design',1,0,'completed','timer',NULL,NULL,NULL,'2026-02-27 14:16:21','2026-04-10 14:04:10',0,0,NULL,'manual'),(2,1,1,NULL,'2025-10-21 14:00:00','2025-10-21 16:30:00',9000,'Fixing navigation bar bug',1,0,'completed','timer',NULL,NULL,NULL,'2026-02-27 14:16:21','2026-04-10 14:04:10',0,0,NULL,'manual'),(3,1,4,4,'2026-03-06 10:49:46','2026-03-27 08:24:38',1805692,'Melaku Digital Inc.',1,0,'abandoned','timer',NULL,NULL,NULL,'2026-03-06 15:49:46','2026-04-10 14:04:10',0,0,NULL,'abandoned'),(4,1,4,2,'2026-03-13 11:45:40','2026-03-27 08:24:38',1197538,'Freelance work',1,0,'approved','timer',4,'2026-03-27 10:55:32',NULL,'2026-03-13 15:45:40','2026-04-10 14:04:10',0,0,NULL,'abandoned'),(5,3,5,8,'2026-04-10 11:10:07','2026-04-10 11:34:03',1436,'Design: start phase 1',1,0,'approved','timer',8,'2026-04-10 11:34:28',NULL,'2026-04-10 15:10:07','2026-04-10 15:34:28',0,0,NULL,'manual'),(6,1,4,11,'2026-04-17 08:35:28','2026-04-17 08:40:29',301,'General work',1,0,'completed','timer',NULL,NULL,NULL,'2026-04-17 12:35:28','2026-04-17 12:40:29',0,0,NULL,'manual'),(7,1,4,11,'2026-04-17 08:40:32','2026-04-17 10:42:21',7309,'General work',1,0,'completed','timer',NULL,NULL,NULL,'2026-04-17 12:40:32','2026-04-17 14:42:21',0,0,NULL,'manual'),(8,1,2,11,'2026-04-17 10:42:24','2026-04-17 11:30:24',2880,'SEO Audit',1,0,'completed','timer',NULL,NULL,NULL,'2026-04-17 14:42:24','2026-04-17 15:30:24',0,0,NULL,'manual'),(9,3,5,8,'2026-04-17 10:43:21','2026-04-17 11:33:56',3035,'Testing: security testing',1,0,'completed','timer',NULL,NULL,NULL,'2026-04-17 14:43:21','2026-04-17 15:33:56',0,0,NULL,'manual'),(10,1,2,11,'2026-04-17 11:32:37','2026-04-17 11:45:19',762,'SEO Audit',1,0,'completed','timer',NULL,NULL,NULL,'2026-04-17 15:32:37','2026-04-17 15:45:19',0,0,NULL,'manual'),(11,1,4,11,'2026-04-17 11:45:22','2026-04-17 11:48:08',166,'Melaku Digital Inc.',1,0,'completed','timer',NULL,NULL,NULL,'2026-04-17 15:45:22','2026-04-17 15:48:08',0,0,NULL,'manual'),(12,1,4,11,'2026-04-17 11:48:22','2026-04-17 11:48:51',29,'Melaku Digital Inc.',1,0,'completed','timer',NULL,NULL,NULL,'2026-04-17 15:48:22','2026-04-17 15:48:51',0,0,NULL,'manual'),(13,1,4,11,'2026-05-01 08:19:47',NULL,0,'Coding: digimarkt enhancement',1,0,'running','timer',NULL,NULL,NULL,'2026-05-01 12:19:47','2026-05-01 12:19:47',0,0,NULL,'manual'),(14,3,5,8,'2026-05-01 08:34:52',NULL,0,'Coding: working',1,0,'running','timer',NULL,NULL,NULL,'2026-05-01 12:34:52','2026-05-01 12:34:52',0,0,NULL,'manual');
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `users_fk_current_project` (`current_project_id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `users_fk_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_fk_current_project` FOREIGN KEY (`current_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'admin_user','admin@timeforge.local','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','admin','Super Admin',NULL,NULL,NULL,1,'2026-03-13 11:23:25',NULL,NULL,NULL,NULL,'2026-01-30 13:57:53','2026-04-10 14:04:10'),(2,1,'dev_sarah','sarah@timeforge.local','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','freelancer','Sarah Developer',NULL,NULL,NULL,1,'2026-03-13 11:38:14','2026-03-13 12:23:51',4,NULL,NULL,'2026-01-30 13:57:53','2026-04-10 14:04:10'),(3,1,'client_bob','bob@timeforge.local','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','client','Bob The Client',NULL,NULL,NULL,1,'2026-03-13 11:19:05',NULL,NULL,NULL,NULL,'2026-01-30 13:57:53','2026-04-10 14:04:10'),(4,2,'admin','admin@example.com','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','admin','Administrator','Melaku Digital Inc.',NULL,'images/logos/4_logo.png',1,'2026-05-01 08:14:52','2026-03-06 14:40:38',4,NULL,NULL,'2026-02-06 14:47:00','2026-05-01 12:14:52'),(5,1,'freelancer1','freelancer1@example.com','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','freelancer','Sample Freelancer',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2026-02-06 14:47:00','2026-04-10 14:04:10'),(6,1,'client1','client1@example.com','$2y$10$Bq0fhgYEsUffmExi0ETWleY89s0GFyuQ9EVRI2O4k2iAHcYtmMube','client','Sample Client',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2026-02-06 14:47:00','2026-04-10 14:04:10'),(7,1,'sara','sarakey@timeforge.com','$2y$10$gM4HuEs1G1zlqIFsoQEHIe0IpYEnig.Omygc4jJtONKBXMzvx/btG','freelancer','sara key',NULL,NULL,NULL,1,'2026-02-13 11:27:27',NULL,NULL,NULL,NULL,'2026-02-13 14:10:55','2026-04-10 14:04:10'),(8,1,'Etef','etefmelaku@gmail.com','$2y$10$zQe9n49AM0U3zq6S.O9uHOcxJpPRkkd719./6b6faaDJs0f1YPjIG','admin','Etefworkie Melaku','Melaku Digital Inc.','Web Design and software development',NULL,1,'2026-05-01 08:34:25','2026-05-01 08:34:52',5,NULL,NULL,'2026-02-13 15:58:57','2026-05-01 12:40:11'),(9,1,'Rose','rose@timeforge.com','$2y$10$HQxSMJiCwzmnIV3x9TPDf.VvzwvgnZf7B1A8KNLgxwE4rtsN8aXYW','client','Rose Etef',NULL,NULL,NULL,1,'2026-04-10 08:54:19',NULL,NULL,NULL,NULL,'2026-02-13 16:07:33','2026-04-10 14:04:10'),(10,1,'ademe','abelconltd@gmail.com','$2y$10$Src9cEOBTf1n1zdRp3tANO9MaXg5XxzucgO2mBFsKhO5zlD5o7aeO','freelancer','abel',NULL,NULL,NULL,1,'2026-02-20 08:14:09',NULL,NULL,NULL,NULL,'2026-02-20 13:13:52','2026-04-10 14:04:10'),(11,1,'Abi','gizieart@gmail.com','$2y$10$afzkYYWImgw5/3VkSx0yYuMB9L0l6aiBlnLIjhOTlRV7r0yKyFNR.','freelancer','Abegaile',NULL,NULL,NULL,1,'2026-05-01 08:19:04','2026-05-01 08:19:47',4,NULL,NULL,'2026-02-27 13:06:12','2026-05-01 12:19:47'),(12,4,'George','wodebetf@gmail.com','$2y$10$lK6lQtyW1sIncnFiPs7TKugzX1QCaPXSYQBHlIKFttRAj2el1DYGu','admin','George ETEF',NULL,NULL,NULL,1,'2026-02-27 10:54:12',NULL,NULL,NULL,NULL,'2026-02-27 15:53:55','2026-04-10 14:04:10'),(13,5,'Abe','melakuetf@gmail.com','$2y$10$lmKJzt1hDNuydhJApmK.FuKRNwvU3/cUMK3lfZMooiP7MN6z3kBSm','admin','Abegaile Ademe',NULL,NULL,NULL,1,'2026-04-10 09:40:46',NULL,NULL,NULL,NULL,'2026-04-10 13:40:07','2026-04-10 14:04:10');
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

-- Dump completed on 2026-05-01  8:42:43
