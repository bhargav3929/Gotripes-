-- SQL Script to create tbl_UAEActivities table
-- Import this file in Hostinger phpMyAdmin

CREATE TABLE IF NOT EXISTS `tbl_UAEActivities` (
  `activityID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `createdBy` varchar(255) DEFAULT NULL,
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifiedBy` varchar(255) DEFAULT NULL,
  `modifiedDate` timestamp NULL DEFAULT NULL,
  `activityName` varchar(255) NOT NULL,
  `activityLocation` varchar(255) NOT NULL,
  `activityImage` varchar(255) NOT NULL,
  `activityCurrency` varchar(255) NOT NULL DEFAULT '$',
  `activityPrice` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `activityDescription` text DEFAULT NULL,
  `activityDuration` varchar(255) DEFAULT NULL,
  `activityInclusions` text DEFAULT NULL,
  `activityExclusions` text DEFAULT NULL,
  `activityTerms` text DEFAULT NULL,
  `emirates_id` bigint(20) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`activityID`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraint if tbl_emirates table exists
-- ALTER TABLE `tbl_UAEActivities` 
-- ADD CONSTRAINT `tbl_uaeactivities_emirates_id_foreign` 
-- FOREIGN KEY (`emirates_id`) REFERENCES `tbl_emirates` (`id`) ON DELETE SET NULL;
