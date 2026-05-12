<?php
/**
 * Add User Status Column Migration
 * Run: http://localhost/nutrimind/run_user_status_migration.php
 */

require_once 'config/Database.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>User Status Migration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 { color: #667eea; text-align: center; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #17a2b8; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔒 User Status Migration</h1>";

try {
    $database = new Database();
    $db = $database->connect();
    
    echo "<div class='info'>📊 Connected to database successfully!</div>";
    
    // Check if column already exists
    $checkQuery = "SHOW COLUMNS FROM user LIKE 'status'";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "<div class='info'>ℹ️ Status column already exists. No migration needed.</div>";
    } else {
        // Add status column
        $sql = "ALTER TABLE user ADD COLUMN status ENUM('active', 'blocked') DEFAULT 'active' AFTER role";
        $db->exec($sql);
        echo "<div class='success'>✅ Added 'status' column to user table</div>";
        
        // Add index
        $sql = "CREATE INDEX idx_user_status ON user(status)";
        $db->exec($sql);
        echo "<div class='success'>✅ Created index on status column</div>";
    }
    
    echo "<div class='success'>
            <h3>✅ Migration Completed Successfully!</h3>
            <p>User blocking/unblocking feature is now ready!</p>
          </div>";
    
    echo "<div class='info'>
            <h3>🎯 What's New:</h3>
            <ul>
                <li>Admins can block/unblock user accounts</li>
                <li>Blocked users cannot login</li>
                <li>Blocked users see a message to contact support</li>
                <li>All existing users are set to 'active' by default</li>
            </ul>
          </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>
            <h3>❌ Migration Failed</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
          </div>";
}

echo "    </div>
</body>
</html>";
?>
