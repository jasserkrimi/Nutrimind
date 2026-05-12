<?php
/**
 * Database Migration Script for Password Reset Feature
 * Run this file once to add password reset columns to the user table
 */

require_once 'config/Database.php';

echo "Starting database migration for password reset feature...\n\n";

try {
    $database = new Database();
    $db = $database->connect();
    
    // Check if columns already exist
    $checkQuery = "SHOW COLUMNS FROM `user` LIKE 'reset_code'";
    $stmt = $db->query($checkQuery);
    
    if ($stmt->rowCount() > 0) {
        echo "✓ Password reset columns already exist. No migration needed.\n";
        exit;
    }
    
    // Add reset_code column
    echo "Adding reset_code column...\n";
    $db->exec("ALTER TABLE `user` ADD COLUMN `reset_code` VARCHAR(6) NULL DEFAULT NULL AFTER `allergique`");
    echo "✓ reset_code column added successfully.\n";
    
    // Add reset_code_expires column
    echo "Adding reset_code_expires column...\n";
    $db->exec("ALTER TABLE `user` ADD COLUMN `reset_code_expires` DATETIME NULL DEFAULT NULL AFTER `reset_code`");
    echo "✓ reset_code_expires column added successfully.\n";
    
    // Add indexes for better performance
    echo "Adding indexes...\n";
    $db->exec("ALTER TABLE `user` ADD INDEX `idx_reset_code` (`reset_code`)");
    echo "✓ Index on reset_code added successfully.\n";
    
    // Check if email index already exists
    $checkIndexQuery = "SHOW INDEX FROM `user` WHERE Key_name = 'idx_email'";
    $stmt = $db->query($checkIndexQuery);
    
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE `user` ADD INDEX `idx_email` (`email`)");
        echo "✓ Index on email added successfully.\n";
    } else {
        echo "✓ Index on email already exists.\n";
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "\nYou can now use the password reset feature:\n";
    echo "- Forgot Password: http://localhost/nutrimind/views/forgot_password.php\n";
    echo "- Reset Password: http://localhost/nutrimind/views/reset_password.php\n";
    
} catch (PDOException $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "\nPlease run the SQL manually from database_update_password_reset.sql\n";
}
?>
