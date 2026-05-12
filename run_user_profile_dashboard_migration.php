<?php
/**
 * User Profile Dashboard Migration
 * Run: http://localhost/nutrimind/run_user_profile_dashboard_migration.php
 */

require_once 'config/Database.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>User Profile Dashboard Migration</title>
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
        <h1>📊 User Profile Dashboard Migration</h1>";

try {
    $database = new Database();
    $db = $database->connect();
    
    echo "<div class='info'>📊 Connected to database successfully!</div>";
    
    $sqlFile = __DIR__ . '/database_user_profile_dashboard.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found");
    }
    
    $sql = file_get_contents($sqlFile);
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    foreach ($statements as $statement) {
        try {
            $db->exec($statement);
            if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                echo "<div class='success'>✅ Created table: <code>{$matches[1]}</code></div>";
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "<div class='info'>ℹ️ Table already exists (skipped)</div>";
            } else {
                echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
    
    echo "<div class='success'>
            <h3>✅ Migration Completed Successfully!</h3>
            <p>User Profile Dashboard is now ready!</p>
          </div>";
    
    echo "<div class='info'>
            <h3>🎯 New Features:</h3>
            <ul>
                <li>📝 Add private notes to users</li>
                <li>🏷️ Tag users with custom labels</li>
                <li>📊 View user activity timeline</li>
                <li>📧 Track email communication history</li>
                <li>📈 See user health progress</li>
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
