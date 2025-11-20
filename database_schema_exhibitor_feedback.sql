-- SQL Schema for Exhibitor Feedback Table
-- Run this SQL directly in your database

CREATE TABLE IF NOT EXISTS `exhibitor_feedback` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Optional: user ID if logged in',
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  
  -- Ratings (1-5 scale)
  `event_rating` tinyint(1) UNSIGNED NOT NULL COMMENT 'Rating from 1 to 5',
  `portal_rating` tinyint(1) UNSIGNED NOT NULL COMMENT 'Rating from 1 to 5',
  `overall_experience_rating` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Rating from 1 to 5',
  
  -- Feedback text fields
  `what_liked_most` text DEFAULT NULL,
  `what_could_be_improved` text DEFAULT NULL,
  `additional_comments` text DEFAULT NULL,
  
  -- Recommendation
  `would_recommend` enum('yes','no','maybe') DEFAULT NULL,
  
  -- Additional feedback categories
  `event_organization_rating` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Rating from 1 to 5',
  `venue_rating` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Rating from 1 to 5',
  `networking_opportunities_rating` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Rating from 1 to 5',
  
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_email` (`email`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_exhibitor_feedback_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

