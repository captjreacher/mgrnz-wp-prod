-- Create the blueprint subscriptions table with ai_submission_id field
-- Run this in phpMyAdmin, Adminer, or your database tool

CREATE TABLE IF NOT EXISTS `wp_mgrnz_blueprint_subscriptions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subscription_type` varchar(50) NOT NULL DEFAULT 'blueprint_download',
  `blueprint_id` bigint(20) DEFAULT NULL,
  `ai_submission_id` varchar(255) DEFAULT NULL,
  `subscribed_at` datetime NOT NULL,
  `download_count` int(11) DEFAULT 0,
  `last_download_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_blueprint_id` (`blueprint_id`),
  KEY `idx_ai_submission_id` (`ai_submission_id`),
  KEY `idx_subscribed_at` (`subscribed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
