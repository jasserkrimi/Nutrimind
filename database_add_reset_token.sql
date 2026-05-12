-- Ajouter les colonnes pour le système de token
-- Exécutez ce SQL dans phpMyAdmin

ALTER TABLE `user` 
ADD COLUMN `reset_token` VARCHAR(255) NULL DEFAULT NULL AFTER `reset_code_expires`,
ADD COLUMN `reset_token_expires` DATETIME NULL DEFAULT NULL AFTER `reset_token`;

-- Ajouter un index pour améliorer les performances
ALTER TABLE `user` 
ADD INDEX `idx_reset_token` (`reset_token`);
