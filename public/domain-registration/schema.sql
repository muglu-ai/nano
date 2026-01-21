-- Domain Registration Form - Database Schema
-- Created for BTS Portal Domain Registration System

-- Create database (optional - uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS `bts_portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `bts_portal`;

-- Drop table if exists (for fresh installation)
DROP TABLE IF EXISTS `domain_registrations`;

-- Create domain_registrations table
CREATE TABLE `domain_registrations` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Full name of the registrant',
    `email` VARCHAR(255) NOT NULL COMMENT 'Email address',
    `mobile` VARCHAR(20) NOT NULL COMMENT 'Mobile number (10-15 digits)',
    `org` VARCHAR(255) DEFAULT NULL COMMENT 'Organization name (optional)',
    `designation` VARCHAR(255) DEFAULT NULL COMMENT 'Designation/Job title (optional)',
    `country` VARCHAR(100) NOT NULL COMMENT 'Country name',
    `domains` TEXT NOT NULL COMMENT 'Comma-separated list of selected domains',
    `user_ip` VARCHAR(45) NOT NULL COMMENT 'User IP address (supports IPv4 and IPv6)',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Registration timestamp',
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    PRIMARY KEY (`id`),
    INDEX `idx_email` (`email`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_country` (`country`),
    INDEX `idx_user_ip` (`user_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Domain registration form submissions';

-- Optional: Create a view for recent registrations
CREATE OR REPLACE VIEW `v_recent_registrations` AS
SELECT 
    id,
    name,
    email,
    mobile,
    org,
    designation,
    country,
    domains,
    user_ip,
    created_at,
    DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS formatted_date
FROM `domain_registrations`
ORDER BY created_at DESC;

-- Optional: Create a view for domain statistics
CREATE OR REPLACE VIEW `v_domain_statistics` AS
SELECT 
    domain_name,
    COUNT(*) AS registration_count
FROM (
    SELECT 
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(domains, ',', numbers.n), ',', -1)) AS domain_name
    FROM 
        domain_registrations
    CROSS JOIN (
        SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
    ) numbers
    WHERE 
        CHAR_LENGTH(domains) - CHAR_LENGTH(REPLACE(domains, ',', '')) >= numbers.n - 1
) AS domain_list
GROUP BY domain_name
ORDER BY registration_count DESC;
