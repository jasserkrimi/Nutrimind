<?php
/**
 * Run Email Campaigns Database Migration
 * 
 * This script creates the email_campaigns and user_segments tables
 * Run this file once by accessing: http://localhost/nutrimind/run_email_campaigns_migration.php
 */

require_once 'config/Database.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Email Campaigns Migration</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        h1 {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #17a2b8;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .step-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Email Campaigns Migration</h1>";

try {
    $database = new Database();
    $db = $database->connect();
    
    echo "<div class='info'>📊 Connected to database successfully!</div>";
    
    // Read SQL file
    $sqlFile = __DIR__ . '/database_email_campaigns.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: database_email_campaigns.sql");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    echo "<div class='step'>
            <div class='step-title'>📝 Executing SQL Statements...</div>";
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        try {
            $db->exec($statement);
            $successCount++;
            
            // Extract table name from CREATE TABLE statement
            if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                echo "<div class='success'>✅ Created table: <code>{$matches[1]}</code></div>";
            } elseif (preg_match('/CREATE INDEX.*?`?(\w+)`?/i', $statement, $matches)) {
                echo "<div class='success'>✅ Created index: <code>{$matches[1]}</code></div>";
            }
        } catch (PDOException $e) {
            $errorCount++;
            // Check if error is "table already exists"
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "<div class='info'>ℹ️ Table/Index already exists (skipped)</div>";
            } else {
                echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
    
    echo "</div>";
    
    if ($errorCount === 0) {
        echo "<div class='success'>
                <h3>✅ Migration Completed Successfully!</h3>
                <p>All database tables have been created:</p>
                <ul>
                    <li><code>email_campaigns</code> - Stores email campaign history</li>
                    <li><code>user_segments</code> - Tracks user segmentation</li>
                </ul>
              </div>";
        
        echo "<div class='info'>
                <h3>🎯 Next Steps:</h3>
                <ol>
                    <li>Go to the Users page in your admin panel</li>
                    <li>Click on the new <strong>\"AI Email Campaign\"</strong> button</li>
                    <li>Select users and generate personalized emails with AI</li>
                    <li>Send emails individually or in bulk</li>
                </ol>
              </div>";
        
        echo "<div style='text-align: center;'>
                <a href='views/backoffice/users.php' class='btn'>Go to Users Page →</a>
              </div>";
    } else {
        echo "<div class='error'>
                <h3>⚠️ Migration completed with some errors</h3>
                <p>Please check the errors above and try again if needed.</p>
              </div>";
    }
    
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
