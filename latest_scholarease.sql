/*
SQLyog Community v13.2.0 (64 bit)
MySQL - 8.0.30 : Database - scholarease_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`scholarease_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `scholarease_db`;

/*Table structure for table `applications` */

DROP TABLE IF EXISTS `applications`;

CREATE TABLE `applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `scholarship_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `applications_user_id_scholarship_id_unique` (`user_id`,`scholarship_id`),
  KEY `applications_scholarship_id_foreign` (`scholarship_id`),
  CONSTRAINT `applications_scholarship_id_foreign` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `applications` */

/*Table structure for table `bookmarks` */

DROP TABLE IF EXISTS `bookmarks`;

CREATE TABLE `bookmarks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `scholarship_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `notified_at` datetime DEFAULT NULL,
  `notification_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notification_error` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_scholarship` (`user_id`,`scholarship_id`),
  KEY `fk_bookmarks_scholarship` (`scholarship_id`),
  CONSTRAINT `fk_bookmarks_scholarship` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bookmarks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `bookmarks` */

insert  into `bookmarks`(`id`,`user_id`,`scholarship_id`,`created_at`,`notified_at`,`notification_status`,`notification_error`) values 
(18,28,47,'2026-05-30 17:51:52',NULL,'pending',NULL),
(19,28,44,'2026-05-30 17:52:19',NULL,'pending',NULL);

/*Table structure for table `category_definitions` */

DROP TABLE IF EXISTS `category_definitions`;

CREATE TABLE `category_definitions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_type` enum('academic','income','study_path') COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `min_value` int DEFAULT NULL,
  `max_value` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_category` (`category_type`,`code`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `category_definitions` */

insert  into `category_definitions`(`id`,`category_type`,`code`,`label`,`description`,`min_value`,`max_value`) values 
(1,'academic','A1','0-3 As','Low academic achievement: 0 to 3 As in SPM',0,3),
(2,'academic','A2','4-6 As','Moderate academic achievement: 4 to 6 As in SPM',4,6),
(3,'academic','A3','7-9 As','Good academic achievement: 7 to 9 As in SPM',7,9),
(4,'academic','A4','10-12 As','Excellent academic achievement: 10 to 12+ As in SPM',10,12),
(5,'income','B1','B40','Bottom 40% household income (< RM4,850/month)',NULL,NULL),
(6,'income','B3','M40','Middle 40% household income (RM4,850 - RM10,970/month)',NULL,NULL),
(7,'income','B4','T20','Top 20% household income (> RM10,970/month)',NULL,NULL),
(8,'study_path','C1','Pre-University','Foundation, Matriculation, A-Level, STPM',NULL,NULL),
(9,'study_path','C2','Diploma','Diploma programs',NULL,NULL),
(10,'study_path','C3','Degree','Undergraduate degree programs',NULL,NULL),
(11,'study_path','C4','TVET','Technical and Vocational Education and Training',NULL,NULL);

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `feedback` */

DROP TABLE IF EXISTS `feedback`;

CREATE TABLE `feedback` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `rating` int NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_user_id_foreign` (`user_id`),
  CONSTRAINT `feedback_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `feedback` */

insert  into `feedback`(`id`,`user_id`,`rating`,`comment`,`approved`,`created_at`,`updated_at`) values 
(1,28,5,'best!',1,'2026-05-31 17:46:01','2026-05-31 18:03:00'),
(2,1,5,'ScholarEase helped me find the perfect scholarship for my studies! The matching system is very accurate.',1,'2026-05-31 17:47:27','2026-05-31 17:47:27'),
(4,1,5,'The OCR feature saved me so much time. No need to manually enter my SPM results.',1,'2026-05-31 17:47:28','2026-05-31 17:47:28');

/*Table structure for table `locations` */

DROP TABLE IF EXISTS `locations`;

CREATE TABLE `locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('security','clinic','emergency') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `locations` */

insert  into `locations`(`id`,`name`,`type`,`latitude`,`longitude`,`created_at`,`updated_at`) values 
(1,'Field','security',20.12345,67.14532,'2026-02-03 18:26:49','2026-02-03 18:26:49');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2014_10_12_100000_create_password_resets_table',1),
(4,'2019_08_19_000000_create_failed_jobs_table',1),
(5,'2019_12_14_000001_create_personal_access_tokens_table',1),
(6,'2026_01_23_150720_create_locations_table',1),
(7,'2026_01_23_191838_create_reports_table',1),
(8,'2026_01_23_211706_create_scholarships_table',1),
(9,'2026_01_23_211715_create_user_profiles_table',1),
(10,'2026_01_23_211729_create_bookmarks_table',1),
(11,'2026_01_23_211739_create_scraping_logs_table',1),
(12,'2026_01_24_070331_create_applications_table',2),
(13,'2026_01_24_193802_add_missing_columns_to_scraping_logs_table',3),
(14,'2026_01_24_213251_add_is_official_to_scholarships_table_fixed',4),
(15,'2026_01_25_210506_make_deadline_nullable_in_scholarships',4),
(16,'2026_01_27_190812_add_biasiswa_level_to_scholarships',5),
(17,'2026_01_27_192950_create_scholarship_rules_table',5),
(18,'2026_01_27_194343_update_scholarship_rules_for_ranges',5),
(19,'2026_01_27_195159_make_keyword_nullable_in_scholarship_rules',5),
(20,'2026_01_30_100001_add_scholarship_level_to_scholarships',5),
(21,'2026_01_31_160111_create_notifications_table',1),
(22,'2026_04_19_150835_add_role_to_users_table',6),
(23,'2026_04_19_155509_fix_role_column_users',7),
(24,'2026_04_27_152156_add_notification_status_to_bookmarks_table',8),
(25,'2026_05_31_165719_create_feedback_table',9);

/*Table structure for table `notifications` */

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `notifications` */

insert  into `notifications`(`id`,`type`,`notifiable_type`,`notifiable_id`,`data`,`read_at`,`created_at`,`updated_at`) values 
('2b48ed01-9a5c-4591-adca-50875c3f0cb2','App\\Notifications\\ScholarshipDeadlineReminder','App\\Models\\User',1,'{\"title\":\"Scholarship Deadline Reminder\",\"message\":\"TESTT deadline in 5 days!\",\"action_url\":null,\"scholarship_id\":130}',NULL,'2026-01-31 18:31:25','2026-01-31 18:31:25'),
('d8a5ad82-321d-4293-9d59-b7ad6d9e7311','App\\Notifications\\ScholarshipDeadlineReminder','App\\Models\\User',2,'{\"title\":\"Scholarship Deadline Reminder\",\"message\":\"TESTT deadline in 5 days!\",\"action_url\":null,\"scholarship_id\":130}',NULL,'2026-01-31 20:20:28','2026-01-31 20:20:28'),
('da3e8077-90b9-44e3-a058-c8b0b4dcabba','App\\Notifications\\ScholarshipDeadlineReminder','App\\Models\\User',1,'{\"title\":\"Scholarship Deadline Reminder\",\"message\":\"Bank Negara Kijang Pre-University Scholarship deadline in -214 days!\",\"action_url\":\"https:\\/\\/unienrol.com\\/scholarships\\/external\\/malaysian\\/detail\\/bank-negara-kijang-pre-university-scholarship\",\"scholarship_id\":102}',NULL,'2026-01-31 17:35:25','2026-01-31 17:35:25');

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `password_resets` */

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_resets` */

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

insert  into `personal_access_tokens`(`id`,`tokenable_type`,`tokenable_id`,`name`,`token`,`abilities`,`last_used_at`,`expires_at`,`created_at`,`updated_at`) values 
(1,'App\\Models\\User',12,'auth_token','0a8d4f3d5f7d7007fad2b7f9f56369426f1162ab8aaf05e99f9475f0217cc710','[\"*\"]',NULL,NULL,'2026-05-02 16:53:09','2026-05-02 16:53:09'),
(2,'App\\Models\\User',12,'auth_token','7265399a9ee7f1e353c2e618701ecc385c063617f92bea092a7f70b2b6237f67','[\"*\"]',NULL,NULL,'2026-05-02 16:53:12','2026-05-02 16:53:12'),
(3,'App\\Models\\User',12,'auth_token','e31d5b545d89ee52ad870c1391a2aaa4a1d41453c61445c7baf7dd88f0048aa3','[\"*\"]','2026-05-02 16:58:33',NULL,'2026-05-02 16:58:25','2026-05-02 16:58:33'),
(4,'App\\Models\\User',12,'auth_token','4e47900dc60122a43c2c12dbab870f0ecbb86c9d9887578122a6f8745dafd2b2','[\"*\"]','2026-05-02 17:02:53',NULL,'2026-05-02 17:02:49','2026-05-02 17:02:53'),
(5,'App\\Models\\User',12,'auth_token','4a5f2310b7656b2f54f83164ae4bf40a81659ee609dcae42f9c69e92dc625d83','[\"*\"]','2026-05-02 17:05:17',NULL,'2026-05-02 17:05:02','2026-05-02 17:05:17'),
(6,'App\\Models\\User',12,'auth_token','3b93545dcfbb1fd5ea4cf72dee15531cb368184117b0f5ae6639bf85bb84e5e3','[\"*\"]','2026-05-02 17:47:23',NULL,'2026-05-02 17:13:43','2026-05-02 17:47:23'),
(7,'App\\Models\\User',12,'auth_token','ac615f4dd5a289918345b72df4ad6b5b92a29a9848bd256226257258d4d710d5','[\"*\"]',NULL,NULL,'2026-05-02 18:32:34','2026-05-02 18:32:34'),
(8,'App\\Models\\User',12,'auth_token','0830926a949cd0d94af8fda75d929d4569962ac207b59ccd34111a58ac930fb7','[\"*\"]','2026-05-02 18:46:21',NULL,'2026-05-02 18:44:52','2026-05-02 18:46:21'),
(9,'App\\Models\\User',12,'auth_token','f776d6838f462372dbbe2ea84817a6242dd6e00982ca3c32d258cd85cfa03910','[\"*\"]','2026-05-02 18:55:50',NULL,'2026-05-02 18:54:19','2026-05-02 18:55:50'),
(10,'App\\Models\\User',12,'auth_token','124e5c6c3b687fd68799c41a238609f6a14f34c6fdef7a3e08c5b7a40b50d621','[\"*\"]','2026-05-02 19:09:59',NULL,'2026-05-02 19:06:20','2026-05-02 19:09:59'),
(11,'App\\Models\\User',12,'auth_token','0ec83c36e02ff115c7a2733556042d58f296e78e9cc0e2c5a2dee546553c3d7a','[\"*\"]',NULL,NULL,'2026-05-02 19:12:51','2026-05-02 19:12:51'),
(12,'App\\Models\\User',12,'auth_token','bd34a33f7420e74e355189800fc46b9032850411c3c00a1631d260d68268e366','[\"*\"]','2026-05-02 19:21:40',NULL,'2026-05-02 19:20:27','2026-05-02 19:21:40'),
(13,'App\\Models\\User',12,'auth_token','da94a17de875a91c9f8e9df37eb1d9a332df28305dc8bc69dfad478cfebc77ae','[\"*\"]',NULL,NULL,'2026-05-02 19:44:33','2026-05-02 19:44:33'),
(14,'App\\Models\\User',12,'auth_token','8abd9cf2e946614787902f1bf440586e2c48a6be5b369eec5b252371ce165ff3','[\"*\"]','2026-05-02 19:52:16',NULL,'2026-05-02 19:49:56','2026-05-02 19:52:16'),
(15,'App\\Models\\User',12,'auth_token','66b5cf8a6ac48789244cca8ffb8b9c970876a05846d22f3baa5d2cdf92126e24','[\"*\"]',NULL,NULL,'2026-05-03 07:04:28','2026-05-03 07:04:28'),
(16,'App\\Models\\User',12,'auth_token','3d2c1cd731f3abd6c5890cfca27d4427412e56b81a2df4181bae5595f47e78b8','[\"*\"]','2026-05-03 07:09:50',NULL,'2026-05-03 07:07:31','2026-05-03 07:09:50'),
(17,'App\\Models\\User',12,'auth_token','ba5864b14b8b4900edf20c5b3f85bd75cfb2871aae853d64030f9ea18f344653','[\"*\"]','2026-05-03 07:11:55',NULL,'2026-05-03 07:11:26','2026-05-03 07:11:55'),
(18,'App\\Models\\User',12,'auth_token','d2fcd965c6b015c70ad83fd978abb5ede513a79977c4d47770dbd1e9cb08e6fb','[\"*\"]','2026-05-03 07:25:23',NULL,'2026-05-03 07:24:43','2026-05-03 07:25:23'),
(19,'App\\Models\\User',12,'auth_token','ad10bc0762cd1e881863fef162744e547de1e2fb94154c98b4aafbed201aeaa5','[\"*\"]','2026-05-03 07:30:48',NULL,'2026-05-03 07:29:46','2026-05-03 07:30:48'),
(20,'App\\Models\\User',12,'auth_token','def9ea044898443d3934fb371d8f070e0961979509cdc8d4e4230f84c3b39639','[\"*\"]',NULL,NULL,'2026-05-03 07:38:22','2026-05-03 07:38:22'),
(21,'App\\Models\\User',12,'auth_token','ddd97cccf7e35f165b9b67079da17f43f715a0eb1e160867f8739eeeedab5c7e','[\"*\"]','2026-05-03 07:38:34',NULL,'2026-05-03 07:38:24','2026-05-03 07:38:34'),
(22,'App\\Models\\User',12,'auth_token','4469c49bd5f31140c72be1d3956850a1a7da614c9d894d868365b225aabd0e44','[\"*\"]',NULL,NULL,'2026-05-03 08:39:25','2026-05-03 08:39:25'),
(23,'App\\Models\\User',12,'auth_token','4f14938e4f17b6afb3b4cd8e0f71d4d9403db54c6a1a705cde3db535a17266ed','[\"*\"]',NULL,NULL,'2026-05-03 08:39:28','2026-05-03 08:39:28'),
(24,'App\\Models\\User',12,'auth_token','76b5f07d7e0941c63c77ac2569a9f6e5df7570525332361a81c604c359de2557','[\"*\"]','2026-05-03 08:39:39',NULL,'2026-05-03 08:39:28','2026-05-03 08:39:39'),
(25,'App\\Models\\User',12,'auth_token','df6dc51bbbe8e6797fbc5fe06d62f29f9237bb6599554d4eaed0bb691daed08b','[\"*\"]','2026-05-03 08:47:00',NULL,'2026-05-03 08:46:05','2026-05-03 08:47:00'),
(26,'App\\Models\\User',12,'auth_token','40e00af2ab71ab781173e1ac1d0b2bab671d6bb7a5615bcfdbb00b86fba4bb4d','[\"*\"]',NULL,NULL,'2026-05-03 08:51:43','2026-05-03 08:51:43'),
(27,'App\\Models\\User',12,'auth_token','38fc62002bd5ee62bae1e8722b27d25781921378a3850be9ff004cb8c1007718','[\"*\"]','2026-05-03 08:52:58',NULL,'2026-05-03 08:51:44','2026-05-03 08:52:58'),
(28,'App\\Models\\User',12,'auth_token','76e6a577e645964a92237ccb2b287faa54febff2ad563357d219f502815477d3','[\"*\"]',NULL,NULL,'2026-05-03 08:56:57','2026-05-03 08:56:57'),
(29,'App\\Models\\User',12,'auth_token','b71d300cb6cf8460e42dde293375b6e7229b79a2120c2547a0d2e3aaf04aaec0','[\"*\"]','2026-05-03 09:04:01',NULL,'2026-05-03 09:03:47','2026-05-03 09:04:01'),
(30,'App\\Models\\User',12,'auth_token','a35171c1815aaa80c47318e638187659890bfb9120c7ca736a3e64188a96a7b6','[\"*\"]',NULL,NULL,'2026-05-03 09:16:24','2026-05-03 09:16:24'),
(31,'App\\Models\\User',12,'auth_token','a2ff8d3bf6b2e630751e7c3f6c3eeb908c9f3aaa7a14a1055aed97754b2ec093','[\"*\"]','2026-05-03 09:24:06',NULL,'2026-05-03 09:21:36','2026-05-03 09:24:06'),
(32,'App\\Models\\User',12,'auth_token','464774f66e2f2269e2b0335ff71b40899ab5e923c0fe631eb95aa40819c946a1','[\"*\"]',NULL,NULL,'2026-05-03 13:38:42','2026-05-03 13:38:42'),
(33,'App\\Models\\User',12,'auth_token','7fd7527d6c38d7718fc6606befaf473f7e9c86f74f7539df92aed3fb6f1f8a3f','[\"*\"]','2026-05-03 13:39:38',NULL,'2026-05-03 13:38:43','2026-05-03 13:39:38'),
(34,'App\\Models\\User',12,'auth_token','a8a34fe0286c0ee95cd08ef17b93a1d6a572ce8636989f38e3793fe6f7203dae','[\"*\"]','2026-05-03 15:38:34',NULL,'2026-05-03 15:33:49','2026-05-03 15:38:34'),
(35,'App\\Models\\User',12,'auth_token','bbc7ea6740b50db96158934ac06e4c12beaf0388b509d4e115a441fdda5ae86b','[\"*\"]','2026-05-03 15:41:00',NULL,'2026-05-03 15:40:20','2026-05-03 15:41:00'),
(36,'App\\Models\\User',12,'auth_token','7c5a1ab83bd73e10b9e8c32d3d608144a348c543d3b82c0875b37882a66a3ce3','[\"*\"]','2026-05-03 15:51:10',NULL,'2026-05-03 15:50:11','2026-05-03 15:51:10'),
(37,'App\\Models\\User',12,'auth_token','e8926094fa300520a29e818017b63aeee57f2df6a5ab6d785d5f3996ae8a2dc1','[\"*\"]','2026-05-03 16:37:35',NULL,'2026-05-03 16:37:13','2026-05-03 16:37:35'),
(38,'App\\Models\\User',12,'auth_token','62caa1ef974cc4703aad0ddef4bce5c967f351adf75dd08d835e69fb0576c61e','[\"*\"]',NULL,NULL,'2026-05-03 16:39:28','2026-05-03 16:39:28'),
(39,'App\\Models\\User',12,'auth_token','c6d0f767021e193a5117233941b0b48b94c175cea022518f3af12cbde8bb66b6','[\"*\"]',NULL,NULL,'2026-05-03 16:39:30','2026-05-03 16:39:30'),
(40,'App\\Models\\User',26,'auth_token','4247609e3631ef6163589ac927c7a190aeae29cd98434d256d7f3ec91e119518','[\"*\"]','2026-05-03 16:44:45',NULL,'2026-05-03 16:44:19','2026-05-03 16:44:45'),
(41,'App\\Models\\User',12,'auth_token','3770245337f6c01bbd64b47589147e9b9d3c7ace9ce2774d8cca8ea7d2cdaa6f','[\"*\"]','2026-05-03 16:57:05',NULL,'2026-05-03 16:56:35','2026-05-03 16:57:05'),
(42,'App\\Models\\User',12,'auth_token','e4c6fc100fd7fc967b6679925b65655970c696af5f9f46c1a5655ac95ff38138','[\"*\"]','2026-05-10 08:45:10',NULL,'2026-05-10 08:44:35','2026-05-10 08:45:10'),
(43,'App\\Models\\User',12,'auth_token','1999e632d3389c415ff248f97c15650e4d83753c37debf2982ff7e1905340ae8','[\"*\"]','2026-05-10 12:35:20',NULL,'2026-05-10 12:35:07','2026-05-10 12:35:20'),
(44,'App\\Models\\User',12,'auth_token','1bfefa25638f7fe123bf81b371717eff46216e3cab6f95a73da6a4c323258cdb','[\"*\"]','2026-05-10 15:18:32',NULL,'2026-05-10 15:15:20','2026-05-10 15:18:32'),
(45,'App\\Models\\User',12,'auth_token','6417bc9689c22feafab0642f133c0e60aa8b51aaf42d13a060ccc5437c5baa31','[\"*\"]','2026-05-10 15:22:27',NULL,'2026-05-10 15:20:55','2026-05-10 15:22:27'),
(46,'App\\Models\\User',12,'auth_token','dafb8388aa24d4fbb6776d14332170537b3727776f67898d7bbb5e38aeb3e582','[\"*\"]',NULL,NULL,'2026-05-10 15:20:57','2026-05-10 15:20:57'),
(47,'App\\Models\\User',12,'auth_token','05b1c8f3f2496cc0d85aaaeeb57f8a30de4ad8ef0430c4f24f8d93fcb9929567','[\"*\"]','2026-05-10 15:27:30',NULL,'2026-05-10 15:26:33','2026-05-10 15:27:30'),
(48,'App\\Models\\User',12,'auth_token','c5b61b839d7fe3d11a91072d6fdb4baf95946349672a7c6ae301cbf31a07fc3f','[\"*\"]','2026-05-10 15:36:26',NULL,'2026-05-10 15:36:15','2026-05-10 15:36:26'),
(49,'App\\Models\\User',12,'auth_token','2b7d412a59fea21e9569b6d2760c2ac74e8d7b48467af2ba16f8e1258f05b345','[\"*\"]',NULL,NULL,'2026-05-10 15:36:16','2026-05-10 15:36:16'),
(50,'App\\Models\\User',12,'auth_token','93234de40ce252d53d2ca5382e40a85110de90fdf7af959e6af6f24cf0267a3e','[\"*\"]','2026-05-10 15:41:17',NULL,'2026-05-10 15:41:05','2026-05-10 15:41:17'),
(51,'App\\Models\\User',12,'auth_token','c8da32f837e2188de25fef9929390450271ccaef7ab9d9225164bf3c1b4342d3','[\"*\"]',NULL,NULL,'2026-05-10 15:41:08','2026-05-10 15:41:08'),
(52,'App\\Models\\User',12,'auth_token','084c6f623e71f95a8784e1d83b5f458f696d8d728c15f2928aa3799996c76e2f','[\"*\"]','2026-05-10 15:48:20',NULL,'2026-05-10 15:47:22','2026-05-10 15:48:20'),
(53,'App\\Models\\User',12,'auth_token','0d8f8ae7e53096d9814b0db3543c2b3e5bb8bf287e43a32e18d43396a2c07c13','[\"*\"]','2026-05-10 16:03:04',NULL,'2026-05-10 16:02:29','2026-05-10 16:03:04'),
(54,'App\\Models\\User',12,'auth_token','36d2d7f1db683748648e214735d184c80d9e3df38d076ab6beea623691419c59','[\"*\"]','2026-05-10 16:10:36',NULL,'2026-05-10 16:06:59','2026-05-10 16:10:36'),
(55,'App\\Models\\User',12,'auth_token','2dccdbdbca3c160a9f33a88d96627463d805a427e2d42c566fab9d49a70e4044','[\"*\"]','2026-05-10 16:31:04',NULL,'2026-05-10 16:29:20','2026-05-10 16:31:04'),
(56,'App\\Models\\User',12,'auth_token','5f2e663f37a57feeb93028103386c90f1ab4c34cf53b1aa785546fcdfc9e448f','[\"*\"]','2026-05-10 16:38:49',NULL,'2026-05-10 16:38:01','2026-05-10 16:38:49'),
(57,'App\\Models\\User',12,'auth_token','0a2e1f9e4c75591271534848ed206fe589932fe89e8cbe0f98ec2bc1113ab33c','[\"*\"]','2026-05-10 16:41:36',NULL,'2026-05-10 16:41:26','2026-05-10 16:41:36'),
(58,'App\\Models\\User',12,'auth_token','c4776148b0393dc33128782f432830d3ff1036050efa446045455de0bd1b9483','[\"*\"]','2026-05-10 17:05:54',NULL,'2026-05-10 17:05:52','2026-05-10 17:05:54'),
(59,'App\\Models\\User',27,'auth_token','a9498f3baf5d6eb793e2495e4bdc1ac02bb780a2642a2b8e60771868b6e6838d','[\"*\"]','2026-05-11 14:37:31',NULL,'2026-05-11 14:36:36','2026-05-11 14:37:31'),
(60,'App\\Models\\User',12,'auth_token','80884f67c0b7c38847e273dd398821c5d378f1a1d105212bafbc823ee43ceba8','[\"*\"]','2026-05-11 14:52:57',NULL,'2026-05-11 14:52:54','2026-05-11 14:52:57'),
(61,'App\\Models\\User',27,'auth_token','70d514bb1a30c21683e41424532e935f67b2007c798e834c75cb182c7fe9eecb','[\"*\"]','2026-05-11 14:55:36',NULL,'2026-05-11 14:53:17','2026-05-11 14:55:36'),
(62,'App\\Models\\User',12,'auth_token','d5c590fbb35ba12b07ad2b854ce43e802dcd25ffc4e59dd230f8624d3db2e617','[\"*\"]','2026-05-11 14:58:14',NULL,'2026-05-11 14:57:04','2026-05-11 14:58:14'),
(63,'App\\Models\\User',27,'auth_token','c859a86a6ed56f3f607e1ac7668cc37894bd886dab3f5484695929b70d51d4ac','[\"*\"]','2026-05-11 14:59:34',NULL,'2026-05-11 14:59:06','2026-05-11 14:59:34'),
(64,'App\\Models\\User',27,'auth_token','6639e5ec74d1e13473f4df05b4693accf5f34277c6afc738e2a0ddbebfb848ae','[\"*\"]','2026-05-11 15:03:06',NULL,'2026-05-11 15:02:20','2026-05-11 15:03:06'),
(65,'App\\Models\\User',27,'auth_token','fe4b666991ffec3cda732d1d158ff84be781902900164002f5b4c15c04967e69','[\"*\"]','2026-05-11 15:12:36',NULL,'2026-05-11 15:10:57','2026-05-11 15:12:36'),
(66,'App\\Models\\User',27,'auth_token','a0e6acc4934d5f151a317332b8abbcead70816c5d0dec11a84738d29a02b815c','[\"*\"]','2026-05-11 15:21:00',NULL,'2026-05-11 15:19:11','2026-05-11 15:21:00'),
(67,'App\\Models\\User',12,'auth_token','09c41ceebeae76a81f705d52eecb4ccbcb4975da50b2a7df6c98185076b04bb2','[\"*\"]','2026-05-11 15:27:49',NULL,'2026-05-11 15:22:19','2026-05-11 15:27:49'),
(68,'App\\Models\\User',12,'auth_token','8599329f59f3c7eb1a0437366169595f1ac31f8a64583f1c1cbf314d85f682fd','[\"*\"]','2026-05-11 15:33:06',NULL,'2026-05-11 15:32:06','2026-05-11 15:33:06'),
(69,'App\\Models\\User',12,'auth_token','054509679329221a8dc31b95429d71407e1be352f326a5ba7196780cf82c30c0','[\"*\"]','2026-05-12 05:11:45',NULL,'2026-05-12 05:10:57','2026-05-12 05:11:45'),
(70,'App\\Models\\User',12,'auth_token','1057bf6cb9e6311869fcd9e9019a0c1756c65ebd4a45394b0a74bbdc55991c7c','[\"*\"]','2026-05-12 05:12:04',NULL,'2026-05-12 05:10:59','2026-05-12 05:12:04'),
(71,'App\\Models\\User',12,'auth_token','288dd4cd3602e61209968f2466f149ffa895f2283fc116a3ba7232772dff5456','[\"*\"]','2026-05-12 06:16:01',NULL,'2026-05-12 06:15:58','2026-05-12 06:16:01'),
(72,'App\\Models\\User',12,'auth_token','4a3232c74d254845c0fcde298f26a9d44abcb8adf76b5cc29dc7f6c1fa06251e','[\"*\"]','2026-05-12 06:18:40',NULL,'2026-05-12 06:17:50','2026-05-12 06:18:40'),
(73,'App\\Models\\User',12,'auth_token','6c1ad898b9bcacec0f492b1ca157c93798017b13bd34d17015ee23b02c36e977','[\"*\"]','2026-05-12 06:18:44',NULL,'2026-05-12 06:17:51','2026-05-12 06:18:44'),
(74,'App\\Models\\User',12,'auth_token','4d2ea3aaefc3cdfaa17848713c07181e88fbb529ae58c62ab935b3364d45d405','[\"*\"]','2026-05-12 06:26:25',NULL,'2026-05-12 06:25:21','2026-05-12 06:26:25'),
(75,'App\\Models\\User',12,'auth_token','5a28ba4ce85c7527367016e6ef4a8416bd3f1ebc082af3a5a5efd6a3b53a5f4a','[\"*\"]','2026-05-12 06:31:56',NULL,'2026-05-12 06:31:53','2026-05-12 06:31:56'),
(76,'App\\Models\\User',12,'auth_token','d524b6c92eb4729bf7ee0170fb776023f85f9ee45153451f185c8dffe719e770','[\"*\"]','2026-05-12 06:32:46',NULL,'2026-05-12 06:31:55','2026-05-12 06:32:46');

/*Table structure for table `reports` */

DROP TABLE IF EXISTS `reports`;

CREATE TABLE `reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `incident_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reports_user_id_foreign` (`user_id`),
  CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `reports` */

/*Table structure for table `scholarship_eligibility_criteria` */

DROP TABLE IF EXISTS `scholarship_eligibility_criteria`;

CREATE TABLE `scholarship_eligibility_criteria` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `scholarship_id` bigint unsigned NOT NULL,
  `min_spm_as` int DEFAULT NULL,
  `max_spm_as` int DEFAULT NULL,
  `required_subjects` json DEFAULT NULL,
  `bumiputera_required` tinyint(1) DEFAULT '0',
  `bumiputera_priority` tinyint(1) DEFAULT '0',
  `gender_requirement` enum('Any','Male','Female') COLLATE utf8mb4_unicode_ci DEFAULT 'Any',
  `citizenship_required` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_requirement` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rural_priority` tinyint(1) DEFAULT '0',
  `max_monthly_income` decimal(10,2) DEFAULT NULL,
  `income_categories` json DEFAULT NULL,
  `fields_of_study` json DEFAULT NULL,
  `study_destination` enum('Local','Overseas','Both') COLLATE utf8mb4_unicode_ci DEFAULT 'Both',
  `min_age` int DEFAULT NULL,
  `max_age` int DEFAULT NULL,
  `leadership_required` tinyint(1) DEFAULT '0',
  `leadership_priority` tinyint(1) DEFAULT '0',
  `sports_achievement` tinyint(1) DEFAULT '0',
  `min_community_hours` int DEFAULT NULL,
  `bond_required` tinyint(1) DEFAULT '0',
  `bond_years` int DEFAULT NULL,
  `priority_weight` int DEFAULT '1',
  `max_score` int DEFAULT '100',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `study_paths` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_scholarship_criteria` (`scholarship_id`),
  CONSTRAINT `fk_scholarship_criteria` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `scholarship_eligibility_criteria` */

insert  into `scholarship_eligibility_criteria`(`id`,`scholarship_id`,`min_spm_as`,`max_spm_as`,`required_subjects`,`bumiputera_required`,`bumiputera_priority`,`gender_requirement`,`citizenship_required`,`state_requirement`,`rural_priority`,`max_monthly_income`,`income_categories`,`fields_of_study`,`study_destination`,`min_age`,`max_age`,`leadership_required`,`leadership_priority`,`sports_achievement`,`min_community_hours`,`bond_required`,`bond_years`,`priority_weight`,`max_score`,`notes`,`created_at`,`updated_at`,`study_paths`) values 
(25,44,1,4,NULL,0,0,'Any',NULL,NULL,0,NULL,NULL,NULL,'Both',NULL,NULL,0,0,0,NULL,0,NULL,1,100,NULL,'2026-04-27 11:51:25','2026-04-27 11:51:25',NULL),
(26,47,4,8,'[]',0,0,'Any','Malaysia','Terengganu',0,800.00,NULL,'[]','Both',18,19,0,0,0,NULL,0,NULL,1,100,NULL,'2026-05-15 13:48:05','2026-05-15 13:48:05','[]'),
(47,68,8,NULL,'[\"Mathematics\"]',0,0,'Any','Malaysian',NULL,0,NULL,NULL,'[\"Computer Science\", \"Data Science\", \"Finance\", \"Accounting\", \"Economics\", \"Law\", \"Actuarial Science\", \"Mathematics\", \"Statistics\", \"Science\"]','Overseas',NULL,19,0,1,0,NULL,0,NULL,1,100,'Auto-parsed by ruleParser','2026-05-16 03:42:46','2026-05-16 03:45:36','[\"Foundation\", \"Degree\", \"Postgraduate\"]'),
(50,71,8,NULL,'[\"Mathematics\"]',0,1,'Any','Malaysian',NULL,0,20000.00,NULL,'[\"Engineering\", \"Computer Science\", \"Data Science\", \"Finance\", \"Accounting\", \"Economics\", \"Mathematics\", \"Statistics\", \"Business\", \"Communication\", \"Education\"]','Overseas',17,NULL,0,1,1,NULL,0,NULL,1,100,'Auto-parsed by ruleParser','2026-05-16 17:16:53','2026-05-16 18:12:10','[\"Foundation\", \"Matriculation\", \"Diploma\", \"Degree\"]'),
(59,77,NULL,NULL,'[\"Mathematics\", \"Physics\", \"Chemistry\", \"Biology\"]',0,0,'Any',NULL,NULL,0,NULL,NULL,'[\"Architecture\", \"Engineering\", \"Medicine\", \"Computer Science\", \"Finance\", \"Accounting\", \"Economics\", \"Law\", \"Mathematics\", \"Statistics\", \"Archaeology\", \"Art & Design\", \"History\", \"Linguistics\", \"Performing Arts\", \"Philosophy\", \"Chemistry\", \"Physics\", \"Geography\", \"Environmental Science\", \"Biological Science\", \"Pharmacy\", \"Business\", \"Communication\", \"Education\", \"Hospitality\", \"Anthropology\", \"Social Science\"]','Both',NULL,NULL,0,0,1,NULL,0,NULL,1,100,'Auto-parsed by ruleParser','2026-05-16 18:11:59','2026-05-31 01:33:28','[\"Foundation\", \"Degree\", \"Postgraduate\"]'),
(60,78,NULL,NULL,'[\"Mathematics\", \"Physics\", \"Chemistry\", \"Biology\"]',0,0,'Any',NULL,NULL,0,NULL,NULL,'[\"Engineering\", \"Computer Science\", \"Finance\", \"Accounting\", \"Economics\", \"Law\", \"Mathematics\", \"Statistics\", \"Archaeology\", \"Architecture\", \"Art & Design\", \"History\", \"Linguistics\", \"Performing Arts\", \"Philosophy\", \"Chemistry\", \"Physics\", \"Geography\", \"Environmental Science\", \"Biological Science\", \"Pharmacy\", \"Business\", \"Communication\", \"Education\", \"Hospitality\", \"Anthropology\"]','Overseas',NULL,NULL,0,0,1,NULL,0,NULL,1,100,'Auto-parsed by ruleParser','2026-05-16 18:29:04','2026-05-16 18:29:04','[\"Foundation\", \"Degree\", \"Postgraduate\"]'),
(61,79,NULL,NULL,'[\"Mathematics\", \"Physics\", \"Chemistry\", \"Biology\"]',0,0,'Any',NULL,NULL,0,NULL,NULL,'[\"Engineering\", \"Medicine\", \"Computer Science\", \"Finance\", \"Accounting\", \"Economics\", \"Law\", \"Mathematics\", \"Statistics\", \"Archaeology\", \"Architecture\", \"Art & Design\", \"History\", \"Linguistics\", \"Performing Arts\", \"Philosophy\", \"Chemistry\", \"Physics\", \"Geography\", \"Environmental Science\", \"Biological Science\", \"Pharmacy\", \"Business\", \"Communication\", \"Education\", \"Hospitality\", \"Anthropology\"]','Local',NULL,NULL,0,1,1,NULL,0,NULL,1,100,'Auto-parsed by ruleParser','2026-05-16 18:35:37','2026-05-16 18:35:37','[\"Foundation\", \"Degree\", \"Postgraduate\"]'),
(62,80,8,NULL,'null',0,1,'Any',NULL,NULL,1,NULL,'[\"B40\", \"M40\"]','[\"Engineering\", \"Medicine\", \"Computer Science\", \"Law\", \"Statistics\", \"Science\"]','Overseas',NULL,18,0,0,0,NULL,0,NULL,1,100,'Auto-parsed by ruleParser','2026-05-16 19:15:46','2026-05-16 23:41:59','[\"Foundation\", \"Degree\"]'),
(64,90,6,NULL,'null',0,0,'Any','Malaysian',NULL,0,4850.00,NULL,'[\"Computer Science\", \"Finance\", \"Education\"]','Local',NULL,19,0,0,0,NULL,0,NULL,1,100,'Auto-parsed by ruleParser','2026-05-17 00:20:42','2026-05-31 01:34:04','[\"Foundation\", \"Matriculation\"]'),
(65,93,8,NULL,'[\"Physics\", \"Chemistry\"]',0,0,'Any','Malaysian',NULL,0,NULL,NULL,'[\"Engineering\", \"Accounting\", \"Economics\", \"Law\", \"Science\"]','Overseas',NULL,22,0,1,1,NULL,0,NULL,1,100,'Auto-parsed by ruleParser','2026-05-17 00:28:38','2026-05-17 00:28:38','[\"Degree\", \"TVET\"]'),
(66,96,8,NULL,'null',0,0,'Any',NULL,NULL,0,NULL,NULL,'[\"Engineering\", \"Data Science\", \"Business\", \"Education\"]','Overseas',NULL,NULL,0,1,1,NULL,0,NULL,1,100,'Auto-parsed by ruleParser','2026-05-17 00:39:07','2026-05-17 00:40:53','[\"Foundation\", \"Degree\"]'),
(68,99,NULL,NULL,'null',0,0,'Any',NULL,NULL,0,NULL,NULL,'[\"Technical\"]','Both',NULL,NULL,0,0,0,NULL,0,NULL,1,100,'Auto-parsed by ruleParser','2026-05-17 00:52:32','2026-05-17 01:46:54','[\"Diploma\", \"TVET\"]'),
(69,100,8,NULL,'null',0,0,'Any',NULL,NULL,0,NULL,NULL,'[\"Computer Science\"]','Local',NULL,NULL,0,0,0,NULL,1,NULL,1,100,'Eligibility details not explicitly stated on official page','2026-05-17 01:01:43','2026-05-17 01:01:43','[\"Degree\"]'),
(70,101,8,NULL,'[]',0,0,'Any','Malaysia',NULL,0,NULL,NULL,'[\"Engineering\", \"Medicine\"]','Both',NULL,19,0,0,0,NULL,1,NULL,1,100,'Eligibility details not explicitly stated on official page','2026-05-17 01:07:22','2026-05-16 17:46:14','[\"Foundation\", \"Diploma\"]'),
(71,102,7,NULL,'[]',0,0,'Any','Malaysia','Terengganu',0,5239.99,NULL,'[\"Computer Science\", \"Mathematics\", \"Hospitality\"]','Both',NULL,19,0,0,0,NULL,0,NULL,1,100,NULL,'2026-05-16 18:07:57','2026-05-16 18:07:57','[\"Foundation\", \"Diploma\", \"TVET\"]');

/*Table structure for table `scholarships` */

DROP TABLE IF EXISTS `scholarships`;

CREATE TABLE `scholarships` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `raw_eligibility` longtext COLLATE utf8mb4_unicode_ci,
  `application_link` text COLLATE utf8mb4_unicode_ci,
  `deadline` date DEFAULT NULL,
  `source` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'scraped',
  `source_website` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_official` tinyint(1) DEFAULT '1',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `scholarships` */

insert  into `scholarships`(`id`,`title`,`provider`,`description`,`raw_eligibility`,`application_link`,`deadline`,`source`,`source_website`,`is_official`,`is_active`,`created_at`,`updated_at`) values 
(44,'testtt','testtt','ddd',NULL,NULL,'2026-04-30','manual',NULL,1,1,'2026-04-27 11:51:25','2026-04-27 11:51:25'),
(47,'hhh','hhhh','hhh','hhhh','https://www.petronas.com/careers/students-graduates','2026-06-25','manual',NULL,1,1,'2026-05-15 13:48:05','2026-05-15 13:48:05'),
(68,'Kijang Pre-University Scholarship','Bank Negara Malaysia','Kijang Pre-University Scholarship','Navigation\nSkip to Content\nOur Scholarships\nMeet our Scholars\nHow to Apply\nFAQs\nMore Opportunities\nBM\n\n\nScholarships\n\n \n\n Careers\nScholarships\n\n \n8.7k\nShares\n\nBank Negara Malaysia (BNM) is committed to shaping the best Malaysian talents through our scholarship programme. Our scholarship holders will have access to the best learning experiences at top universities as per the BNM’s list of approved universities in the fields of Economics, Accounting and Finance, Actuarial Science, Mathematics, Statistics, Data Science, Computer Science and Law.\n\nBeyond just outstanding academic achievements, BNM also seeks individuals with excellent leadership qualities as well as exceptional interpersonal and communication skills.\n\nScholars will also benefit from holistic development opportunities, including regular engagement and continuous support from BNM, as well as a structured internship during summer breaks. Through this programme, BNM aims to equip scholars with the knowledge, skills, and values necessary to contribute meaningfully to the nation’s economic and financial advancement.\n\n \n\nBNM offers two types of scholarship programmes:\n\nKijang Pre-University Scholarship\nKijang Undergraduate Scholarship (Apply Now) 17 April - 26 April 2026\n\nKijang Pre-University Scholarship\n\nEligibility Criteria\n\nExcellent Academic Achievements: A minimum of 8As (A+ and A only) obtained in the recent SPM along with CEFR.\nStrong leadership qualities, proven teamwork capabilities, outstanding performance in extra-curricular activities and exceptional interpersonal skills.\nApplicant must be a Malaysian citizen and not exceeding 19  years of age on 1 July 2026.\n\nWhat’s Next?\n\nOnce awarded, scholarship recipients will begin their studies with pre‑university programmes at BNM’s approved pre-university colleges. \nThe scholarship provides comprehensive support, including tuition fees, subsistence allowance and other related allowances.\nProgression to an undergraduate programme is subject to scholars meeting the requirements set by BNM under the Kijang Undergraduate Programme.\nUpon successful completion of their undergraduate studies, scholars will be required to fulfil their employment bond with BNM, based on a ratio of one year of study to two years of service.\n\nNote: Only shortlisted candidates will be notified for further assessments.\n\nMeet Our Scholars\n\nSubmitting Your Application\n\nImportant note\n\nBy submitting an application, you will have declared that you have read, understood and accepted our Notice on the Personal Data Protection Act (2010) and give us full consent to process your personal information along with sensitive data.\n\nScholarship FAQs\n1. What do the scholarships offer to a successful applicant?\n2. How do I submit my application?\n3. When do I need to provide the supporting documents?\n4. I do not have SPM examination results because I studied overseas. Am I still eligible to apply?\n5. I am currently studying A-levels and will sit for the exams in June this year. Which examination results can I provide in the meantime?\n6. Is the Bank\'s scholarship limited to certain subjects only?\n7. Which universities are considered acceptable for undergraduate or postgraduate study?\n8. What does the selection process involve?\n9. How many scholarships are awarded every year?\n10. What is the minimum grade I am expected to achieve during my study programme?\n11. I just missed the application closing date. Can you accept my applications if I submit it now?\n12. I applied last year but was not successful. If I reapply, will my application be considered?\n13. Are the scholarship programmes offered by the Bank open only to Malaysian citizens?\n14. Upon completion of the study programme would I be contractually bounded to work at the Bank?\n15. I have more questions, whom should I speak to?\n\nMore Opportunities\n\nKijang Graduate Programme\n\nThe Kijang Graduate Programme provides a holistic exposure to the Bank\'s diverse roles in shaping the nation. You will be job-rotated across the Bank\'s sectors over 18 months, allowing you to discover the possible range of career paths with us.\n\nLearn More\n\nInternships\n\nOur internship programme offers the opportunity to gain invaluable working experience and transferable skills within a range of specialized fields, whilst contributing to central banking work that directly impacts the nation\'s progress.\n\nLearn More\n\nJob Vacancies\n\nWhat we do shapes the lives of Malaysians every day, and for years to come. This is why here in Bank Negara Malaysia, we look for the best and brightest, invest in our people, and offer diverse paths for professional growth.\n\nLearn More\n\nSubscribe to our email alerts\n\nFollow us on social media\n\nFor specific enquiries on:\n\nJob applications\n\nrecruit@bnm.gov.my\n\nScholarships\n\nprofiling@bnm.gov.my\n\nKijang Graduate Programme\n\nkijang.GP@bnm.gov.my\n\nInternships\n\nbnminternship@bnm.gov.my\n\nFor general enquiries:\n\nBNMLINK Contact Centre\n\n1-300-88-5465\nMonday - Friday 9am - 5pm\nGeneral Line\n+603-2784-8888\n© 2026 Bank Negara Malaysia. All rights reserved. Terms of Use. Disclaimer. Privacy Policy.','https://www.bnm.gov.my/careers/scholarships','2026-04-26','scraped','bnm',1,1,'2026-05-16 03:42:46','2026-05-16 03:42:46'),
(71,'BPMB Award of Group Undergraduate Scholarship (BAGUS)','Bank Pembangunan Malaysia Berhad','BPMB Award of Group Undergraduate Scholarship (BAGUS)','Skip to main content\nSkip to footer\nScholarship\nMedia\nEXIM Bank\nSME Bank\nCFIL\nAbout Us\nProgrammes\nFinancing\nTreasury\nMIND and Sustainability\nReports\nCareers\nContact Us\nEN\nBM\nTransform Your Education\nwith BAGUS\n(BPMB Award of Group’s Undergraduate Scholarship)\n\nBank Pembangunan Malaysia Berhad (BPMB) is dedicated to nurturing promising Malaysian talents who will play a pivotal role in advancing the nation’s development agenda through our BPMB Award of Group’s Undergraduate Scholarship (BAGUS) programme.\n\nBAGUS\nScholarships for Malaysia\'s Outstanding Students\n\nOpen to outstanding Malaysian students, the BAGUS Programme provides opportunities to pursue undergraduate degrees in banking-related disciplines at reputable universities both locally and abroad.\n\nBeginning in 2026, BPMB will also offer scholarships for candidates enrolled in twinning programmes.\nEligibility for the twinning programme is subject to the partner university being ranked within the top 100 of the QS World University Rankings. \n\nApplication Requirements\nGeneral\nCitizen of Malaysia\nAge at least 17 years old and not more than 23 years old\nPossess good interpersonal and communication skills\nAbility to demonstrate leadership qualities and potential\nActively involved in extra-curricular activities and sports\nApplication from undergraduate students in Year 1 will also be considered\nEducation\nCompleted Pre-University/\nFoundation/Matriculation/\nDiploma program and achieved excellent examination results in any of the following:\nSTPM / A-Level: Minimum\nof 3As\nDiploma / Matriculation: Minimum Cumulative Grade Point Average (CGPA) of 3.50\nAustralian Matriculation: Minimum ATAR of 80%\nAmerican Foundation Program: Minimum SAT\nscore of 1,200\nSPM: Minimum 8As in Sijil Pelajaran Malaysia or equivalent\nFinancial\n\nApplicants must not be a recipient of any other scholarship or award from other organisations/foundations\nMonthly household income of RM20,000 and below\nScholarship Coverage\nOur scholarship recipients will have access to world-class education, covering fields such as:\nBusiness\n\nAccounting/ACCA, Business, Finance, and Economics\n\nEngineering and Technology\n\n\nEngineering, Information Systems, Information and Communication Technology (ICT), Computer Science, and Data Science/Analytics\n\nOther Banking-related Study\nActurarial Science, Mathematics, and Statistics\n\nThe scholarship covers tuition fees, subsistence, and related allowances. Upon successful completion of the undergraduate programme, scholars will be considered for employment with BPMB and will be required to serve the Bank for a period of three (3) to six (6) years.\n\nList of Universities\n\nApplicants may apply for scholarships to the universities below for Education Year of 2026:\n\nMalaysia\nAustralia\nUnited Kingdom\nUnited States\nMalaysia\nUniversiti Kebangsaan Malaysia\nUniversiti Malaya\nUniversiti Putra Malaysia\nUniversiti Sains Malaysia\nUniversiti Teknologi Malaysia\nUniversiti Utara Malaysia\nSunway University\nTaylor’s University\nUCSI University\nUniversiti Teknologi PETRONAS\nZakat for Education, Hope for the Future\n\nTo help more deserving students access higher education, BPMB has introduced BAGUS UMMAH – a Zakat-funded extension of the BAGUS Scholarship Programme dedicated to supporting Asnaf students.\nGuided by our values of community impact, diversity and inclusion, the initiative ensures financial limitations do not stand in the way of academic potential.\nThe programme is overseen by BPMB Group’s Shariah Management team in collaboration with selected local universities and disbursement is made through the universities’ bursaries.\n\nSubmit Your Application\nStatement on Eligibility\n\nApplications must meet the minimum requirements to be considered. Those that do not meet these requirements will be deemed ineligible.\n\nApplication Opens\n\nPlease email your application to BAGUS@bpmb.com.my by 15 June 2026.\n\nApplication Form\nStatement on Education Results\n\nApplications using forecast results will be accepted; however, applicants must submit their official results once released.\nBPMB will use the official results for the final selection. Shortlisted candidates will be required to undergo assessments and interviews.\n\nEN\nBM\nBank Pembangunan\nMIND and Sustainability\nLeadership\nCommunity and Social Impact\nAwards and Milestones\n50 Years of Impact\nSuccess Stories\nCorporate Governance\nProgramme\nMADANI Development Programme\nTransportation & Logistics Programme\nDigital Infrastructure & High Impact Sectors Programme\nSustainable Development & Transition Programme\nRenewable Energy & Transition Programme\nBumiputera Economic Development Programme\nNational Energy Transition Facility\nBlended Finance\nFinancing\nTawarruq Asset Financing (TWA)\nTawarruq Fixed Working Capital (TWF)\nTawarruq Revolving Working Capital (TWQR)\nKafalah Bank Guarantee-i (BG-i)\nFees and Charges\nTreasury\nTawarruq Deposit\nInterest Rate Swap (IRS)\n⁠⁠Islamic Profit Rate Swap (IPRS)\nForeign Exchange (FX)\n⁠⁠Islamic Foreign Exchange (FX-i)\nReports\nCareers\nScholarship\nMedia\nContact Us\ne-Procurement\n\n© 1973 - 2026 Bank Pembangunan Malaysia Berhad 197301003074 (16562-K)\nAll rights reserved | Privacy Notice | Terms, Policies and Disclaimer','https://www.bpmb.com.my/scholarship/','2026-06-15','scraped','bpmb',1,1,'2026-05-16 17:16:53','2026-05-16 17:16:53'),
(77,'Khazanah Watan Scholarship Programme','Yayasan Khazanah','Khazanah Watan Scholarship Programme','	\nAbout Us	\nScholarship Programmes\nACE²	\n#YKStories	\nContact Us\n	\nApply Now\nKhazanah Watan Scholarship Programme\nInvesting in Today’s Talent for Tomorrow’s Promise.\nProgramme Summary\nWhere To Study\nOur approved universities in Malaysia\n	\nStudy Level\nUniversity\n	\nApplication Cycle\nIn March, yearly\n(for Postgraduate studies)\nIn May / June, yearly\n(for Foundation studies)\nIn July, yearly\n(for Undergraduate studies)\n	\nScholarship Coverage\nFull\n* Dates may be subject to change.\nGeneral Information	Eligibility	Subject Areas	Approved Universities\n	\nArts & Humanities\nArchaeology\nArchitecture / Built Environment\nArt & Design\nClassics & Ancient History\nEnglish Language & Literature\nHistory\nLinguistics\nModern Languages\nPerforming Arts\nPhilosophy\nTheology, Divinity & Religious Studies\n	\nEngineering & Technology\nComputer Science\nEngineering - Mechanical\nEngineering - Civil & Structural\nEngineering - Electrical & Electronic\nEngineering - Mechanical, Aeronautical & Manufacturing\n	\nLife Sciences\nAgriculture\nAnatomy & Physiology\nBiological Science\nPharmacy & Pharmacology\nScience\n	\nNatural Sciences\nChemistry\nEarth & Marine Sciences\nEnvironmental Sciences\nGeography\nGeophysics\nMaterial Sciences\nMathematics\nPhysics\n	\nSocial Sciences & Management\nAccounting & Finance\nAnthropology\nBusiness & Management Studies\nCommunication & Media Studies\nDevelopment Studies\nEconomics & Econometrics\nEducation\nHospitality & Leisure Management\nLaw\nLibrary & Information Management\nPolitics\nSocial Policy & Administration\nSociology\nSports-Related Subjects\nStatistics & Operational Research\nNote: We do not sponsor degree programmes in Medicine, Dentistry, Veterinary Science or Architecture. However, Postgraduate studies in these fields are eligible for application.\nOther Scholarship Programmes\n\nKhazanah Watan Scholarship – Equity Pathway Programme\nFoundation (University)\n	\nOur approved universities in Malaysia\nRhodes Scholarship for Malaysia, in Partnership With Yayasan Khazanah\nMaster\'s Studies\n	\nUniversity of Oxford, UK\nKhazanah-Oxford Centre for Islamic Studies Merdeka Scholarship Programme\nMaster\'s & PhD Studies\n	\nUniversity of Oxford, UK\n	\nLatest Updates\nApplication Timeline\n	\n29th March 12:00AM – 13th April 2026 11:59PM MYT (Foundation Studies)\nTimeline Details\n* Dates may be subject to change.\n\"Thanks to Yayasan Khazanah I am now able to unlock my full potential.\"\nThaqif Aris bin Johan Aris\nKolej Yayasan Saad, Melaka\nRead Thaqif\'s Story\nDisclaimerAll scholarship programmes are administered by Yayasan Khazanah and is subject to the availability of funding, as well as applicable policies and guidelines in place from time to time.\n\nWhile Yayasan Khazanah aims to support eligible candidates, participation is not guaranteed and does not constitute an entitlement. All applications are assessed through a competitive selection process, and decisions are made at the discretion of Yayasan Khazanah.\n\nYayasan Khazanah reserves the right to review, update, or amend the programme at any time, including its eligibility criteria, selection process, and terms and conditions, where necessary.\n\nApplicants are expected to provide accurate and complete information. Yayasan Khazanah reserves the right to verify submitted information and request additional documentation where required, and may withdraw or revise any offer in cases of inaccurate or incomplete information.\n\nYayasan Khazanah shall not be held responsible for any loss or inconvenience arising from participation in, or reliance on, the programme.\nReady To Join Yayasan Khazanah?\nApply Now\nAbout Us\nWho We Are\nWhy Yayasan Khazanah?\nOur Aspiration\nOur Selection Process\nScholarship Programmes\nACE²\nExecutive Short Courses\n#YKStories\nScholars\nPublications\nMedia & Events\n	\nContact Us\n+603 - 5870 4333\nyk@yayasankhazanah.com.my\n© 2026 YAYASAN KHAZANAH 200601021924 (741677V). All Rights Reserved\n	\nPrivacy Policy | Corporate Governance | Khazanah Nasional Berhad','https://yayasankhazanah.com.my/scholarship-programmes/khazanah-watan-scholarship-programme','2026-04-12','scraped','khazanah_watan',1,1,'2026-05-16 18:11:59','2026-05-16 18:28:26'),
(78,'Wakalah PayNet – Yayasan Khazanah Scholarship Programme','Yayasan Khazanah','Wakalah PayNet – Yayasan Khazanah Scholarship Programme','	\nAbout Us	\nScholarship Programmes\nACE²	\n#YKStories	\nContact Us\n	\nApply Now\nWakalah PayNet-Yayasan Khazanah Scholarship Programme\nInvesting in Today’s Talent for Tomorrow’s Promise.\nProgramme Summary\nWhere To Study\nOur approved universities in Malaysia\n	\nStudy Level\nUniversity\n	\nApplication Cycle\nIn May / June, yearly\n(for Foundation studies)\nIn July, yearly\n(for Undergraduate studies)\n	\nScholarship Coverage\nFull\n* Dates may be subject to change.\nGeneral Information	Eligibility	Subject Areas	Approved Universities\n	\nArts & Humanities\nArchaeology\nArchitecture / Built Environment\nArt & Design\nClassics & Ancient History\nEnglish Language & Literature\nHistory\nLinguistics\nModern Languages\nPerforming Arts\nPhilosophy\nTheology, Divinity & Religious Studies\n	\nEngineering & Technology\nComputer Science\nEngineering - Mechanical\nEngineering - Civil & Structural\nEngineering - Electrical & Electronic\nEngineering - Mechanical, Aeronautical & Manufacturing\n	\nLife Sciences\nAgriculture\nAnatomy & Physiology\nBiological Science\nPharmacy & Pharmacology\nScience\n	\nNatural Sciences\nChemistry\nEarth & Marine Sciences\nEnvironmental Sciences\nGeography\nGeophysics\nMaterial Sciences\nMathematics\nPhysics\n	\nSocial Sciences & Management\nAccounting & Finance\nAnthropology\nBusiness & Management Studies\nCommunication & Media Studies\nDevelopment Studies\nEconomics & Econometrics\nEducation\nHospitality & Leisure Management\nLaw\nLibrary & Information Management\nPolitics\nSocial Policy & Administration\nSociology\nSports-Related Subjects\nStatistics & Operational Research\nOther Scholarship Programmes\n\nKhazanah Global Scholarship Programme\nA-Level, Undergraduate, Master\'s & PhD Studies\n	\nAbroad\nKhazanah-Oxford Centre for Islamic Studies Merdeka Scholarship Programme\nMaster\'s & PhD Studies\n	\nUniversity of Oxford, UK\nChevening-Khazanah Scholarship Programme\nMaster\'s Studies\n	\nApproved universities in the UK\n	\nLatest Updates\nApplication Timeline\n	\n29th March 12:00AM – 13th April 2026 11:59PM MYT (Foundation Studies)\nTimeline Details\n* Dates may be subject to change.\n“The Khazanah scholarship investment in my education humbled me.\"\nDr Anne Marie Warren\nPhD in IS (Civic Engagement in Social Media), University of Malaya\nRead Anne\'s Story\nDisclaimerAll scholarship programmes are administered by Yayasan Khazanah and is subject to the availability of funding, as well as applicable policies and guidelines in place from time to time.\n\nWhile Yayasan Khazanah aims to support eligible candidates, participation is not guaranteed and does not constitute an entitlement. All applications are assessed through a competitive selection process, and decisions are made at the discretion of Yayasan Khazanah.\n\nYayasan Khazanah reserves the right to review, update, or amend the programme at any time, including its eligibility criteria, selection process, and terms and conditions, where necessary.\n\nApplicants are expected to provide accurate and complete information. Yayasan Khazanah reserves the right to verify submitted information and request additional documentation where required, and may withdraw or revise any offer in cases of inaccurate or incomplete information.\n\nYayasan Khazanah shall not be held responsible for any loss or inconvenience arising from participation in, or reliance on, the programme.\nReady To Join Yayasan Khazanah?\nApply Now\nAbout Us\nWho We Are\nWhy Yayasan Khazanah?\nOur Aspiration\nOur Selection Process\nScholarship Programmes\nACE²\nExecutive Short Courses\n#YKStories\nScholars\nPublications\nMedia & Events\n	\nContact Us\n+603 - 5870 4333\nyk@yayasankhazanah.com.my\n© 2026 YAYASAN KHAZANAH 200601021924 (741677V). All Rights Reserved\n	\nPrivacy Policy | Corporate Governance | Khazanah Nasional Berhad','https://www.yayasankhazanah.com.my/scholarship-programmes/wakalah-paynet-yayasan-khazanah-scholarship-programme','2026-04-12','scraped','khazanah_paynet',1,1,'2026-05-16 18:29:04','2026-05-16 18:29:04'),
(79,'Khazanah Watan Scholarship – Equity Pathway Programme','Yayasan Khazanah','Khazanah Watan Scholarship – Equity Pathway Programme','	\nAbout Us	\nScholarship Programmes\nACE²	\n#YKStories	\nContact Us\n	\nApply Now\nKhazanah Watan Scholarship – Equity Pathway Programme\nInvesting in Today’s Talent for Tomorrow’s Promise.\nProgramme Summary\nWhere To Study\nOur approved universities in Malaysia\n	\nStudy Level\nFoundation (University)\n	\nApplication Cycle\nIn March / April, yearly\n	\nScholarship Coverage\nFull\n* Dates may be subject to change.\nGeneral Information	Eligibility	Subject Areas	Approved Universities\n	\nArts & Humanities\nArchaeology\nArchitecture / Built Environment\nArt & Design\nClassics & Ancient History\nEnglish Language & Literature\nHistory\nLinguistics\nModern Languages\nPerforming Arts\nPhilosophy\nTheology, Divinity & Religious Studies\n	\nEngineering & Technology\nComputer Science\nEngineering - Mechanical\nEngineering - Civil & Structural\nEngineering - Electrical & Electronic\nEngineering - Mechanical, Aeronautical & Manufacturing\n	\nLife Sciences\nAgriculture\nAnatomy & Physiology\nBiological Science\nPharmacy & Pharmacology\nScience\n	\nNatural Sciences\nChemistry\nEarth & Marine Sciences\nEnvironmental Sciences\nGeography\nGeophysics\nMaterial Sciences\nMathematics\nPhysics\n	\nSocial Sciences & Management\nAccounting & Finance\nAnthropology\nBusiness & Management Studies\nCommunication & Media Studies\nDevelopment Studies\nEconomics & Econometrics\nEducation\nHospitality & Leisure Management\nLaw\nLibrary & Information Management\nPolitics\nSocial Policy & Administration\nSociology\nSports-Related Subjects\nStatistics & Operational Research\nNote: We do not sponsor degree programmes in Medicine, Dentistry, Veterinary Science or Architecture. However, Postgraduate studies in these fields are eligible for application.\nOther Scholarship Programmes\n\nKhazanah Watan-ACCA Scholarship Programme\nProfessional Certification\n	\nLocal\nWakalah PayNet-Yayasan Khazanah Scholarship Programme\nFoundation & Undergraduate Studies\n	\nLocal\nChevening-Khazanah Scholarship Programme\nMaster\'s Studies\n	\nApproved universities in the UK\n	\nLatest Updates\nApplication Timeline\n	\n17th April 12:00AM – 21st April 2026 11:59PM MYT (Foundation (University))\n* Dates may be subject to change.\n“The leadership programmes organised by Yayasan Khazanah were very helpful.\"\nSabiha Akter Monny\nBachelor in Electrical and Electronics Engineering, Universiti Tenaga Nasional\nRead Sabiha\'s Story\nDisclaimerAll scholarship programmes are administered by Yayasan Khazanah and is subject to the availability of funding, as well as applicable policies and guidelines in place from time to time.\n\nWhile Yayasan Khazanah aims to support eligible candidates, participation is not guaranteed and does not constitute an entitlement. All applications are assessed through a competitive selection process, and decisions are made at the discretion of Yayasan Khazanah.\n\nYayasan Khazanah reserves the right to review, update, or amend the programme at any time, including its eligibility criteria, selection process, and terms and conditions, where necessary.\n\nApplicants are expected to provide accurate and complete information. Yayasan Khazanah reserves the right to verify submitted information and request additional documentation where required, and may withdraw or revise any offer in cases of inaccurate or incomplete information.\n\nYayasan Khazanah shall not be held responsible for any loss or inconvenience arising from participation in, or reliance on, the programme.\nReady To Join Yayasan Khazanah?\nApply Now\nAbout Us\nWho We Are\nWhy Yayasan Khazanah?\nOur Aspiration\nOur Selection Process\nScholarship Programmes\nACE²\nExecutive Short Courses\n#YKStories\nScholars\nPublications\nMedia & Events\n	\nContact Us\n+603 - 5870 4333\nyk@yayasankhazanah.com.my\n© 2026 YAYASAN KHAZANAH 200601021924 (741677V). All Rights Reserved\n	\nPrivacy Policy | Corporate Governance | Khazanah Nasional Berhad','https://www.yayasankhazanah.com.my/scholarship-programmes/khazanah-watan-scholarship-%e2%80%93-equity-pathway-programme','2026-04-20','scraped','khazanah_equity',1,1,'2026-05-16 18:35:37','2026-05-16 18:35:37'),
(80,'MARA Pembiayaan Pelajaran Peringkat Persediaan','MARA','MARA Pembiayaan Pelajaran Peringkat Persediaan','	\n   \nBMEN\nMAJLIS AMANAH RAKYAT\nPORTAL\nRASMI\nKEMENTERIAN\nKEMAJUAN DESA DAN WILAYAH\nUTAMA\nINFO MARA\nPENDIDIKAN\nKEUSAHAWANAN\nPELABURAN\nHUBUNGI KAMI\nPENDIDIKAN\n\nPEMBIAYAAN PELAJARAN\n\n	\nAnda disini :»Laman Utama»Indeks»Pendidikan»Pembiayaan Pelajaran»Peringkat Persediaan\nPERINGKAT PERSEDIAAN\nPengenalan kepada Program Pembangunan Bakat Muda (YTP)\nDikhususkan kepada pelajar yang memperolehi keputusan cemerlang di peringkat Sijil Pelajaran Malaysia (SPM) bagi mengikuti Program Persediaan Ijazah Pertama di dalam dan luar negara. Keutamaan akan diberikan kepada pelajar cemerlang dari golongan B40 dan M40 khususnya golongan miskin bandar dan luar bandar.\nProgram Persediaan: IB/A-Level / Foundation/Pra Universiti/Program Persediaan ke Universiti “non-native English speaking countries”.\nLaluan ke 10 Universiti Terbaik Mengikut Negara dan 30 Universiti Terbaik Dunia Mengikut Bidang (Top 10 University by Country and Top 30 University by Subject) setelah tamat peringkat persediaan.\nSyarat Permohonan\nCemerlang di peringkat Sijil Pelajaran Malaysia (SPM) pada tahun semasa\nKeutamaan pada Kumpulan B40\nLulus Program MARA Student Assessment Programme (MSAP)\nPenglibatan aktif dan cemerlang dalam aktiviti ko-kurikulum\nBerumur 18 tahun pada tahun permohonan\n		\nSyarat Penajaan\nPemohon dan salah seorang ibu atau bapa adalah warganegara dan bertaraf bumiputera.\nIbu dan bapa/Penjaga serta pelajar TIDAK disenaraihitam oleh MARA.\nPemohon tidak menerima bantuan kewangan / tajaan dari mana-mana agensi untuk peringkat yang sama (double sponsor).\nKursus yang diikuti adalah kursus yang diiktiraf oleh Kerajaan Malaysia dan Badan Profesional Malaysia\nTidak pernah ditamatkan bantuan oleh mana-mana penaja atas sebab tindakan disiplin yang menyalahi undang-undang negara dan tidak mempunyai rekod jenayah.\nBebas daripada penyakit kronik / berjangkit / penyakit yang memerlukan rawatan susulan dan berupaya mengikuti pengajian sehingga tamat.\nTertakluk kepada dasar dan syarat-syarat yang sedang berkuatkuasa\nLain-lain\nBidang keutamaan adalah Computing, Health Science and Medicine, Engineering, Arts and Design, Social Science (C.H.E.Ar.S)\nPelajar yang berjaya menamatkan pengajian serta memenuhi syarat kelayakan yang ditetapkan, akan melanjutkan pengajian ke peringkat ijazah pertama di dalam negara atau di luar negara\n\ne-PERKHIDMATAN\nAduan SISPAA\ne-Baki\nMARA EPS\nMyEduloan\nMyUsahawan\nMyPremis\ne-Potensi\nKlinik Panel MARA\nMyHALAL\nAlumni\n\n\nMajlis Amanah Rakyat (MARA)\n21, Jalan MARA\n50609 Kuala Lumpur\nMARA Hotline : 03-26132000\n\nPAUTAN\nmyGoverment\nKKDW\nFELCRA\nKEMAS\nRISDA\nJAKOA\nKETENGAH\nPERDA\nKESEDAR\nKEDA\nKEJORA\n\n\nBILANGAN PELAWAT\n\nPelawat Hari Ini: [esi wpstatistics stat=visitors time=today]\n\nJumlah Pelawat: [esi wpstatistics stat=visitors time=total]\n\nJumlah Capaian: [esi wpstatistics stat=visits time=total]\n\nHakcipta Terpelihara 2025 © Majlis Amanah Rakyat (MARA)\nTerma & Syarat\nDasar Privasi\nNotis Privasi\nDasar Keselamatan\nPenafian\nBantuan','https://www.mara.gov.my/bm/pendidikan/pembiayaan-pelajaran/peringkat-persediaan/',NULL,'scraped','mara',1,1,'2026-05-16 19:15:46','2026-05-16 19:15:46'),
(90,'Axiata Equity in Education Fund','Axiata Foundation','Axiata Equity in Education Fund','ABOUT US\nEDUCATION\nCOMMUNITY INVESTMENTS\nENVIRONMENT\nFLAGSHIP\nOUR STORIES\nMEDIA\nGet In Touch\n/\nEducation\n/\nEquity In Education Fund\nAxiata Foundation All-Star Bestari Scholarship\n\nApplications are closed.\n\nProgramme Overview\n\n \n\nProgramme Journey\n\n \n\nTestimonials\n\n \n\nFAQ\n\nEnabling Education For All\n\nThe Axiata Foundation All-Star Bestari Scholarship empowers high-potential students from underprivileged backgrounds and marginalised communities to pursue and complete their studies, enabling them to fulfil their greatest potential. \n\n \n\nThe programme is offered at pre-university levels, providing scholarship recipients with financial support to cover their education and living expenses. The programme also nurtures their development beyond academics through structured interventions for holistic personal and professional growth.\n\n \n\nApplications are closed.\n\nAbout the programme\n\nAxiata Foundation invites high-potential students from underprivileged and marginalised communities backgrounds who have demonstrated excellence in their academics and beyond to apply for the All-Star Bestari Scholarship. \n\n \n\nThrough the scholarship, they will be supported to pursue their pre-university education at local public institutions. \n\nWhat does the scholarship programme cover?\nA full scholarship for the duration of pre-university studies in a local public institution \nThe scholarship covers tuition fees, living and other related allowances \nStructured development interventions are provided to accelerate personal, academic and professional development \nWhat courses and institutions are covered?\n\nAll foundation and matriculation courses that are offered by local public institutions* \n\n \n\n*Maximum of 1 year \n\nHow many scholars will be selected each year?\n\nThere are a fixed number of scholarships provided every year. \n\nWho can apply?\n\nThis programme is only open to students who fulfil these four criteria: \n\nMalaysian citizens \nAged 19 and below \nFrom families with a monthly combined household income of RM4,850 and below \nHave a minimum of 6As* in Sijil Pelajaran Malaysia (SPM)\n*Includes grades of A+, A, or A- for any subjects. \n\n \n\nTerms & conditions: \n\n1) This selection criteria only applies to the year 2026. \n\n \n\n2) This scholarship is not applicable to employees (including family members) of Axiata Group Berhad or any of its subsidiaries and key associates. \n\n \n\n3) Subsidiaries include Axiata Digital Labs, ADA, Boost Holdings, EDOTCO, XL Axiata, Dialog, Robi, Smart, Ncell, and key associates include CelcomDigi \n\nMore Enquiries?\n\nShare your details, and our representative will be in touch soon \n\nGet in Touch\n\nA Foundation For Advancement\nQUICK LINKS\nAbout Us\nEducation\nCommunity Investments\nEnvironment\nAxiata Foundation Brand Kit\nGET IN TOUCH\nContact Us\nAXIATA FOUNDATION\nLevel 27, Axiata Tower, 9 Jalan Stesen Sentral 5, Kuala Lumpur Wilayah Persekutuan, Kuala Lumpur 50470, Malaysia\n\n© Copyright 2026 Axiata Foundation [201101011216 (939346-X)]. All Rights Reserved.\n\nPrivacy Notice\nTerms of Use\nCookie Notice\nSitemap','https://www.axiata-foundation.com/education/equity-in-education-fund/application',NULL,'scraped','axiata',1,1,'2026-05-17 00:20:42','2026-05-31 01:16:39'),
(93,'PETRONAS Education Sponsorship Programme (PESP)','PETRONAS','PETRONAS Education Sponsorship Programme (PESP)','Global\n\nPerihal Kami\nBerita & Media\nKerjaya\nBekerjasama dengan Kami\nHubungi Kami\nMemajukan Tenaga\nKemampanan\nHubungan Pelaburan\nCerita Kami\n\nPelajar & Graduan\n\nMembentuk Generasi\nCemerlang Masa Depan\n\nKami merintis perjalanan demi kejayaan generasi baharu pemimpin dan pereka cipta. Inilah saatnya!\n\nProgram Pendidikan\n\nPETRONAS percaya pelaburan dalam pendidikan dan pembangunan modal insan pada semua peringkat akan melahirkan bakat yang mampan dan berterusan bagi PETRONAS, industri, dan juga negara.\n\nDalam merealisasikan matlamat perniagaannya, PETRONAS terus memberikan sumbangan kepada pembangunan mampan dengan meningkatkan akses kepada pendidikan berkualiti, menggalakkan aktiviti pembelajaran dan perkembangan serta merealisasikan aspirasi kerjaya mereka.\n\nPenajaan Pendidikan\nLatihan Industri\nSkim Peningkatan Kebolehpasaran Graduan (GEES)\nPendidikan dan Latihan Teknikal dan Vokasional (TVET)\n \n\n \n\nPenajaan Pendidikan (Lepasan SPM)\n\nProgram penajaan bagi pelajar SPM yang layak untuk melanjutkan pelajaran ke peringkat tinggi di Universiti Teknologi PETRONAS (UTP) dan universiti terkemuka tempatan dan luar negara di Amerika Syarikat, United Kingdom, Australia, New Zealand, China, Jepun, dan Korea Selatan.\n\n \n\n \n\n \n\nKriteria Kelayakan\n\n1. Pencapaian Akademik Cemerlang\n\nSPM tahun semasa\nProgram Tempatan: Memperoleh sekurang-kurangnya 8A SPM & CEFR (B1)\nProgram Luar Negara: Memperoleh sekurang-kurangnya 4A+ & 4A SPM & CEFR (C1)\n\nNota: Sila ambil maklum bahawa kriteria kelayakan untuk setiap permohonan adalah berbeza mengikut kepada disiplin yang dipilih (teknikal atau bukan teknikal). Untuk maklumat lebih lanjut, sila rujuk garis panduan permohonan penajaan pendidikan PETRONAS yang terdapat di sistem aplikasi dalam talian kami.\n\n2. Kekuatan Kepimpinan\n\nKami meneliti dengan lebih jauh daripada akademik dan mengambil kira impak yang pelajar lakukan di luar bilik darjah. Penyertaan dalam kelab, sukan, perkhidmatan komuniti, peranan kepimpinan, dan aktiviti lain mampu meningkatkan kebolehjayaan permohonan.\n\n3. Penilaian Holistik\n\nSebagai sebahagian daripada komitmen kami untuk memilih calon yang paling layak, kami telah merangka proses penilaian yang komprehensif untuk mendapatkan pemahaman yang lebih mendalam tentang kualiti dan keupayaan unik calon.\n\nPenilaian Personaliti & Kognitif (Penilaian atas talian)\nPenilaian ini bertujuan untuk menilai keupayaan pemikiran serta ciri-ciri personaliti semula jadi anda bagi memahami cara anda memproses maklumat, menyelesaikan masalah dan bertindak balas terhadap pelbagai situasi. Penilaian ini membantu mengenal pasti atribut utama yang sejajar dengan jangkaan serta keperluan seorang sarjana PETRONAS.\n\n\nPenilaian Kompetensi Kepimpinan (Penilaian atas talian)\nPenilaian ini memberi tumpuan kepada tingkah laku dan pengalaman yang ditunjukkan sepanjang pendidikan menengah anda. Ia bertujuan untuk menilai bagaimana anda telah menerapkan kualiti kepimpinan dalam konteks kehidupan sebenar dan sejauh mana kecekapan ini mencerminkan piawaian seorang sarjana PETRONAS.\n\n\nPenilaian Potensi Pelajar (Penilaian secara bersemuka)\nPenilaian ini adalah temu duga secara bersemuka di mana calon akan dinilai oleh penilai PETRONAS sendiri. Penilaian ini direka untuk mengukur kualiti kepimpinan dan tingkah laku calon.\n\n \n\nTerma & Syarat\n\nPenajaan pendidikan ini direka untuk individu yang mempamerkan bakat, dedikasi, dan potensi yang luar biasa.\n\nJawatankuasa pemilihan kami berkomited untuk mengenal pasti dan memupuk bakat yang paling unggul di kalangan kelompok pemohon. Dalam memilih bakat holistik yang layak, setiap permohonan dikaji dengan teliti, dengan mempertimbangkan aspek pencapaian akademik, karakter peribadi, potensi kepimpinan, dan impak terhadap komuniti.\n\nOleh kerana ianya sangat kompetitif, kami ingin menekankan bahawa hanya mereka yang memenuhi syarat akan disenarai pendek dan dipilih untuk pemilihan terakhir.\n\n \n\nTempoh Permohonan\n\nPermohonan tajaan dibuka bermula 31 Mac 2026 12:00 tengah hari sehingga 10 April 2026 at 5:00 petang.\n\n0\nMohon Sekarang\n\nTerokai Disiplin kami\n\nKejuruteraan Mekatronik\n\nKetahui selanjutnya\n\nKejuruteraan Mekanikal\n\nKetahui selanjutnya\n\nKejuruteraan Elektrik / Kejuruteraan Instrumentasi\n\nKetahui selanjutnya\n\nKejuruteraan Petroleum\n\nKetahui selanjutnya\n\nGeosains Petroleum / Geologi / Geofizik\n\nKetahui selanjutnya\n\nKejuruteraan Awam\n\nKetahui selanjutnya\n\nKejuruteraan Kimia\n\nKetahui selanjutnya\n\nKimia Gunaan\n\nKetahui selanjutnya\n\nKejuruteraan Marin\n\nKetahui selanjutnya\n\nKejuruteraan Perkapalan\n\nKetahui selanjutnya\n\nKejuruteraan Maritim\n\nKetahui selanjutnya\n\nKejuruteraan Perisian / Sains Komputer\n\nKetahui selanjutnya\n\nSains Data\n\nKetahui selanjutnya\n\nSains Aktuari (Risiko Kewangan)\n\nKetahui selanjutnya\n\nMatematik, Statistik & Ekonomi\n\nKetahui selanjutnya\n\nPemasaran\n\nKetahui selanjutnya\n\nEkonomi\n\nKetahui selanjutnya\n\nPerakaunan / Kewangan\n\nKetahui selanjutnya\n\nUndang-Undang\n\nLearn more\n\nPsikologi / Sosiologi (Tingkah Laku Organisasi)\n\nKetahui selanjutnya\n\nHarta Tanah & Pengurusan Hartanah\n\nKetahui selanjutnya\n\nSeni Bina\n\nKetahui selanjutnya\n\n \n\n \n\n \n\nPenajaan Pendidikan (Pendidikan Pertengahan)\n\nProgram penajaan pendidikan bagi pelajar Malaysia yang cemerlang dan sedang melanjutkan pengajian atau telah menerima tawaran bersyarat / tetap untuk menyambung pengajian ijazah pertama di universiti-universiti terpilih yang diluluskan oleh PETRONAS di United Kingdom.\n\n \n\n \n\nKriteria Kelayakan:\n\n\nPemohon mestilah warganegara Malaysia berumur TIDAK melebihi 22 tahun.\nPemohon TIDAK berada di bawah sebarang bentuk penajaan pendidikan lain.\nPemohon TIDAK mempunyai sebarang rekod kes disiplin atau pernah digantung pengajian.\n\n1. Pencapaian Akademik Cemerlang\n\nPelajar Ijazah:\nSedang mengikuti program ijazah di universiti yang diluluskan oleh PETRONAS di United Kingdom.\nMempunyai keputusan akademik terkini sekurang-kurangnya CGPA 3.70 atau setara dengan Ijazah Sarjana Muda Kelas Pertama.\nMempunyai sekurang-kurangnya satu tahun penuh pengajian yang masih berbaki.\n\n  ATAU\n\nPelajar Pra Universiti:\nTelah mendapat tawaran bersyarat atau penerimaan tetap bagi program pengajian di universiti yang diluluskan oleh PETRONAS di United Kingdom.\n\n2. Kekuatan Kepimpinan\n\nKami melihat lebih daripada pencapaian akademik. Penglibatan aktif dalam aktiviti kokurikulum seperti kelab, sukan, pertandingan, perkhidmatan komuniti dan peranan kepimpinan serta penglibatan dalam aktiviti lain boleh meningkatkan kebolehjayaan permohonan.\n\n3. Penilaian Holistik\n\nPenilaian Personaliti & Kognitif (Penilaian dalam talian)\nSebagai sebahagian daripada komitmen kami untuk memilih calon yang paling layak, kami telah merangka penilaian dalam talian yang komprehensif bagi memahami dengan lebih mendalam personaliti, keupayaan menyelesaikan masalah dan potensi kepimpinan anda, termasuk cara anda menangani cabaran dan pembelajaran melalui pengalaman sebenar.\nTemu duga Pertengahan Penajaan (MSI)\nTemu duga ini akan dijalankan secara dalam talian oleh panel penilai PETRONAS bagi menilai ciri kepimpinan, tingkah laku serta kesesuaian anda sebagai penerima tajaan PETRONAS.\n\n \n\nTerma & Syarat\n\nPenajaan pendidikan ini adalah khusus untuk pelajar yang menampilkan prestasi akademik yang cemerlang, sahsiah dan kepimpinan yang tinggi serta penglibatan dalam komuniti.\n\nSetiap permohonan akan dinilai dengan teliti bagi mengenal pasti calon yang paling berpotensi. Memandangkan ia adalah program yang sangat kompetitif, hanya pemohon yang memenuhi semua kriteria akan disenarai pendek dan dipertimbangkan untuk pemilihan akhir.\n\n \n\nTempoh Permohonan\n\nPermohonan untuk penajaan kini telah ditutup. Sila nantikan kemas kini terkini dan peluang permohonan akan datang.\n\n \n\n0\nApply now\n\n \n\n \n\nBiasiswa Chevening-PETRONAS\n\nBiasiswa Chevening-PETRONAS adalah sebahagian daripada inisiatif biasiswa global berprestij kerajaan United Kingdom (UK), yang dibiayai oleh Pejabat Luar Negeri, Komanwel dan Pembangunan serta PETRONAS. Program ini menawarkan peluang kepada dua pelajar Malaysia untuk me','https://www.petronas.com/bm/careers/students-graduates','2026-04-10','scraped','petronas',1,1,'2026-05-17 00:28:38','2026-05-17 00:28:38'),
(96,'Shell Malaysia Scholarship Programme','Shell Malaysia','Shell Malaysia Scholarship Programme','Scholarships\n\nSince its inception, the Shell Malaysia Scholarship Programme has benefitted more than 1500 students in pursuing their educational aspirations and provided a unique entrance into the energy industry.\n\nApplication opens on 31st March - 14th April 2026.\nShell Scholarship\n\nThe offering is for candidates to be sponsored by 2 tiers A-levels and undergraduates in prestigious colleges and universities locally and overseas. This is subject to the scholastic performance of the student.\n\nWe look to sponsor students intending to pursue higher education in the following fields of study:\n\nEngineering: Mechanical, Civil, Chemical, Petroleum, Electrical & Electronics, Environmental, Sustainable\nSciences: Geology, Geosciences, Data Science\nCommercial: Business & Management, Digital Marketing\nEligibility\nStrong leadership skills and active participation in extracurricular activities such as societies and clubs, sports, etc.\nNot presently holding other scholarships/loans\nConsistently strong academic achievements as follows:\nMinimum 8As (A/A+) in Sijil Pelajaran Malaysia (SPM) OR\nExcellent O-level grades at IGCSE (A*/A)\nBefore applying, ensure you are eligible to work in the country where the scholarship program is offered.\nMust be 2025 SPM or 2026 O-Level leavers\nWilling to pursue A-levels at selected boarding institution\nApplication Overview\n\nImportant information:\n\nApplication opens on 31st March - 14th April 2026.\nOnly shortlisted applicants will be notified to progress to the next steps of application.\n\nRead the transcript\nApplication Process\nApply\nStage 1\nStage 2\nMeet our Scholars\n\nFind out what life is like on the Shell MY Scholarship Programme from our previous scholars and now employees.\n\nRead their testimonials\n\nMore in Students and Graduates\nShell Graduate Programme\n\nDiscover the exciting and diverse world of Shell. See what you could do and where you could go.\n\nRead more\nAssessed Internships\n\nDiscover the exciting and diverse world of Shell. See what you could do and where you could go.\n\nRead more\nBenefits of working at Shell\nMeet our graduates\nTips to apply successfully','https://www.shell.com.my/about-us/careers/students-and-graduates/scholarships.html','2026-04-14','scraped','shell',1,1,'2026-05-17 00:39:06','2026-05-17 00:39:06'),
(99,'JPA Program Dermasiswa B40 (DB40)','Jabatan Perkhidmatan Awam (JPA)','JPA Program Dermasiswa B40 (DB40)','Program Dermasiswa B40 (DB40)\n Latihan Sebelum Perkhidmatan (LSP)\nUtama  Info Penajaan  Latihan Sebelum Perkhidmatan (LSP)  Program Pelajar  Program Dermasiswa B40 (DB40)\n\nProgram Dermasiswa B40 (DB40) ini diwujudkan bagi membantu pelajar-pelajar lepasan Sijil Pelajaran Malaysia (SPM) dalam kalangan keluarga berpendapatan rendah iaitu golongan B40 untuk melanjutkan pengajian ke peringkat yang lebih tinggi, khususnya di peringkat Diploma dan Diploma dalam bidang Pendidikan Teknikal dan Latihan Vokasional (TVET) yang telah mendapat pengiktirafan penuh atau sementara oleh Agensi Kelayakan Malaysia (MQA). \n\nBentuk Penajaan : Dermasiswa\n\nSyarat Umum\nSyarat Khusus\nInstitusi Pengajian\nBidang Pengajian','https://penajaan.jpa.gov.my/info-penajaan/latihan-sebelum-perkhidmatan/program-pelajar/program-dermasiswa-b40-db40.html',NULL,'scraped','jpa_db40',1,1,'2026-05-17 00:52:32','2026-05-17 00:52:32'),
(100,'JProgram Khas Lepasan Sijil Pelajaran Malaysia Dalam Negara (LSPM)','Jabatan Perkhidmatan Awam (JPA)','JProgram Khas Lepasan Sijil Pelajaran Malaysia Dalam Negara (LSPM)','Program Khas Lepasan Sijil Pelajaran Malaysia Dalam Negara (LSPM)\n Latihan Sebelum Perkhidmatan (LSP)\nUtama  Info Penajaan  Latihan Sebelum Perkhidmatan (LSP)  Program Pelajar  Program Khas Lepasan Sijil Pelajaran Malaysia Dalam Negara (LSPM)\n\nProgram Khas Lepasan Sijil Pelajaran Malaysia Dalam Negara (LSPM) merupakan penajaan Jabatan Perkhidmatan Awam (JPA) kepada pelajar terbaik lepasan Sijil Pelajaran Malaysia (SPM) untuk melanjutkan pengajian di dalam negara. Calon-calon akan ditawarkan penajaan secara berpakej untuk mengikuti pengajian peringkat Persediaan hingga ke Ijazah Pertama, tertakluk kepada syarat-syarat yang ditetapkan.\n\nBentuk penajaan : Pinjaman Boleh Ubah (PBU) Berasaskan Merit Akademik\n\nSyarat Umum\nSyarat Khusus\nInstitusi Pengajian\nBidang Pengajian','https://penajaan.jpa.gov.my/info-penajaan/latihan-sebelum-perkhidmatan/program-pelajar/program-khas-lepasan-sijil-pelajaran-malaysia-dalam-negara-lspm.html',NULL,'scraped','jpa_lspm',1,1,'2026-05-17 01:01:43','2026-05-17 01:01:43'),
(101,'Program Penajaan Nasional (PPN)','Jabatan Perkhidmatan Awam (JPA)','Program Penajaan Nasional (PPN)','Program Penajaan Nasional (PPN)\r\n Latihan Sebelum Perkhidmatan (LSP)\r\nUtama  Info Penajaan  Latihan Sebelum Perkhidmatan (LSP)  Program Pelajar  Program Penajaan Nasional (PPN)\r\n\r\nProgram Penajaan Nasional (PPN) merupakan penajaan kepada pelajar terbaik lepasan Sijil Pelajaran Malaysia (SPM) untuk melanjutkan pelajaran ke universiti terkemuka dunia yang dikenal pasti. Calon-calon yang terpilih akan ditawarkan penajaan secara berpakej bagi mengikuti pengajian peringkat Persediaan sehingga ke peringkat Ijazah Pertama, tertakluk kepada syarat-syarat yang ditetapkan. \r\n\r\nBentuk penajaan : Pinjaman Boleh Ubah (PBU) Berasaskan Merit Akademik\r\n\r\nSyarat Umum\r\nSyarat Khusus\r\nInstitusi Pengajian\r\nBidang Pengajian','https://penajaan.jpa.gov.my/info-penajaan/latihan-sebelum-perkhidmatan/program-pelajar/program-penajaan-nasional-ppn.html',NULL,'scraped','jpa_ppn',1,1,'2026-05-17 01:07:22','2026-05-16 17:41:05'),
(102,'biasiswa agung','athirah','aaa','aaa',NULL,NULL,'manual',NULL,1,1,'2026-05-16 18:07:57','2026-05-16 18:07:57');

/*Table structure for table `scraping_logs` */

DROP TABLE IF EXISTS `scraping_logs`;

CREATE TABLE `scraping_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source_website` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_scraped` int DEFAULT '0',
  `success_count` int DEFAULT '0',
  `failed_count` int DEFAULT '0',
  `status` enum('success','partial','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'success',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `started_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `website_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pages_to_scrape` int DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `duration_seconds` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `scraping_logs` */

insert  into `scraping_logs`(`id`,`source_website`,`total_scraped`,`success_count`,`failed_count`,`status`,`error_message`,`started_at`,`finished_at`,`created_at`,`website_url`,`pages_to_scrape`,`details`,`duration_seconds`) values 
(1,'jpa',3,3,0,'success',NULL,'2026-02-02 19:54:56','2026-02-02 19:55:10','2026-02-02 19:55:09',NULL,NULL,NULL,0),
(2,'jpa',3,3,0,'success',NULL,'2026-02-02 20:03:21','2026-02-02 20:03:36','2026-02-02 20:03:35',NULL,NULL,NULL,0),
(3,'jpa',3,3,0,'success',NULL,'2026-02-02 20:06:59','2026-02-02 20:07:10','2026-02-02 20:07:09',NULL,NULL,NULL,0),
(4,'yp',2,2,0,'success',NULL,'2026-02-02 20:17:37','2026-02-02 20:17:49','2026-02-02 20:17:49',NULL,NULL,NULL,0),
(5,'khazanah',3,3,0,'success',NULL,'2026-02-02 23:04:59','2026-02-02 23:05:15','2026-02-02 23:05:15',NULL,NULL,NULL,0),
(6,'bnm',2,2,0,'success',NULL,'2026-02-02 23:16:36','2026-02-02 23:16:49','2026-02-02 23:16:49',NULL,NULL,NULL,0),
(7,'petronas',1,1,0,'success',NULL,'2026-02-02 23:58:02','2026-02-02 23:58:13','2026-02-02 23:58:12',NULL,NULL,NULL,0),
(8,'shell',1,1,0,'success',NULL,'2026-02-03 00:00:54','2026-02-03 00:00:59','2026-02-03 00:00:59',NULL,NULL,NULL,0),
(9,'shell',1,1,0,'success',NULL,'2026-02-03 00:17:10','2026-02-03 00:17:18','2026-02-03 00:17:18',NULL,NULL,NULL,0),
(10,'mara',1,0,1,'failed',NULL,'2026-02-03 00:28:00','2026-02-03 00:28:08','2026-02-03 00:28:07',NULL,NULL,NULL,0),
(11,'petronas',1,1,0,'success',NULL,'2026-02-03 00:28:44','2026-02-03 00:28:51','2026-02-03 00:28:51',NULL,NULL,NULL,0),
(12,'mara',1,0,1,'failed',NULL,'2026-02-03 00:30:57','2026-02-03 00:31:15','2026-02-03 00:31:15',NULL,NULL,NULL,0),
(13,'mara',1,1,0,'success',NULL,'2026-02-03 00:32:49','2026-02-03 00:32:56','2026-02-03 00:32:56',NULL,NULL,NULL,0),
(14,'bpmb',1,1,0,'success',NULL,'2026-02-03 00:43:40','2026-02-03 00:43:49','2026-02-03 00:43:49',NULL,NULL,NULL,0),
(15,'axiata',1,1,0,'success',NULL,'2026-02-03 00:53:25','2026-02-03 00:53:34','2026-02-03 00:53:34',NULL,NULL,NULL,0),
(19,'jpa',3,3,0,'success',NULL,'2026-02-03 22:08:15','2026-02-03 22:08:29','2026-02-03 22:08:29',NULL,NULL,NULL,0),
(20,'jpa',3,3,0,'success',NULL,'2026-02-03 22:12:14','2026-02-03 22:12:24','2026-02-03 22:12:23',NULL,NULL,NULL,0),
(21,'khazanah',3,3,0,'success',NULL,'2026-02-03 22:16:49','2026-02-03 22:17:03','2026-02-03 22:17:03',NULL,NULL,NULL,0),
(22,'khazanah',3,3,0,'success',NULL,'2026-02-03 22:18:14','2026-02-03 22:18:29','2026-02-03 22:18:29',NULL,NULL,NULL,0),
(23,'petronas',1,1,0,'success',NULL,'2026-02-03 22:25:37','2026-02-03 22:25:48','2026-02-03 22:25:47',NULL,NULL,NULL,0),
(24,'mara',1,1,0,'success',NULL,'2026-02-03 22:26:56','2026-02-03 22:27:03','2026-02-03 22:27:03',NULL,NULL,NULL,0),
(25,'bnm',2,2,0,'success',NULL,'2026-02-03 22:28:38','2026-02-03 22:28:47','2026-02-03 22:28:47',NULL,NULL,NULL,0),
(26,'bpmb',1,1,0,'success',NULL,'2026-02-03 22:30:51','2026-02-03 22:31:00','2026-02-03 22:31:00',NULL,NULL,NULL,0),
(27,'axiata',1,1,0,'success',NULL,'2026-02-03 22:32:13','2026-02-03 22:32:18','2026-02-03 22:32:18',NULL,NULL,NULL,0),
(28,'shell',1,1,0,'success',NULL,'2026-02-03 22:33:34','2026-02-03 22:33:39','2026-02-03 22:33:39',NULL,NULL,NULL,0),
(29,'yp',2,2,0,'success',NULL,'2026-02-03 22:34:37','2026-02-03 22:34:46','2026-02-03 22:34:46',NULL,NULL,NULL,0),
(30,'jpa',3,3,0,'success',NULL,'2026-02-03 22:36:23','2026-02-03 22:36:34','2026-02-03 22:36:33',NULL,NULL,NULL,0),
(31,'khazanah',3,3,0,'success',NULL,'2026-02-03 22:36:36','2026-02-03 22:36:49','2026-02-03 22:36:49',NULL,NULL,NULL,0),
(32,'petronas',1,1,0,'success',NULL,'2026-02-03 22:36:50','2026-02-03 22:36:57','2026-02-03 22:36:57',NULL,NULL,NULL,0),
(33,'mara',1,1,0,'success',NULL,'2026-02-03 22:36:58','2026-02-03 22:37:05','2026-02-03 22:37:04',NULL,NULL,NULL,0),
(34,'bnm',2,2,0,'success',NULL,'2026-02-03 22:37:08','2026-02-03 22:37:17','2026-02-03 22:37:16',NULL,NULL,NULL,0),
(35,'bpmb',1,1,0,'success',NULL,'2026-02-03 22:37:18','2026-02-03 22:37:25','2026-02-03 22:37:24',NULL,NULL,NULL,0),
(36,'axiata',1,1,0,'success',NULL,'2026-02-03 22:37:27','2026-02-03 22:37:33','2026-02-03 22:37:33',NULL,NULL,NULL,0),
(37,'shell',1,1,0,'success',NULL,'2026-02-03 22:37:35','2026-02-03 22:37:40','2026-02-03 22:37:40',NULL,NULL,NULL,0),
(38,'yp',2,2,0,'success',NULL,'2026-02-03 22:37:42','2026-02-03 22:37:51','2026-02-03 22:37:51',NULL,NULL,NULL,0),
(39,'shell',1,1,0,'success',NULL,'2026-02-04 04:50:52','2026-02-04 04:51:01','2026-02-04 04:51:01',NULL,NULL,NULL,0),
(40,'shell',1,1,0,'success',NULL,'2026-02-04 14:00:21','2026-02-04 14:00:29','2026-02-04 14:00:29',NULL,NULL,NULL,0),
(41,'shell',1,1,0,'success',NULL,'2026-02-04 14:46:55','2026-02-04 14:47:02','2026-02-04 14:47:02',NULL,NULL,NULL,0),
(42,'jpa',3,3,0,'success',NULL,'2026-04-21 12:22:01','2026-04-21 12:22:14','2026-04-21 12:22:14',NULL,NULL,NULL,0),
(43,'axiata',1,1,0,'success',NULL,'2026-04-21 12:22:34','2026-04-21 12:22:40','2026-04-21 12:22:40',NULL,NULL,NULL,0),
(44,'bnm',2,2,0,'success',NULL,'2026-04-21 12:22:53','2026-04-21 12:23:02','2026-04-21 12:23:02',NULL,NULL,NULL,0),
(45,'bpmb',1,1,0,'success',NULL,'2026-04-21 12:23:21','2026-04-21 12:23:27','2026-04-21 12:23:27',NULL,NULL,NULL,0),
(46,'jpa',3,3,0,'success',NULL,'2026-04-21 12:23:37','2026-04-21 12:23:47','2026-04-21 12:23:46',NULL,NULL,NULL,0),
(47,'khazanah',3,3,0,'success',NULL,'2026-04-21 12:23:56','2026-04-21 12:24:08','2026-04-21 12:24:08',NULL,NULL,NULL,0),
(48,'mara',1,1,0,'success',NULL,'2026-04-21 12:24:37','2026-04-21 12:24:43','2026-04-21 12:24:43',NULL,NULL,NULL,0),
(49,'petronas',1,1,0,'success',NULL,'2026-04-21 12:24:50','2026-04-21 12:24:58','2026-04-21 12:24:57',NULL,NULL,NULL,0),
(50,'shell',1,1,0,'success',NULL,'2026-04-21 12:25:08','2026-04-21 12:25:12','2026-04-21 12:25:12',NULL,NULL,NULL,0),
(51,'yp',2,2,0,'success',NULL,'2026-04-21 12:25:20','2026-04-21 12:25:28','2026-04-21 12:25:28',NULL,NULL,NULL,0),
(52,'axiata',1,1,0,'success',NULL,'2026-04-27 14:49:24','2026-04-27 14:49:35','2026-04-27 14:49:35',NULL,NULL,NULL,0),
(53,'jpa',3,3,0,'success',NULL,'2026-04-27 15:00:40','2026-04-27 15:00:55','2026-04-27 15:00:54',NULL,NULL,NULL,0),
(54,'khazanah',3,3,0,'success',NULL,'2026-04-27 15:00:56','2026-04-27 15:01:12','2026-04-27 15:01:12',NULL,NULL,NULL,0),
(55,'petronas',1,1,0,'success',NULL,'2026-04-27 15:01:14','2026-04-27 15:01:22','2026-04-27 15:01:22',NULL,NULL,NULL,0),
(56,'mara',1,1,0,'success',NULL,'2026-04-27 15:01:24','2026-04-27 15:01:31','2026-04-27 15:01:30',NULL,NULL,NULL,0),
(57,'bnm',2,2,0,'success',NULL,'2026-04-27 15:01:32','2026-04-27 15:01:44','2026-04-27 15:01:44',NULL,NULL,NULL,0),
(58,'bpmb',1,1,0,'success',NULL,'2026-04-27 15:01:46','2026-04-27 15:01:53','2026-04-27 15:01:53',NULL,NULL,NULL,0),
(59,'axiata',1,1,0,'success',NULL,'2026-04-27 15:01:55','2026-04-27 15:02:01','2026-04-27 15:02:00',NULL,NULL,NULL,0),
(60,'shell',1,1,0,'success',NULL,'2026-04-27 15:02:02','2026-04-27 15:02:07','2026-04-27 15:02:07',NULL,NULL,NULL,0),
(61,'jpa',3,3,0,'success',NULL,'2026-04-27 15:01:58','2026-04-27 15:02:10','2026-04-27 15:02:09',NULL,NULL,NULL,0),
(62,'yp',2,2,0,'success',NULL,'2026-04-27 15:02:09','2026-04-27 15:02:18','2026-04-27 15:02:18',NULL,NULL,NULL,0),
(63,'khazanah',3,3,0,'success',NULL,'2026-04-27 15:02:11','2026-04-27 15:02:24','2026-04-27 15:02:23',NULL,NULL,NULL,0),
(64,'petronas',1,1,0,'success',NULL,'2026-04-27 15:02:25','2026-04-27 15:02:33','2026-04-27 15:02:33',NULL,NULL,NULL,0),
(65,'mara',1,1,0,'success',NULL,'2026-04-27 15:02:35','2026-04-27 15:02:42','2026-04-27 15:02:42',NULL,NULL,NULL,0),
(66,'bnm',2,2,0,'success',NULL,'2026-04-27 15:02:44','2026-04-27 15:02:53','2026-04-27 15:02:53',NULL,NULL,NULL,0),
(67,'bpmb',1,1,0,'success',NULL,'2026-04-27 15:02:55','2026-04-27 15:03:03','2026-04-27 15:03:03',NULL,NULL,NULL,0),
(68,'axiata',1,1,0,'success',NULL,'2026-04-27 15:03:05','2026-04-27 15:03:11','2026-04-27 15:03:10',NULL,NULL,NULL,0),
(69,'shell',1,1,0,'success',NULL,'2026-04-27 15:03:12','2026-04-27 15:03:17','2026-04-27 15:03:17',NULL,NULL,NULL,0),
(70,'yp',2,2,0,'success',NULL,'2026-04-27 15:03:19','2026-04-27 15:03:26','2026-04-27 15:03:26',NULL,NULL,NULL,0),
(71,'axiata',1,1,0,'success',NULL,'2026-05-15 21:33:32','2026-05-15 21:33:42','2026-05-15 21:33:42',NULL,NULL,NULL,0),
(72,'axiata',1,1,0,'success',NULL,'2026-05-15 21:34:41','2026-05-15 21:34:45','2026-05-15 21:34:45',NULL,NULL,NULL,0),
(73,'jpa',3,3,0,'success',NULL,'2026-05-15 21:35:46','2026-05-15 21:35:54','2026-05-15 21:35:54',NULL,NULL,NULL,0),
(74,'khazanah',3,3,0,'success',NULL,'2026-05-15 21:35:55','2026-05-15 21:36:08','2026-05-15 21:36:07',NULL,NULL,NULL,0),
(75,'petronas',1,1,0,'success',NULL,'2026-05-15 21:36:08','2026-05-15 21:36:15','2026-05-15 21:36:14',NULL,NULL,NULL,0),
(76,'mara',1,1,0,'success',NULL,'2026-05-15 21:36:16','2026-05-15 21:36:21','2026-05-15 21:36:21',NULL,NULL,NULL,0),
(77,'bnm',2,2,0,'success',NULL,'2026-05-15 21:36:22','2026-05-15 21:36:32','2026-05-15 21:36:31',NULL,NULL,NULL,0),
(78,'bpmb',1,1,0,'success',NULL,'2026-05-15 21:36:32','2026-05-15 21:36:38','2026-05-15 21:36:38',NULL,NULL,NULL,0),
(79,'axiata',1,1,0,'success',NULL,'2026-05-15 21:36:35','2026-05-15 21:36:39','2026-05-15 21:36:38',NULL,NULL,NULL,0),
(80,'axiata',1,1,0,'success',NULL,'2026-05-15 21:36:39','2026-05-15 21:36:43','2026-05-15 21:36:42',NULL,NULL,NULL,0),
(81,'shell',1,1,0,'success',NULL,'2026-05-15 21:36:44','2026-05-15 21:36:48','2026-05-15 21:36:47',NULL,NULL,NULL,0),
(82,'yp',2,2,0,'success',NULL,'2026-05-15 21:36:48','2026-05-15 21:36:56','2026-05-15 21:36:55',NULL,NULL,NULL,0),
(83,'axiata',1,1,0,'success',NULL,'2026-05-15 21:40:32','2026-05-15 21:40:36','2026-05-15 21:40:36',NULL,NULL,NULL,0),
(84,'axiata',1,1,0,'success',NULL,'2026-05-15 21:40:50','2026-05-15 21:40:54','2026-05-15 21:40:53',NULL,NULL,NULL,0),
(85,'axiata',1,1,0,'success',NULL,'2026-05-15 21:41:37','2026-05-15 21:41:41','2026-05-15 21:41:41',NULL,NULL,NULL,0),
(86,'axiata',1,1,0,'success',NULL,'2026-05-15 21:42:23','2026-05-15 21:42:27','2026-05-15 21:42:27',NULL,NULL,NULL,0),
(87,'axiata',1,1,0,'success',NULL,'2026-05-15 21:47:50','2026-05-15 21:47:56','2026-05-15 21:47:55',NULL,NULL,NULL,0),
(88,'axiata',1,0,1,'failed',NULL,'2026-05-15 22:22:22','2026-05-15 22:22:32','2026-05-15 22:22:31',NULL,NULL,NULL,0),
(89,'axiata',1,1,0,'success',NULL,'2026-05-15 22:23:53','2026-05-15 22:23:57','2026-05-15 22:23:57',NULL,NULL,NULL,0),
(90,'axiata',1,0,1,'failed',NULL,'2026-05-15 22:36:05','2026-05-15 22:36:13','2026-05-15 22:36:12',NULL,NULL,NULL,0),
(91,'axiata',1,1,0,'success',NULL,'2026-05-15 22:38:46','2026-05-15 22:38:51','2026-05-15 22:38:50',NULL,NULL,NULL,0),
(92,'axiata',1,1,0,'success',NULL,'2026-05-15 22:43:28','2026-05-15 22:43:32','2026-05-15 22:43:32',NULL,NULL,NULL,0),
(93,'axiata',1,1,0,'success',NULL,'2026-05-15 22:50:38','2026-05-15 22:50:46','2026-05-15 22:50:46',NULL,NULL,NULL,0),
(94,'jpa',3,3,0,'success',NULL,'2026-05-15 22:51:03','2026-05-15 22:51:12','2026-05-15 22:51:11',NULL,NULL,NULL,0),
(95,'khazanah',3,3,0,'success',NULL,'2026-05-15 22:51:13','2026-05-15 22:51:27','2026-05-15 22:51:26',NULL,NULL,NULL,0),
(96,'petronas',1,1,0,'success',NULL,'2026-05-15 22:51:28','2026-05-15 22:51:35','2026-05-15 22:51:34',NULL,NULL,NULL,0),
(97,'mara',1,1,0,'success',NULL,'2026-05-15 22:51:36','2026-05-15 22:51:41','2026-05-15 22:51:41',NULL,NULL,NULL,0),
(98,'bnm',2,2,0,'success',NULL,'2026-05-15 22:51:42','2026-05-15 22:51:54','2026-05-15 22:51:54',NULL,NULL,NULL,0),
(99,'axiata',1,1,0,'success',NULL,'2026-05-15 22:51:57','2026-05-15 22:52:01','2026-05-15 22:52:01',NULL,NULL,NULL,0),
(100,'bpmb',1,1,0,'success',NULL,'2026-05-15 22:51:55','2026-05-15 22:52:01','2026-05-15 22:52:01',NULL,NULL,NULL,0),
(101,'axiata',1,1,0,'success',NULL,'2026-05-15 22:52:02','2026-05-15 22:52:07','2026-05-15 22:52:06',NULL,NULL,NULL,0),
(102,'shell',1,1,0,'success',NULL,'2026-05-15 22:52:08','2026-05-15 22:52:12','2026-05-15 22:52:12',NULL,NULL,NULL,0),
(103,'yp',2,2,0,'success',NULL,'2026-05-15 22:52:13','2026-05-15 22:52:21','2026-05-15 22:52:21',NULL,NULL,NULL,0),
(104,'axiata',1,1,0,'success',NULL,'2026-05-15 23:06:37','2026-05-15 23:06:46','2026-05-15 23:06:45',NULL,NULL,NULL,0),
(105,'axiata',1,1,0,'success',NULL,'2026-05-15 23:09:17','2026-05-15 23:09:22','2026-05-15 23:09:21',NULL,NULL,NULL,0),
(106,'axiata',1,1,0,'success',NULL,'2026-05-15 23:14:01','2026-05-15 23:14:05','2026-05-15 23:14:05',NULL,NULL,NULL,0),
(107,'axiata',1,1,0,'success',NULL,'2026-05-15 23:16:30','2026-05-15 23:16:34','2026-05-15 23:16:33',NULL,NULL,NULL,0),
(108,'axiata',1,1,0,'success',NULL,'2026-05-15 23:16:51','2026-05-15 23:16:56','2026-05-15 23:16:55',NULL,NULL,NULL,0),
(109,'axiata',1,1,0,'success',NULL,'2026-05-15 23:17:21','2026-05-15 23:17:27','2026-05-15 23:17:26',NULL,NULL,NULL,0),
(110,'axiata',1,1,0,'success',NULL,'2026-05-15 23:18:37','2026-05-15 23:18:41','2026-05-15 23:18:41',NULL,NULL,NULL,0),
(111,'axiata',1,1,0,'success',NULL,'2026-05-15 23:22:19','2026-05-15 23:22:23','2026-05-15 23:22:23',NULL,NULL,NULL,0),
(112,'axiata',1,1,0,'success',NULL,'2026-05-15 23:22:38','2026-05-15 23:22:42','2026-05-15 23:22:42',NULL,NULL,NULL,0),
(113,'axiata',1,1,0,'success',NULL,'2026-05-15 23:23:05','2026-05-15 23:23:09','2026-05-15 23:23:08',NULL,NULL,NULL,0),
(114,'axiata',1,1,0,'success',NULL,'2026-05-15 23:25:04','2026-05-15 23:25:08','2026-05-15 23:25:08',NULL,NULL,NULL,0),
(115,'axiata',1,1,0,'success',NULL,'2026-05-15 23:25:28','2026-05-15 23:25:32','2026-05-15 23:25:31',NULL,NULL,NULL,0),
(116,'axiata',1,1,0,'success',NULL,'2026-05-15 23:25:47','2026-05-15 23:25:51','2026-05-15 23:25:51',NULL,NULL,NULL,0),
(117,'axiata',1,1,0,'success',NULL,'2026-05-15 23:27:17','2026-05-15 23:27:22','2026-05-15 23:27:22',NULL,NULL,NULL,0),
(118,'axiata',1,1,0,'success',NULL,'2026-05-15 23:27:35','2026-05-15 23:27:39','2026-05-15 23:27:38',NULL,NULL,NULL,0),
(119,'axiata',1,1,0,'success',NULL,'2026-05-15 23:28:38','2026-05-15 23:28:42','2026-05-15 23:28:42',NULL,NULL,NULL,0),
(120,'axiata',1,1,0,'success',NULL,'2026-05-15 23:28:54','2026-05-15 23:28:58','2026-05-15 23:28:58',NULL,NULL,NULL,0),
(121,'axiata',1,1,0,'success',NULL,'2026-05-15 23:29:46','2026-05-15 23:29:51','2026-05-15 23:29:50',NULL,NULL,NULL,0),
(122,'bnm',2,2,0,'success',NULL,'2026-05-16 02:18:00','2026-05-16 02:18:17','2026-05-16 02:18:16',NULL,NULL,NULL,0),
(123,'bnm',2,0,2,'failed',NULL,'2026-05-16 02:34:07','2026-05-16 02:34:18','2026-05-16 02:34:17',NULL,NULL,NULL,0),
(124,'bnm',2,0,2,'failed',NULL,'2026-05-16 02:38:10','2026-05-16 02:38:20','2026-05-16 02:38:19',NULL,NULL,NULL,0),
(125,'bnm',2,2,0,'success',NULL,'2026-05-16 02:39:47','2026-05-16 02:39:58','2026-05-16 02:39:57',NULL,NULL,NULL,0),
(126,'axiata',1,1,0,'success',NULL,'2026-05-16 02:41:50','2026-05-16 02:41:55','2026-05-16 02:41:55',NULL,NULL,NULL,0),
(127,'bnm',2,2,0,'success',NULL,'2026-05-16 02:44:32','2026-05-16 02:44:48','2026-05-16 02:44:47',NULL,NULL,NULL,0),
(128,'bnm',2,1,1,'partial',NULL,'2026-05-16 03:00:48','2026-05-16 03:01:26','2026-05-16 03:01:26',NULL,NULL,NULL,0),
(129,'bnm',2,2,0,'success',NULL,'2026-05-16 03:04:35','2026-05-16 03:04:47','2026-05-16 03:04:47',NULL,NULL,NULL,0),
(130,'bnm',2,2,0,'success',NULL,'2026-05-16 03:15:04','2026-05-16 03:15:22','2026-05-16 03:15:22',NULL,NULL,NULL,0),
(131,'bnm',1,1,0,'success',NULL,'2026-05-16 03:28:30','2026-05-16 03:28:42','2026-05-16 03:28:41',NULL,NULL,NULL,0),
(132,'bnm',1,1,0,'success',NULL,'2026-05-16 03:35:08','2026-05-16 03:35:15','2026-05-16 03:35:15',NULL,NULL,NULL,0),
(133,'bnm',1,1,0,'success',NULL,'2026-05-16 03:40:32','2026-05-16 03:40:38','2026-05-16 03:40:37',NULL,NULL,NULL,0),
(134,'bnm',1,1,0,'success',NULL,'2026-05-16 03:42:12','2026-05-16 03:42:16','2026-05-16 03:42:16',NULL,NULL,NULL,0),
(135,'bnm',1,1,0,'success',NULL,'2026-05-16 03:42:42','2026-05-16 03:42:46','2026-05-16 03:42:46',NULL,NULL,NULL,0),
(136,'bnm',1,1,0,'success',NULL,'2026-05-16 03:45:32','2026-05-16 03:45:37','2026-05-16 03:45:36',NULL,NULL,NULL,0),
(137,'bpmb',1,1,0,'success',NULL,'2026-05-16 16:43:06','2026-05-16 16:43:16','2026-05-16 16:43:16',NULL,NULL,NULL,0),
(138,'bpmb',1,0,1,'failed',NULL,'2026-05-16 17:05:45','2026-05-16 17:05:52','2026-05-16 17:05:51',NULL,NULL,NULL,0),
(139,'bpmb',1,1,0,'success',NULL,'2026-05-16 17:11:23','2026-05-16 17:11:28','2026-05-16 17:11:27',NULL,NULL,NULL,0),
(140,'bpmb',1,1,0,'success',NULL,'2026-05-16 17:16:46','2026-05-16 17:16:54','2026-05-16 17:16:53',NULL,NULL,NULL,0),
(141,'khazanah',3,3,0,'success',NULL,'2026-05-16 17:36:52','2026-05-16 17:37:24','2026-05-16 17:37:23',NULL,NULL,NULL,0),
(142,'khazanah',3,3,0,'success',NULL,'2026-05-16 17:42:02','2026-05-16 17:42:30','2026-05-16 17:42:30',NULL,NULL,NULL,0),
(143,'khazanah_watan',1,1,0,'success',NULL,'2026-05-16 17:53:52','2026-05-16 17:54:02','2026-05-16 17:54:01',NULL,NULL,NULL,0),
(144,'khazanah_watan',1,1,0,'success',NULL,'2026-05-16 17:59:32','2026-05-16 17:59:42','2026-05-16 17:59:41',NULL,NULL,NULL,0),
(145,'khazanah_watan',1,1,0,'success',NULL,'2026-05-16 18:00:19','2026-05-16 18:00:30','2026-05-16 18:00:29',NULL,NULL,NULL,0),
(146,'khazanah_watan',1,1,0,'success',NULL,'2026-05-16 18:03:52','2026-05-16 18:04:03','2026-05-16 18:04:02',NULL,NULL,NULL,0),
(147,'khazanah_watan',1,1,0,'success',NULL,'2026-05-16 18:07:19','2026-05-16 18:07:29','2026-05-16 18:07:29',NULL,NULL,NULL,0),
(148,'khazanah_watan',1,1,0,'success',NULL,'2026-05-16 18:11:21','2026-05-16 18:11:31','2026-05-16 18:11:31',NULL,NULL,NULL,0),
(149,'khazanah_watan',1,1,0,'success',NULL,'2026-05-16 18:11:50','2026-05-16 18:12:00','2026-05-16 18:12:00',NULL,NULL,NULL,0),
(150,'bpmb',1,1,0,'success',NULL,'2026-05-16 18:12:05','2026-05-16 18:12:10','2026-05-16 18:12:10',NULL,NULL,NULL,0),
(151,'khazanah_watan',1,1,0,'success',NULL,'2026-05-16 18:13:45','2026-05-16 18:13:55','2026-05-16 18:13:54',NULL,NULL,NULL,0),
(152,'khazanah_watan',1,1,0,'success',NULL,'2026-05-16 18:16:30','2026-05-16 18:16:40','2026-05-16 18:16:40',NULL,NULL,NULL,0),
(153,'khazanah_watan',1,0,1,'failed',NULL,'2026-05-16 18:26:34','2026-05-16 18:26:46','2026-05-16 18:26:46',NULL,NULL,NULL,0),
(154,'khazanah_watan',1,1,0,'success',NULL,'2026-05-16 18:28:16','2026-05-16 18:28:26','2026-05-16 18:28:26',NULL,NULL,NULL,0),
(155,'khazanah_paynet',1,1,0,'success',NULL,'2026-05-16 18:28:52','2026-05-16 18:29:05','2026-05-16 18:29:04',NULL,NULL,NULL,0),
(156,'khazanah_equity',1,1,0,'success',NULL,'2026-05-16 18:35:25','2026-05-16 18:35:38','2026-05-16 18:35:37',NULL,NULL,NULL,0),
(157,'mara',1,0,1,'failed',NULL,'2026-05-16 19:13:03','2026-05-16 19:13:13','2026-05-16 19:13:13',NULL,NULL,NULL,0),
(158,'mara',1,1,0,'success',NULL,'2026-05-16 19:15:40','2026-05-16 19:15:47','2026-05-16 19:15:46',NULL,NULL,NULL,0),
(159,'mara',1,1,0,'success',NULL,'2026-05-16 19:45:57','2026-05-16 19:46:06','2026-05-16 19:46:06',NULL,NULL,NULL,0),
(160,'mara',1,1,0,'success',NULL,'2026-05-16 19:47:58','2026-05-16 19:48:03','2026-05-16 19:48:03',NULL,NULL,NULL,0),
(161,'khazanah_watan',1,1,0,'success',NULL,'2026-05-16 19:48:13','2026-05-16 19:48:23','2026-05-16 19:48:23',NULL,NULL,NULL,0),
(162,'petronas',1,0,1,'failed',NULL,'2026-05-16 20:33:32','2026-05-16 20:33:44','2026-05-16 20:33:43',NULL,NULL,NULL,0),
(163,'petronas',1,1,0,'success',NULL,'2026-05-16 20:35:12','2026-05-16 20:35:19','2026-05-16 20:35:18',NULL,NULL,NULL,0),
(164,'petronas',1,1,0,'success',NULL,'2026-05-16 20:38:08','2026-05-16 20:38:14','2026-05-16 20:38:13',NULL,NULL,NULL,0),
(165,'petronas',1,0,1,'failed',NULL,'2026-05-16 20:38:27','2026-05-16 20:38:34','2026-05-16 20:38:33',NULL,NULL,NULL,0),
(166,'axiata',1,1,0,'success',NULL,'2026-05-16 20:39:03','2026-05-16 20:39:08','2026-05-16 20:39:07',NULL,NULL,NULL,0),
(167,'axiata',1,1,0,'success',NULL,'2026-05-16 20:44:33','2026-05-16 20:44:37','2026-05-16 20:44:36',NULL,NULL,NULL,0),
(168,'petronas',1,1,0,'success',NULL,'2026-05-16 20:44:39','2026-05-16 20:44:46','2026-05-16 20:44:45',NULL,NULL,NULL,0),
(169,'petronas',1,1,0,'success',NULL,'2026-05-16 20:49:26','2026-05-16 20:49:33','2026-05-16 20:49:32',NULL,NULL,NULL,0),
(170,'petronas',1,1,0,'success',NULL,'2026-05-16 20:53:35','2026-05-16 20:54:12','2026-05-16 20:54:12',NULL,NULL,NULL,0),
(171,'petronas',1,0,1,'failed',NULL,'2026-05-16 20:57:54','2026-05-16 20:58:00','2026-05-16 20:58:00',NULL,NULL,NULL,0),
(172,'petronas',1,1,0,'success',NULL,'2026-05-16 22:43:36','2026-05-16 22:43:46','2026-05-16 22:43:45',NULL,NULL,NULL,0),
(173,'petronas',1,1,0,'success',NULL,'2026-05-16 22:45:47','2026-05-16 22:45:54','2026-05-16 22:45:53',NULL,NULL,NULL,0),
(174,'shell',1,1,0,'success',NULL,'2026-05-16 22:51:28','2026-05-16 22:51:34','2026-05-16 22:51:33',NULL,NULL,NULL,0),
(175,'shell',1,1,0,'success',NULL,'2026-05-16 22:56:11','2026-05-16 22:56:17','2026-05-16 22:56:17',NULL,NULL,NULL,0),
(176,'jpa_db40',1,0,1,'failed',NULL,'2026-05-16 23:04:53','2026-05-16 23:05:03','2026-05-16 23:05:03',NULL,NULL,NULL,0),
(177,'jpa_db40',1,1,0,'success',NULL,'2026-05-16 23:06:00','2026-05-16 23:06:07','2026-05-16 23:06:07',NULL,NULL,NULL,0),
(178,'jpa_db40',1,0,1,'failed',NULL,'2026-05-16 23:14:17','2026-05-16 23:14:25','2026-05-16 23:14:24',NULL,NULL,NULL,0),
(179,'jpa_db40',1,1,0,'success',NULL,'2026-05-16 23:16:20','2026-05-16 23:16:27','2026-05-16 23:16:27',NULL,NULL,NULL,0),
(180,'jpa_db40',1,1,0,'success',NULL,'2026-05-16 23:26:45','2026-05-16 23:26:55','2026-05-16 23:26:55',NULL,NULL,NULL,0),
(181,'jpa_db40',1,1,0,'success',NULL,'2026-05-16 23:28:15','2026-05-16 23:28:22','2026-05-16 23:28:21',NULL,NULL,NULL,0),
(182,'petronas',1,1,0,'success',NULL,'2026-05-16 23:32:40','2026-05-16 23:32:46','2026-05-16 23:32:46',NULL,NULL,NULL,0),
(183,'petronas',1,0,1,'failed',NULL,'2026-05-16 23:35:24','2026-05-16 23:35:30','2026-05-16 23:35:30',NULL,NULL,NULL,0),
(184,'petronas',1,1,0,'success',NULL,'2026-05-16 23:36:51','2026-05-16 23:36:57','2026-05-16 23:36:57',NULL,NULL,NULL,0),
(185,'mara',1,1,0,'success',NULL,'2026-05-16 23:41:54','2026-05-16 23:42:00','2026-05-16 23:42:00',NULL,NULL,NULL,0),
(186,'petronas',1,0,1,'failed',NULL,'2026-05-16 23:42:57','2026-05-16 23:43:03','2026-05-16 23:43:03',NULL,NULL,NULL,0),
(187,'petronas',1,1,0,'success',NULL,'2026-05-16 23:44:55','2026-05-16 23:45:02','2026-05-16 23:45:02',NULL,NULL,NULL,0),
(188,'petronas',1,1,0,'success',NULL,'2026-05-16 23:48:08','2026-05-16 23:48:15','2026-05-16 23:48:15',NULL,NULL,NULL,0),
(189,'petronas',1,0,1,'failed',NULL,'2026-05-16 23:52:34','2026-05-16 23:52:41','2026-05-16 23:52:40',NULL,NULL,NULL,0),
(190,'petronas',1,1,0,'success',NULL,'2026-05-16 23:53:09','2026-05-16 23:53:15','2026-05-16 23:53:15',NULL,NULL,NULL,0),
(191,'axiata',1,1,0,'success',NULL,'2026-05-16 23:55:17','2026-05-16 23:55:21','2026-05-16 23:55:21',NULL,NULL,NULL,0),
(192,'petronas',1,1,0,'success',NULL,'2026-05-16 23:57:35','2026-05-16 23:57:42','2026-05-16 23:57:41',NULL,NULL,NULL,0),
(193,'petronas',1,1,0,'success',NULL,'2026-05-17 00:03:26','2026-05-17 00:03:36','2026-05-17 00:03:36',NULL,NULL,NULL,0),
(194,'petronas',1,0,1,'failed',NULL,'2026-05-17 00:04:22','2026-05-17 00:04:30','2026-05-17 00:04:30',NULL,NULL,NULL,0),
(195,'petronas',1,1,0,'success',NULL,'2026-05-17 00:08:26','2026-05-17 00:08:33','2026-05-17 00:08:32',NULL,NULL,NULL,0),
(196,'petronas',1,1,0,'success',NULL,'2026-05-17 00:10:15','2026-05-17 00:10:23','2026-05-17 00:10:22',NULL,NULL,NULL,0),
(197,'petronas',1,1,0,'success',NULL,'2026-05-17 00:16:13','2026-05-17 00:16:21','2026-05-17 00:16:21',NULL,NULL,NULL,0),
(198,'axiata',1,1,0,'success',NULL,'2026-05-17 00:20:38','2026-05-17 00:20:43','2026-05-17 00:20:42',NULL,NULL,NULL,0),
(199,'petronas',1,0,1,'failed',NULL,'2026-05-17 00:23:43','2026-05-17 00:23:49','2026-05-17 00:23:49',NULL,NULL,NULL,0),
(200,'petronas',1,1,0,'success',NULL,'2026-05-17 00:25:45','2026-05-17 00:25:58','2026-05-17 00:25:57',NULL,NULL,NULL,0),
(201,'shell',1,0,1,'failed',NULL,'2026-05-17 00:27:22','2026-05-17 00:27:28','2026-05-17 00:27:27',NULL,NULL,NULL,0),
(202,'petronas',1,1,0,'success',NULL,'2026-05-17 00:28:34','2026-05-17 00:28:39','2026-05-17 00:28:38',NULL,NULL,NULL,0),
(203,'shell',1,0,1,'failed',NULL,'2026-05-17 00:29:17','2026-05-17 00:29:24','2026-05-17 00:29:23',NULL,NULL,NULL,0),
(204,'shell',1,1,0,'success',NULL,'2026-05-17 00:31:11','2026-05-17 00:31:19','2026-05-17 00:31:18',NULL,NULL,NULL,0),
(205,'shell',1,1,0,'success',NULL,'2026-05-17 00:35:55','2026-05-17 00:36:01','2026-05-17 00:36:01',NULL,NULL,NULL,0),
(206,'shell',1,0,1,'failed',NULL,'2026-05-17 00:36:28','2026-05-17 00:36:34','2026-05-17 00:36:33',NULL,NULL,NULL,0),
(207,'shell',1,1,0,'success',NULL,'2026-05-17 00:39:03','2026-05-17 00:39:07','2026-05-17 00:39:07',NULL,NULL,NULL,0),
(208,'shell',1,1,0,'success',NULL,'2026-05-17 00:40:50','2026-05-17 00:40:53','2026-05-17 00:40:53',NULL,NULL,NULL,0),
(209,'jpa_db40',1,0,1,'failed',NULL,'2026-05-17 00:41:09','2026-05-17 00:41:16','2026-05-17 00:41:15',NULL,NULL,NULL,0),
(210,'jpa_db40',1,1,0,'success',NULL,'2026-05-17 00:43:19','2026-05-17 00:43:23','2026-05-17 00:43:23',NULL,NULL,NULL,0),
(211,'jpa_db40',1,1,0,'success',NULL,'2026-05-17 00:44:00','2026-05-17 00:44:04','2026-05-17 00:44:04',NULL,NULL,NULL,0),
(212,'jpa_db40',1,1,0,'success',NULL,'2026-05-17 00:52:26','2026-05-17 00:52:33','2026-05-17 00:52:32',NULL,NULL,NULL,0),
(213,'jpa_lspm',1,1,0,'success',NULL,'2026-05-17 01:01:39','2026-05-17 01:01:43','2026-05-17 01:01:43',NULL,NULL,NULL,0),
(214,'jpa_ppn',1,1,0,'success',NULL,'2026-05-17 01:07:19','2026-05-17 01:07:23','2026-05-17 01:07:23',NULL,NULL,NULL,0),
(215,'jpa_db40',1,1,0,'success',NULL,'2026-05-17 01:46:46','2026-05-17 01:46:54','2026-05-17 01:46:54',NULL,NULL,NULL,0),
(216,'axiata',1,1,0,'success',NULL,'2026-05-31 01:16:31','2026-05-31 01:16:40','2026-05-31 01:16:40',NULL,NULL,NULL,0),
(217,'khazanah_watan',1,1,0,'success',NULL,'2026-05-31 01:33:11','2026-05-31 01:33:29','2026-05-31 01:33:29',NULL,NULL,NULL,0),
(218,'axiata',1,1,0,'success',NULL,'2026-05-31 01:34:01','2026-05-31 01:34:05','2026-05-31 01:34:04',NULL,NULL,NULL,0);

/*Table structure for table `user_profiles` */

DROP TABLE IF EXISTS `user_profiles`;

CREATE TABLE `user_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `spm_results` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_as` int DEFAULT NULL COMMENT 'Total number of A grades from SPM',
  `bumiputera` tinyint(1) DEFAULT '0' COMMENT 'Is Bumiputera (1=Yes, 0=No)',
  `age` int DEFAULT NULL COMMENT 'User age',
  `gender` enum('Male','Female') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User gender',
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User state of origin',
  `has_leadership` tinyint(1) DEFAULT '0' COMMENT 'Leadership experience',
  `monthly_income` decimal(10,2) DEFAULT NULL,
  `income_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `study_level` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_of_study` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `citizenship` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_profiles_user_id_foreign` (`user_id`),
  CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `user_profiles` */

insert  into `user_profiles`(`id`,`user_id`,`spm_results`,`created_at`,`updated_at`,`total_as`,`bumiputera`,`age`,`gender`,`state`,`has_leadership`,`monthly_income`,`income_category`,`study_level`,`field_of_study`,`citizenship`) values 
(7,12,'{\"FIZIK\": \"A\", \"KIMIA\": \"A\", \"SEJARAH\": \"A\", \"MATEMATIK\": \"B\", \"BAHASA MELAYU\": \"A+\", \"BAHASA INGGERIS\": \"A\"}','2026-04-19 16:33:32','2026-05-12 06:32:14',5,1,22,'Female','terengganu',0,NULL,NULL,NULL,NULL,NULL),
(8,27,'{\"FIZIK\": \"A\", \"KIMIA\": \"A\", \"SEJARAH\": \"A\", \"MATEMATIK\": \"B\", \"BAHASA MELAYU\": \"A+\", \"BAHASA INGGERIS\": \"A\"}','2026-05-11 14:53:37','2026-05-11 15:20:02',5,0,20,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),
(9,28,NULL,'2026-05-17 14:30:55','2026-05-17 16:55:43',4,1,19,'Male','Terengganu',1,5351.00,'B40','Foundation','Accounting','Malaysia');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`email_verified_at`,`password`,`remember_token`,`created_at`,`updated_at`,`role`) values 
(1,'Pablo','admin@gmail.com','2026-04-19 22:27:40','$2y$12$MPcfcZvlxIAvSUv4BONicOOjYAFlzl1udDqKKN45UHpGixSnoLIDC','ii9iOBuXcU7GXNZsxCbyzQ1gv5HCeVK2PfuYnRjJp6dhfm4cBOt43ETwjRWN','2026-01-24 04:55:15','2026-01-24 04:55:15','admin'),
(12,'athi','athirahzahari13@gmail.com','2026-04-19 15:03:02','$2y$12$rK6D3MsMa/tWQhs47wfij.aNHHyNcLmCGPQqk2A.hRfhIvAULbEi2',NULL,'2026-04-19 14:59:45','2026-04-19 15:03:02','user'),
(25,'hhh','athi@gmail.com','2026-05-01 18:53:52','$2y$12$dcCTqTmWo8DdzAn32h8Oj.f39G3z3NW9cu.bnAUIvsBtyqlrJ8uha',NULL,'2026-05-01 18:53:52','2026-05-01 18:53:52','user'),
(26,'hhhh','syaf@gmail.com','2026-05-02 08:21:15','$2y$12$v5w/HlKap6yRxyLFDQKhZ.4vTNYfFjYRTGezaRgM7uS/37SP86/Yu',NULL,'2026-05-02 08:21:15','2026-05-02 08:21:15','user'),
(27,'athi','abc@gmail.com','2026-05-11 14:36:14','$2y$12$yl/9jZVkYmqdCY8OwFYSresvAcz/el01/EzU4CyoLrFviM5E/bH7a',NULL,'2026-05-11 14:36:14','2026-05-11 14:36:14','user'),
(28,'skyzed','2023485872@student.uitm.edu.my',NULL,'$2y$12$7AcKUi46ZxlCXO90ueukQOV8./SHsvwZ3R6ahj50EZp1.EHbyhz7K',NULL,'2026-05-15 12:47:00','2026-05-15 12:47:00','user');

/* Procedure structure for procedure `filter_scholarships` */

/*!50003 DROP PROCEDURE IF EXISTS  `filter_scholarships` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `filter_scholarships`(
    IN p_total_as INT,
    IN p_income_category VARCHAR(10),
    IN p_study_path VARCHAR(10),
    IN p_bumiputera BOOLEAN
)
BEGIN
    SELECT DISTINCT s.id
    FROM scholarships s
    JOIN scholarship_eligibility_criteria ec
        ON ec.scholarship_id = s.id
    WHERE s.is_active = 1

      -- Academic filter
      AND (
          ec.min_spm_as IS NULL
          OR p_total_as >= ec.min_spm_as
      )

      -- Income filter
      AND (
          ec.income_categories IS NULL
          OR JSON_CONTAINS(ec.income_categories, JSON_QUOTE(p_income_category))
      )

      -- Study path filter
      AND (
          ec.study_paths IS NULL
          OR JSON_CONTAINS(ec.study_paths, JSON_QUOTE(p_study_path))
      )

      -- Bumiputera filter
      AND (
          ec.bumiputera_required = 0
          OR p_bumiputera = 1
      );
END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
