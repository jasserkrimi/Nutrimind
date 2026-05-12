-- Add password reset columns to user table
-- Run this SQL in your phpMyAdmin or MySQL client

ALTER TABLE `user` 
ADD COLUMN `reset_code` VARCHAR(6) NULL DEFAULT NULL AFTER `allergique`,
ADD COLUMN `reset_code_expires` DATETIME NULL DEFAULT NULL AFTER `reset_code`;

-- Add index for faster lookups
ALTER TABLE `user` 
ADD INDEX `idx_reset_code` (`reset_code`),
ADD INDEX `idx_email` (`email`);
