-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: laravel
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumnos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nac` date NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumnos_email_unique` (`email`),
  UNIQUE KEY `alumnos_telefono_unique` (`telefono`),
  UNIQUE KEY `alumnos_documento_unique` (`documento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumnos`
--

LOCK TABLES `alumnos` WRITE;
/*!40000 ALTER TABLE `alumnos` DISABLE KEYS */;
/*!40000 ALTER TABLE `alumnos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banners` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_stocks`
--

DROP TABLE IF EXISTS `branch_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branch_stocks` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stockable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stockable_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock` decimal(12,3) NOT NULL DEFAULT '0.000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_stock_unique` (`branch_id`,`stockable_id`,`stockable_type`),
  KEY `branch_stocks_stockable_type_stockable_id_index` (`stockable_type`,`stockable_id`),
  CONSTRAINT `branch_stocks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_stocks`
--

LOCK TABLES `branch_stocks` WRITE;
/*!40000 ALTER TABLE `branch_stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES ('a29c3eb4-6e54-48e6-b4fa-6acd0ec83bd6','Sucursal Principal','Dirección Principal','',1,'2026-08-28 11:18:26','2026-08-28 11:18:26',NULL);
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_movement_categories`
--

DROP TABLE IF EXISTS `cash_movement_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_movement_categories` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('income','expense','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_movement_categories`
--

LOCK TABLES `cash_movement_categories` WRITE;
/*!40000 ALTER TABLE `cash_movement_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `cash_movement_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_movements`
--

DROP TABLE IF EXISTS `cash_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_movements` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cash_session_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,0) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_movements_cash_session_id_foreign` (`cash_session_id`),
  CONSTRAINT `cash_movements_cash_session_id_foreign` FOREIGN KEY (`cash_session_id`) REFERENCES `cash_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_movements`
--

LOCK TABLES `cash_movements` WRITE;
/*!40000 ALTER TABLE `cash_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `cash_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_registers`
--

DROP TABLE IF EXISTS `cash_registers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_registers` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_registers_branch_id_foreign` (`branch_id`),
  CONSTRAINT `cash_registers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_registers`
--

LOCK TABLES `cash_registers` WRITE;
/*!40000 ALTER TABLE `cash_registers` DISABLE KEYS */;
INSERT INTO `cash_registers` VALUES ('a29c39c2-2ad0-4515-897c-08d89377e166','Caja Principal',1,'Caja por defecto del sistema','2026-08-28 11:04:36','2026-10-12 19:57:14','a29c3eb4-6e54-48e6-b4fa-6acd0ec83bd6');
/*!40000 ALTER TABLE `cash_registers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_sessions`
--

DROP TABLE IF EXISTS `cash_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_sessions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cash_register_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_amount` decimal(15,0) NOT NULL,
  `closing_amount` decimal(15,0) DEFAULT NULL,
  `calculated_amount` decimal(15,0) DEFAULT NULL,
  `difference` decimal(15,0) DEFAULT NULL,
  `opened_at` timestamp NOT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `opening_notes` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_sessions_user_id_status_index` (`user_id`,`status`),
  KEY `cash_sessions_cash_register_id_status_index` (`cash_register_id`,`status`),
  KEY `idx_cash_sessions_status_user` (`status`,`user_id`),
  CONSTRAINT `cash_sessions_cash_register_id_foreign` FOREIGN KEY (`cash_register_id`) REFERENCES `cash_registers` (`id`),
  CONSTRAINT `cash_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_sessions`
--

LOCK TABLES `cash_sessions` WRITE;
/*!40000 ALTER TABLE `cash_sessions` DISABLE KEYS */;
INSERT INTO `cash_sessions` VALUES ('a313048b-4fe1-4a40-89a2-003f46ab9d0e','a312fe9a-c9f8-49b9-b464-06a923ab66c9','a29c39c2-2ad0-4515-897c-08d89377e166',1000,NULL,NULL,NULL,'2026-10-26 13:19:19',NULL,'open','',NULL,'2026-10-26 13:19:19','2026-10-26 13:19:19');
/*!40000 ALTER TABLE `cash_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_transactions`
--

DROP TABLE IF EXISTS `customer_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_transactions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('debit','credit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,0) NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_transactions_customer_id_created_at_index` (`customer_id`,`created_at`),
  CONSTRAINT `customer_transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_transactions`
--

LOCK TABLES `customer_transactions` WRITE;
/*!40000 ALTER TABLE `customer_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_ruc_unique` (`ruc`),
  KEY `customers_ruc_index` (`ruc`),
  KEY `idx_customers_active_name` (`is_active`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deboning_items`
--

DROP TABLE IF EXISTS `deboning_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deboning_items` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deboning_session_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `weight` decimal(10,3) NOT NULL DEFAULT '0.000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deboning_items_deboning_session_id_foreign` (`deboning_session_id`),
  KEY `deboning_items_product_id_foreign` (`product_id`),
  CONSTRAINT `deboning_items_deboning_session_id_foreign` FOREIGN KEY (`deboning_session_id`) REFERENCES `deboning_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deboning_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deboning_items`
--

LOCK TABLES `deboning_items` WRITE;
/*!40000 ALTER TABLE `deboning_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `deboning_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deboning_sessions`
--

DROP TABLE IF EXISTS `deboning_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deboning_sessions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `raw_material_product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_weight_start` decimal(10,3) NOT NULL DEFAULT '0.000',
  `total_weight_yield` decimal(10,3) NOT NULL DEFAULT '0.000',
  `waste_weight` decimal(10,3) NOT NULL DEFAULT '0.000',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deboning_sessions_raw_material_product_id_foreign` (`raw_material_product_id`),
  KEY `deboning_sessions_user_id_foreign` (`user_id`),
  CONSTRAINT `deboning_sessions_raw_material_product_id_foreign` FOREIGN KEY (`raw_material_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deboning_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deboning_sessions`
--

LOCK TABLES `deboning_sessions` WRITE;
/*!40000 ALTER TABLE `deboning_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `deboning_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_categories` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_category_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cash_session_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  KEY `expenses_user_id_foreign` (`user_id`),
  KEY `expenses_cash_session_id_foreign` (`cash_session_id`),
  CONSTRAINT `expenses_cash_session_id_foreign` FOREIGN KEY (`cash_session_id`) REFERENCES `cash_sessions` (`id`),
  CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horario_detalles`
--

DROP TABLE IF EXISTS `horario_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horario_detalles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_horario` bigint unsigned NOT NULL,
  `id_materia` bigint unsigned NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `horario_detalles_id_horario_id_materia_unique` (`id_horario`,`id_materia`),
  KEY `horario_detalles_id_materia_foreign` (`id_materia`),
  CONSTRAINT `horario_detalles_id_horario_foreign` FOREIGN KEY (`id_horario`) REFERENCES `horarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `horario_detalles_id_materia_foreign` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horario_detalles`
--

LOCK TABLES `horario_detalles` WRITE;
/*!40000 ALTER TABLE `horario_detalles` DISABLE KEYS */;
/*!40000 ALTER TABLE `horario_detalles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dia` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `horarios_dia_unique` (`dia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_movements`
--

DROP TABLE IF EXISTS `inventory_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in',
  `quantity` decimal(10,3) NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_movements_product_id_foreign` (`product_id`),
  KEY `inventory_movements_user_id_foreign` (`user_id`),
  CONSTRAINT `inventory_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_movements`
--

LOCK TABLES `inventory_movements` WRITE;
/*!40000 ALTER TABLE `inventory_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `license_payments`
--

DROP TABLE IF EXISTS `license_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `license_payments` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `license_payments`
--

LOCK TABLES `license_payments` WRITE;
/*!40000 ALTER TABLE `license_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `license_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `licenses`
--

DROP TABLE IF EXISTS `licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `licenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `licenses`
--

LOCK TABLES `licenses` WRITE;
/*!40000 ALTER TABLE `licenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `licenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materias`
--

DROP TABLE IF EXISTS `materias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_profesor` bigint unsigned NOT NULL,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `materias_codigo_unique` (`codigo`),
  KEY `materias_id_profesor_foreign` (`id_profesor`),
  CONSTRAINT `materias_id_profesor_foreign` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materias`
--

LOCK TABLES `materias` WRITE;
/*!40000 ALTER TABLE `materias` DISABLE KEYS */;
/*!40000 ALTER TABLE `materias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'custom',
  `reference_id` bigint unsigned DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `parent_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_items_parent_id_foreign` (`parent_id`),
  CONSTRAINT `menu_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2014_10_12_200000_add_two_factor_columns_to_users_table',1),(4,'2019_08_19_000000_create_failed_jobs_table',1),(5,'2019_12_14_000001_create_personal_access_tokens_table',1),(6,'2026_01_23_123530_create_locations_table',1),(7,'2026_01_23_123532_create_store_settings_table',1),(8,'2026_01_23_123534_create_categories_table',1),(9,'2026_01_23_123536_create_products_table',1),(10,'2026_01_23_143738_create_customers_table',1),(11,'2026_01_23_144546_create_sales_table',1),(12,'2026_01_23_144548_create_sale_items_table',1),(13,'2026_01_23_144550_create_payments_table',1),(14,'2026_01_23_145722_create_sessions_table',1),(15,'2026_01_23_164534_remove_description_from_products_table',1),(16,'2026_01_23_170325_create_cash_sessions_table',1),(17,'2026_01_23_175356_add_role_to_users_table',1),(18,'2026_01_23_191719_create_cash_movements_table',1),(19,'2026_01_23_192620_add_opening_notes_to_cash_sessions_table',1),(20,'2026_01_24_143048_create_cash_registers_table',1),(21,'2026_01_24_143120_add_cash_register_id_to_cash_sessions_table',1),(22,'2026_01_24_145942_create_suppliers_table',1),(23,'2026_01_24_150002_add_supplier_id_to_products_table',1),(24,'2026_01_26_083121_alter_products_and_sale_items_for_weighables',1),(25,'2026_01_26_084647_add_manage_stock_to_products',1),(26,'2026_01_26_102112_add_location_id_to_cash_registers_table',1),(27,'2026_01_26_103554_simplify_system_and_add_register_to_sales',1),(28,'2026_01_26_104356_final_simplification_and_register_link',1),(29,'2026_01_26_160112_simplify_system_remove_locations',1),(30,'2026_01_26_190214_add_payment_condition_to_sales_table',1),(31,'2026_01_26_220524_create_customer_transactions_table',1),(32,'2026_01_26_225315_add_soft_deletes_to_users_table',1),(33,'2026_01_27_082605_add_deleted_at_to_customers_table',1),(34,'2026_02_14_121944_add_category_to_cash_movements_table',1),(35,'2026_02_19_220753_add_cheque_details_to_payments_table',1),(36,'2026_02_20_080734_add_salesperson_fields_to_users_and_sales',1),(37,'2026_02_20_120000_create_sellers_table_and_refactor_sales',1),(38,'2026_02_20_121500_add_code_and_shift_to_sellers_table',1),(39,'2026_02_20_130000_add_soft_deletes_to_sellers_table',1),(40,'2026_02_20_174834_add_discount_to_products_and_sales',1),(41,'2026_02_20_191837_add_index_to_sales_created_at',1),(42,'2026_02_21_211234_add_image_path_to_products_table',1),(43,'2026_02_21_211247_create_banners_table',1),(44,'2026_02_21_224743_add_image_path_to_categories_table',1),(45,'2026_02_23_201356_add_description_to_products_table',1),(46,'2026_02_24_131620_add_slug_to_products_table',1),(47,'2026_05_08_125820_create_web_orders_table',1),(48,'2026_05_08_130708_modify_web_orders_for_multi_items',1),(49,'2026_05_08_195832_create_purchases_tables',1),(50,'2026_05_08_202057_add_payment_method_to_web_orders_table',1),(51,'2026_05_08_203352_add_web_order_id_to_sales_table',1),(52,'2026_05_08_205732_create_expense_categories_table',1),(53,'2026_05_08_205740_create_expenses_table',1),(54,'2026_05_08_211309_create_product_variations_table',1),(55,'2026_05_08_211318_add_has_variations_to_products_table',1),(56,'2026_05_08_211322_add_product_variation_id_to_sale_items_table',1),(57,'2026_05_08_212138_add_product_variation_id_to_purchase_items_table',1),(58,'2026_05_12_073119_create_units_table',1),(59,'2026_05_12_073120_create_payment_methods_table',1),(60,'2026_05_12_075543_create_cash_movement_categories_table',1),(61,'2026_05_25_203600_create_license_payments_table',1),(62,'2026_05_25_203700_seed_license_settings',1),(63,'2026_05_25_211200_seed_grace_period_setting',1),(64,'2026_05_29_095727_create_branches_table',1),(65,'2026_05_29_095738_add_branch_id_to_related_tables',1),(66,'2026_05_29_100831_create_branch_stocks_table',1),(67,'2026_05_29_161225_change_method_to_string_in_payments_table',1),(68,'2026_06_01_000000_seed_theme_settings_to_store_settings_table',1),(69,'2026_06_01_010000_add_mobile_image_path_to_banners_table',1),(70,'2026_06_01_020000_seed_promo_banners_to_store_settings_table',1),(71,'2026_06_01_030000_seed_benefits_settings_to_store_settings_table',1),(72,'2026_06_01_040000_seed_electronic_products_and_categories',1),(73,'2026_06_01_050000_update_electronic_products_and_categories_images',1),(74,'2026_07_07_103412_create_menu_items_table',1),(75,'2026_07_08_143222_create_inventory_movements_table',1),(76,'2026_07_10_230645_create_pages_table',1),(77,'2026_08_24_234357_add_product_type_to_products_table',1),(78,'2026_08_24_234903_create_deboning_sessions_table',1),(79,'2026_08_24_234914_create_deboning_items_table',1),(80,'2026_08_25_000956_add_decant_fields_to_products_table',1),(81,'2026_08_25_141918_create_restaurant_tables_table',1),(82,'2026_08_25_141918_create_waiters_table',1),(83,'2026_08_25_141919_add_production_zone_to_products_table',1),(84,'2026_08_25_141920_add_comanda_fields_to_sale_items_table',1),(85,'2026_08_25_141921_add_restaurant_fields_to_sales_table',1),(86,'2026_08_26_100655_add_performance_composite_indexes_to_core_tables',1),(87,'2026_08_26_104151_create_permission_tables',1),(88,'2026_08_28_170839_add_foreign_currency_to_payments_table',2),(89,'2026_08_31_085624_add_reference_number_to_payments_table',2),(90,'2026_08_31_100918_create_notifications_table',2),(91,'2026_09_02_081556_create_licenses_table',3),(92,'2026_10_12_000001_add_sifen_fields_to_sales_table',4),(93,'2014_10_12_100000_create_password_resets_table',5),(94,'2026_08_18_215153_create_profesores_table',5),(95,'2026_09_01_215319_create_materias_table',5),(96,'2026_09_29_000000_ajustar_estado_y_fk_materias',5),(97,'2026_09_29_100000_create_horarios_table',5),(98,'2026_10_06_215709_create_alumnos_table',5),(99,'2026_10_26_000001_change_quantity_to_decimal_in_inventory_movements_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
INSERT INTO `payment_methods` VALUES ('a29c39c2-2db3-475f-b550-6280e2c05855','Tarjeta','card',1,'2026-08-28 11:04:36','2026-10-26 14:08:54'),('a29c39c2-2ec2-4d8c-82cd-f8b3682fe639','Transferencia','transfer',1,'2026-08-28 11:04:36','2026-10-26 14:08:57'),('a29c39c2-312a-48fb-9168-299d175db293','QR','qr',1,'2026-08-28 11:04:36','2026-10-26 14:08:52'),('a312fb7e-5f1e-489f-924d-07cfd53ffb4f','Efectivo','cash',1,'2026-10-26 12:54:01','2026-10-26 13:49:41');
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'efectivo',
  `amount` bigint NOT NULL,
  `foreign_amount` decimal(15,2) DEFAULT NULL,
  `foreign_currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cheque_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_sale_id_foreign` (`sale_id`),
  CONSTRAINT `payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variations`
--

DROP TABLE IF EXISTS `product_variations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variations` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_cost` int NOT NULL DEFAULT '0',
  `price_sale` int NOT NULL DEFAULT '0',
  `stock` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variations_sku_unique` (`sku`),
  KEY `product_variations_product_id_foreign` (`product_id`),
  CONSTRAINT `product_variations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variations`
--

LOCK TABLES `product_variations` WRITE;
/*!40000 ALTER TABLE `product_variations` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_variations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `is_raw_material` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price_cost` bigint NOT NULL DEFAULT '0',
  `price_sale` bigint NOT NULL DEFAULT '0',
  `discount_percentage` tinyint unsigned NOT NULL DEFAULT '0',
  `stock` decimal(10,3) NOT NULL DEFAULT '0.000',
  `min_stock` decimal(10,3) NOT NULL DEFAULT '5.000',
  `category_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `unit_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unit',
  `manage_stock` tinyint(1) NOT NULL DEFAULT '1',
  `has_variations` tinyint(1) NOT NULL DEFAULT '0',
  `parent_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fraction_multiplier` decimal(8,3) NOT NULL DEFAULT '1.000',
  `production_zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_sku_index` (`sku`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_supplier_id_foreign` (`supplier_id`),
  KEY `products_parent_id_foreign` (`parent_id`),
  KEY `idx_products_active_category` (`is_active`,`category_id`),
  KEY `idx_products_stock_alerts` (`is_active`,`manage_stock`,`stock`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES ('a3130414-ad5c-4c69-bc3d-4482b6a677af','standard',1,'res','res',NULL,'95737308',NULL,1000,1000,0,10.000,10.000,NULL,NULL,1,'2026-10-26 13:18:01','2026-10-26 13:18:59','kg',1,0,NULL,1.000,'none'),('a31304c2-49a7-4f8a-b698-034e7984350a','standard',0,'costilla','costilla',NULL,'74387367',NULL,1000,25000,0,0.000,0.000,NULL,NULL,1,'2026-10-26 13:19:55','2026-10-26 13:19:55','unit',0,0,'a3130414-ad5c-4c69-bc3d-4482b6a677af',1.000,'none');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profesores`
--

DROP TABLE IF EXISTS `profesores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profesores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `especialidad` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profesores_documento_unique` (`documento`),
  UNIQUE KEY `profesores_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesores`
--

LOCK TABLES `profesores` WRITE;
/*!40000 ALTER TABLE `profesores` DISABLE KEYS */;
/*!40000 ALTER TABLE `profesores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_items` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_variation_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `cost_price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_items_product_id_foreign` (`product_id`),
  KEY `purchase_items_product_variation_id_foreign` (`product_variation_id`),
  CONSTRAINT `purchase_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_product_variation_id_foreign` FOREIGN KEY (`product_variation_id`) REFERENCES `product_variations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
INSERT INTO `purchase_items` VALUES ('a313046c-a950-47ad-afc9-8d020066e3bc','a313046c-a6b0-4915-afb8-4775224dfbc9','a3130414-ad5c-4c69-bc3d-4482b6a677af',NULL,10.00,1000.00,10000.00,'2026-10-26 13:18:59','2026-10-26 13:18:59');
/*!40000 ALTER TABLE `purchase_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchases` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_date` date NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  KEY `purchases_user_id_foreign` (`user_id`),
  CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchases_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
INSERT INTO `purchases` VALUES ('a313046c-a6b0-4915-afb8-4775224dfbc9','a3130444-db5d-4316-9706-9bce200e6828','a312fe9a-c9f8-49b9-b464-06a923ab66c9','2026-10-26',10000.00,NULL,NULL,'completed','2026-10-26 13:18:59','2026-10-26 13:18:59');
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurant_tables`
--

DROP TABLE IF EXISTS `restaurant_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `restaurant_tables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('available','occupied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `current_sale_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurant_tables`
--

LOCK TABLES `restaurant_tables` WRITE;
/*!40000 ALTER TABLE `restaurant_tables` DISABLE KEYS */;
/*!40000 ALTER TABLE `restaurant_tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_items` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_variation_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `price_unit` bigint NOT NULL,
  `subtotal` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_sent_to_production` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sale_items_product_id_foreign` (`product_id`),
  KEY `sale_items_product_variation_id_foreign` (`product_variation_id`),
  KEY `idx_sale_items_sale_product` (`sale_id`,`product_id`),
  CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_items_product_variation_id_foreign` FOREIGN KEY (`product_variation_id`) REFERENCES `product_variations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web_order_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seller_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` bigint NOT NULL DEFAULT '0',
  `subtotal_before_discount` bigint unsigned NOT NULL DEFAULT '0',
  `discount_amount` bigint unsigned NOT NULL DEFAULT '0',
  `tax_10` bigint NOT NULL DEFAULT '0',
  `tax_5` bigint NOT NULL DEFAULT '0',
  `tax_exempt` bigint NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `payment_condition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contado',
  `sifen_cdc` varchar(44) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sifen_numero_factura` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sifen_estado` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ninguno',
  `sifen_qr_url` text COLLATE utf8mb4_unicode_ci,
  `sifen_xml` longtext COLLATE utf8mb4_unicode_ci,
  `sifen_respuesta` text COLLATE utf8mb4_unicode_ci,
  `sifen_tipo_documento` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `restaurant_table_id` bigint unsigned DEFAULT NULL,
  `waiter_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_customer_id_foreign` (`customer_id`),
  KEY `sales_seller_id_foreign` (`seller_id`),
  KEY `sales_created_at_index` (`created_at`),
  KEY `sales_web_order_id_foreign` (`web_order_id`),
  KEY `sales_restaurant_table_id_foreign` (`restaurant_table_id`),
  KEY `sales_waiter_id_foreign` (`waiter_id`),
  KEY `idx_sales_status_created` (`status`,`created_at`),
  KEY `idx_sales_branch_status_created` (`branch_id`,`status`,`created_at`),
  KEY `idx_sales_user_created` (`user_id`,`created_at`),
  KEY `sales_sifen_cdc_index` (`sifen_cdc`),
  KEY `sales_sifen_numero_factura_index` (`sifen_numero_factura`),
  CONSTRAINT `sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_restaurant_table_id_foreign` FOREIGN KEY (`restaurant_table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `sales_waiter_id_foreign` FOREIGN KEY (`waiter_id`) REFERENCES `waiters` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_web_order_id_foreign` FOREIGN KEY (`web_order_id`) REFERENCES `web_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sellers`
--

DROP TABLE IF EXISTS `sellers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sellers` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sellers`
--

LOCK TABLES `sellers` WRITE;
/*!40000 ALTER TABLE `sellers` DISABLE KEYS */;
/*!40000 ALTER TABLE `sellers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('IP15fyr3fB696Jarp7n7os1tmZh56kSJsImdzuo7','a312fd88-84ef-4cf7-8deb-c9356aebd67a','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiVmxUQnl0VXExbFlRZ3VLVXBLU1d0d1hJMnFncVVHMjFXZG5MdnB6OCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNToiaHR0cDovLzEyNy4wLjAuMTo4MDAxL3BvcyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDEvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7czozNjoiYTMxMmZkODgtODRlZi00Y2Y3LThkZWItYzkzNTZhZWJkNjdhIjtzOjIxOiJwYXNzd29yZF9oYXNoX3NhbmN0dW0iO3M6NjA6IiQyeSQxMiRyd3VmU01lUXd1ekd2RWZRT052L0pPdllDbDNZMUhONk8xUGZtVHBURnFxTjQ2YXJLRHl3dSI7fQ==',1793019726),('RuxDYgZZ1zOZjjlYQrPCFG8CwJ0Y5hnL93UAJYW9','a312fe9a-c9f8-49b9-b464-06a923ab66c9','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiSzZWR3psRkZsVWxYSEkxZ3l3c3dnbTdLdDNGa0RvckVXQWZYT2RGUCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDEvc2V0dGluZ3MvcGF5bWVudC1tZXRob2RzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6MzY6ImEzMTJmZTlhLWM5ZjgtNDliOS1iNDY0LTA2YTkyM2FiNjZjOSI7czoyMToicGFzc3dvcmRfaGFzaF9zYW5jdHVtIjtzOjYwOiIkMnkkMTIkZFpydnhqYWVFOFBaNFlOZ2k2TFNYdXFzcDZxR0tXOUdFRVFaeEUyVW0xaXRKVmJhSHdyT0siO30=',1793024317),('wxbPACtJmrjuqv27ALkZsyz150ZCDTVlGfkv5NvS','a29c39c1-b7b3-4c9a-b6e2-f20c6ee722ec','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiTW5Zc0Z5RzMzTU9rMVFuOExqZDM5RUtLeGg1RDdjY25YNWtrcDRmQyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vbG9jYWxob3N0OjgwMDEvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6MzY6ImEyOWMzOWMxLWI3YjMtNGM5YS1iNmUyLWYyMGM2ZWU3MjJlYyI7czoyMToicGFzc3dvcmRfaGFzaF9zYW5jdHVtIjtzOjYwOiIkMnkkMTIka2U4LkFFMGdVRzFRZmZxYmtGYVhpdXI1UW9udG9KMW9NUzM5SThKT0N4TXk3Y0tFNFhNdHUiO30=',1793024317);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_settings`
--

DROP TABLE IF EXISTS `store_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_settings` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_settings`
--

LOCK TABLES `store_settings` WRITE;
/*!40000 ALTER TABLE `store_settings` DISABLE KEYS */;
INSERT INTO `store_settings` VALUES ('22313c7e-b904-4191-bcd0-531aefdd4053','business_type','minimarket','Tipo de Negocio del Sistema','2026-08-28 11:04:34','2026-08-28 11:12:47'),('a29c39bc-e438-4024-8b87-b51423507139','license_enabled_at','2026-08-28','Fecha de habilitación inicial del sistema','2026-08-28 11:04:32','2026-08-28 11:04:32'),('a29c39bc-e7e0-4ae4-a391-44b88b2ce2ef','license_expires_at','2027-10-26','Fecha de vencimiento actual de la licencia del sistema','2026-08-28 11:04:32','2026-10-26 12:39:36'),('a29c39bc-ea55-4de1-a1f4-f937373dcc40','license_grace_period_used','0','Determina si el comercio ya utilizó el comodín de gracia por emergencia de 3 días en el ciclo actual','2026-08-28 11:04:32','2026-08-28 11:04:32'),('a29c39bd-b15b-4839-a615-a6d5efa41e97','top_bar_enabled','0','Habilitar o deshabilitar la barra superior en el e-commerce','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-b326-4009-951b-872d9e165ba9','top_bar_text','¿No querés pagar con tarjeta? ¡No hay problema! Elegí Pago en Puerta para pagar al momento de recibir tu pedido. <a href=\"#\" class=\"underline hover:opacity-80\">Más información sobre formas de pago.</a>','Texto que se muestra en la barra superior','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-b468-4640-8967-eb0d9d3d9961','top_bar_bg_color','#002f6c','Color de fondo en formato HEX para la barra superior','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-b5f3-432a-b998-447e8636da9c','top_bar_text_color','#ffffff','Color del texto en formato HEX para la barra superior','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-b765-4200-9106-277afe238ae1','shop_primary_color','#4f46e5','Color primario de marca para el e-commerce','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-b8a6-4fd5-a1a8-c45f0be2ce33','shop_primary_color_hover','#4338ca','Color de marca primario para el estado hover','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-c20e-4322-9cf7-b4f4c06af74f','promo_banner_1_path','','Ruta de la imagen del banner promocional izquierdo (ancho)','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-c3eb-415c-94f2-265a5bf7ba8f','promo_banner_1_link','#','Enlace de redirección del banner promocional izquierdo','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-c58c-4e1a-b5a6-63fc14db8fe5','promo_banner_2_path','','Ruta de la imagen del banner promocional derecho (angosto)','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-c736-4113-a233-6b56ad06324d','promo_banner_2_link','#','Enlace de redirección del banner promocional derecho','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-ca13-48c3-ae6f-b95ee1d629e1','benefits_enabled','1','Habilitar barra de beneficios de compra antes del footer','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-cb9e-4f43-b299-37c2e245a30f','benefit_1_title','Compra segura','Título del beneficio 1','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-ccd0-466d-aba8-418c2f7398a7','benefit_1_subtitle','Pago protegido con encriptación SSL','Subtítulo del beneficio 1','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-cdeb-4f99-aa27-51e2fbf02f01','benefit_2_title','Envío a todo el país','Título del beneficio 2','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-cf47-4376-af88-bd6cacb51bf8','benefit_2_subtitle','Despachamos a todos los departamentos','Subtítulo del beneficio 2','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-d162-4075-b319-ca5ce979cd99','benefit_3_title','Cambios fáciles','Título del beneficio 3','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-d328-4d05-a476-d5a1b1374e39','benefit_3_subtitle','15 días para cambiar','Subtítulo del beneficio 3','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-d44b-4a66-bd42-d88b2b20bb79','benefit_4_title','Atención al cliente','Título del beneficio 4','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39bd-d544-421b-ab34-caa6f7761de2','benefit_4_subtitle','Soporte por WhatsApp','Subtítulo del beneficio 4','2026-08-28 11:04:33','2026-08-28 11:04:33'),('a29c39c2-1fb5-4388-b7be-63f637fd4514','store_name','ZonaPOS Store','Nombre del comercio','2026-08-28 11:04:36','2026-08-28 11:04:36'),('a29c39c2-2114-4649-8dcb-0166d04fae51','store_ruc','8000000-1','RUC del comercio','2026-08-28 11:04:36','2026-08-28 11:04:36'),('a29c39c2-2308-4d1c-b327-9c3569916390','store_address','Av. Eusebio Ayala km 5','Dirección del comercio','2026-08-28 11:04:36','2026-08-28 11:04:36'),('a29c39c2-2474-43b5-b113-116c98f73540','store_phone','0981 123 456','Teléfono de contacto','2026-08-28 11:04:36','2026-08-28 11:04:36'),('a29c39c2-261a-47cc-ad94-6795b4229e49','ticket_footer','¡Gracias por su compra!','Mensaje al pie del ticket','2026-08-28 11:04:36','2026-08-28 11:04:36'),('a29c39c2-2773-4e83-851e-1ad25c5f0bfa','shop_whatsapp','981123456','WhatsApp para pedidos e-commerce','2026-08-28 11:04:36','2026-08-28 11:04:36'),('a2f6dddd-523f-47e1-9926-0f8f213a8258','license_max_users','3','Límite máximo de usuarios permitidos','2026-10-12 13:27:59','2026-10-12 13:27:59'),('a2f6dddd-dda5-43f1-a132-7ddacab33399','license_max_cash_registers','1','Límite máximo de cajas registradoras permitidas','2026-10-12 13:27:59','2026-10-12 13:27:59'),('a2f6dddd-df64-43eb-9af7-8937cb1e442c','license_max_branches','1','Límite máximo de sucursales permitidas','2026-10-12 13:27:59','2026-10-12 13:27:59'),('a2f6dddd-e1d9-4d43-b1fe-363f8f6f81af','license_tier_name','Básico','Nombre del plan/tier contratado','2026-10-12 13:27:59','2026-10-12 13:27:59'),('a2f6dddd-e364-408a-86af-54cfdab54867','license_ecommerce_enabled','0','Habilitar Catálogo Online / E-commerce','2026-10-12 13:27:59','2026-10-12 13:27:59'),('a2f6fb61-f59e-494c-9506-7a7dd3e5c6f4','sifen_enabled','1',NULL,'2026-10-12 14:50:31','2026-10-12 19:53:52'),('a2f6fb62-60a0-4200-baf5-4930124eeb49','sifen_environment','test',NULL,'2026-10-12 14:50:31','2026-10-12 14:50:31'),('a2f6fb62-d1d1-4367-9502-c367aac5e7fc','sifen_timbrado','12345678',NULL,'2026-10-12 14:50:32','2026-10-12 14:50:32'),('a2f6fb63-9bf4-4327-a0c7-52892095eb14','sifen_timbrado_fecha_inicio','2024-01-01',NULL,'2026-10-12 14:50:32','2026-10-12 14:50:32'),('a2f6fb64-0fed-403a-ab27-393e8c13c88f','sifen_establecimiento','001',NULL,'2026-10-12 14:50:32','2026-10-12 14:50:32'),('a2f6fb64-da7e-434c-9dd1-c49673b2e2ac','sifen_punto_expedicion','001',NULL,'2026-10-12 14:50:33','2026-10-12 14:50:33'),('a2f6fb65-dfe6-4571-a2eb-f6a2378c679f','sifen_serie','AA',NULL,'2026-10-12 14:50:34','2026-10-12 14:50:34'),('a2f6fb66-540e-4280-992b-0da0cda24623','sifen_emisor_ruc','44444401',NULL,'2026-10-12 14:50:34','2026-10-12 14:50:34'),('a2f6fb66-ad83-42a7-9a9e-c900376f8053','sifen_emisor_dv','7',NULL,'2026-10-12 14:50:34','2026-10-12 14:50:34'),('a2f6fb67-1f0c-4c40-afc9-3bbd1d9b1a41','sifen_emisor_tipo_contribuyente','2',NULL,'2026-10-12 14:50:34','2026-10-12 14:50:34'),('a2f6fb67-c43e-44d2-bb34-a2cf6b231aee','sifen_emisor_razon_social','DOCUMENTO ELECTRÓNICO SIN VALOR COMERCIAL NI FISCAL - GENERADO EN AMBIENTE DE PRUEBA',NULL,'2026-10-12 14:50:35','2026-10-12 14:50:35'),('a2f6fb68-10c3-4a17-bbc5-0418ef2e0c0d','sifen_emisor_nombre_fantasia','ZonaShop Test SIFEN',NULL,'2026-10-12 14:50:35','2026-10-12 14:50:35'),('a2f6fb68-6731-4d33-a3e0-c4d652b3a992','sifen_emisor_direccion','Avda. Mariscal López y Yegros',NULL,'2026-10-12 14:50:35','2026-10-12 14:50:35'),('a2f6fb68-f386-4bf6-a887-e8c2cd5b2e43','sifen_emisor_numero_casa','0',NULL,'2026-10-12 14:50:36','2026-10-12 14:50:36'),('a2f6fb69-af6e-47d8-aa93-414fe232a4c1','sifen_emisor_departamento','11',NULL,'2026-10-12 14:50:36','2026-10-12 14:50:36'),('a2f6fb6a-c81a-4397-9cc7-8be9daf9d605','sifen_emisor_departamento_desc','Capital',NULL,'2026-10-12 14:50:37','2026-10-12 14:50:37'),('a2f6fb6d-7791-4ddf-ae74-b8aeb58a802e','sifen_emisor_distrito','1',NULL,'2026-10-12 14:50:39','2026-10-12 14:50:39'),('a2f6fb6f-5479-4e18-9de6-0487b8869793','sifen_emisor_distrito_desc','Asunción',NULL,'2026-10-12 14:50:40','2026-10-12 14:50:40'),('a2f6fb6f-f3ea-4219-9a3f-a22b55e8882a','sifen_emisor_ciudad','1',NULL,'2026-10-12 14:50:40','2026-10-12 14:50:40'),('a2f6fb6f-f72c-4d71-becd-d882e36c27b9','sifen_emisor_ciudad_desc','Asunción',NULL,'2026-10-12 14:50:40','2026-10-12 14:50:40'),('a2f6fb6f-f9ba-427f-a01b-12dd2dc58b4b','sifen_emisor_telefono','021123456',NULL,'2026-10-12 14:50:40','2026-10-12 14:50:40'),('a2f6fb6f-fcda-44aa-8e53-851d1498ea25','sifen_emisor_email','facturacion@zonapos.com',NULL,'2026-10-12 14:50:40','2026-10-12 14:50:40'),('a2f6fb6f-fef9-4999-8450-a081634e00e5','sifen_emisor_actividad_codigo','47111',NULL,'2026-10-12 14:50:40','2026-10-12 14:50:40'),('a2f6fb70-00f7-44e2-b3b8-811f0c031392','sifen_emisor_actividad_desc','Comercio al por menor en comercios no especializados',NULL,'2026-10-12 14:50:40','2026-10-12 14:50:40'),('a2f6fb70-0428-4f79-b6c7-595c70cad7dc','sifen_id_csc','0001',NULL,'2026-10-12 14:50:40','2026-10-12 14:50:40'),('a2f6fb70-0588-44b4-b373-c98bad3038ae','sifen_csc','ABCD0000000000000000000000000000',NULL,'2026-10-12 14:50:40','2026-10-12 14:50:40'),('a2f6fb70-069a-4596-99c1-2c01d8a959ff','sifen_auto_print_kude','1',NULL,'2026-10-12 14:50:40','2026-10-12 14:50:40'),('a2f6ff75-aa24-493f-9200-51904c3a8cb8','sifen_ultimo_numero','3',NULL,'2026-10-12 15:01:55','2026-10-12 19:59:30'),('a2f72e6e-92a3-40a9-bbbf-f5438cab647f','sifen_master_enabled','0','Habilitar Módulo de Facturación Electrónica SIFEN por Master','2026-10-12 17:13:16','2026-10-26 11:51:39');
/*!40000 ALTER TABLE `store_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES ('a3130444-db5d-4316-9706-9bce200e6828','proveedor de carne','','','','','',1,'2026-10-26 13:18:33','2026-10-26 13:18:33');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `units` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` VALUES ('a29c450b-9cc4-4522-812c-b8b1dd0e7efb','kg','',1,'2026-08-28 11:36:09','2026-08-28 11:36:09'),('a312fb7e-6895-4e1f-b6c1-f683638d8e17','UNIDAD',NULL,1,'2026-10-26 12:54:01','2026-10-26 12:54:01');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cajero',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint unsigned DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `branch_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_branch_id_foreign` (`branch_id`),
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('a29c39c1-b7b3-4c9a-b6e2-f20c6ee722ec','Administrador','admin@zonapos.com','admin',NULL,'$2y$12$ke8.AE0gUG1QffqbkFaXiur5QontoJ1oMS39I8JOCxMy7cKE4XMtu',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-28 11:04:35','2026-10-26 13:02:42',NULL,'a29c3eb4-6e54-48e6-b4fa-6acd0ec83bd6'),('a29c39c2-1c89-4196-8451-24ac8e1eeccf','Desarrollador Sote','soteweb@gmail.com','developer',NULL,'$2y$12$6uNVrha0VsfFO/IbNhPHwu/OekARCm1dFdCyCYt2Ox1GgK/1UL962',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-28 11:04:36','2026-10-26 13:02:43',NULL,'a29c3eb4-6e54-48e6-b4fa-6acd0ec83bd6'),('a312fe9a-64bb-4507-8574-57ce4a398fd3','Admin General','admin@admin.com','admin',NULL,'$2y$12$S/k8H6pGrqs6AcdAuvJR6.QvNvvX2tKk/o8wIhDM8/sc2Z8lZcHyG',NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-26 13:02:42','2026-10-26 13:02:42',NULL,'a29c3eb4-6e54-48e6-b4fa-6acd0ec83bd6'),('a312fe9a-c9f8-49b9-b464-06a923ab66c9','Desarrollador Master','dev@zonapos.com','developer',NULL,'$2y$12$dZrvxjaeE8PZ4YNgi6LSXuqsp6qGKW9GEEQZxE2Um1itJVbaHwrOK',NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-26 13:02:42','2026-10-26 13:02:42',NULL,'a29c3eb4-6e54-48e6-b4fa-6acd0ec83bd6');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `waiters`
--

DROP TABLE IF EXISTS `waiters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `waiters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `waiters`
--

LOCK TABLES `waiters` WRITE;
/*!40000 ALTER TABLE `waiters` DISABLE KEYS */;
/*!40000 ALTER TABLE `waiters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_orders`
--

DROP TABLE IF EXISTS `web_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `web_orders` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:guid)',
  `total` int NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente WhatsApp',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `items_json` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `web_orders_product_id_foreign` (`product_id`),
  CONSTRAINT `web_orders_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_orders`
--

LOCK TABLES `web_orders` WRITE;
/*!40000 ALTER TABLE `web_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `web_orders` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-26 12:17:23
