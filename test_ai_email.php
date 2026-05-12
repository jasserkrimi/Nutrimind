<?php
/**
 * Test AI Email Generation
 * 
 * This script tests the Groq AI API integration
 * Run: http://localhost/nutrimind/test_ai_email.php
 */

require_once 'config/Database.php';
require_once 'config/AIEmailGenerator.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test AI Email Generation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
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
        .email-preview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #dee2e6;
        }
        .email-subject {
            font-weight: bold;
            font-size: 18px;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .email-body {
            line-height: 1.6;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .loader {
            text-align: center;
            padding: 20px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🤖 Test AI Email Generation</h1>";

try {
    // Get a test user from database
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT * FROM user WHERE role = 'user' LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $testUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$testUser) {
        throw new Exception("No test user found in database. Please create a user first.");
    }
    
    echo "<div class='info'>
            <strong>📊 Test User:</strong><br>
            Name: {$testUser['nom']}<br>
            Email: {$testUser['email']}<br>
            Role: {$testUser['role']}
          </div>";
    
    echo "<div class='loader'>
            <div class='spinner'></div>
            <p>Generating AI email... This may take 2-5 seconds...</p>
          </div>";
    
    // Flush output to show loading
    ob_flush();
    flush();
    
    // Generate AI email
    $aiGenerator = new AIEmailGenerator();
    $result = $aiGenerator->generateEmail($testUser, 'welcome', 'friendly');
    
    // Hide loader with JavaScript
    echo "<script>document.querySelector('.loader').style.display = 'none';</script>";
    
    if ($result['success']) {
        echo "<div class='success'>
                <h3>✅ AI Email Generated Successfully!</h3>
                <p>The Groq AI API is working correctly.</p>
              </div>";
        
        echo "<div class='email-preview'>
                <div class='email-subject'>
                    📧 Subject: {$result['subject']}
                </div>
                <div class='email-body'>
                    {$result['body']}
                </div>
              </div>";
        
        echo "<div class='info'>
                <h4>🎯 What This Means:</h4>
                <ul>
                    <li>✅ Groq AI API is connected and working</li>
                    <li>✅ Email generation is functional</li>
                    <li>✅ Personalization is working (user data included)</li>
                    <li>✅ You can now use the AI Email Campaign system</li>
                </ul>
              </div>";
        
        echo "<div class='success'>
                <h4>🚀 Next Steps:</h4>
                <ol>
                    <li>Run the database migration: <code>run_email_campaigns_migration.php</code></li>
                    <li>Go to the Users page: <code>views/backoffice/users.php</code></li>
                    <li>Click on \"AI Email Campaign System\"</li>
                    <li>Select a segment and generate emails</li>
                </ol>
              </div>";
        
    } else {
        echo "<div class='error'>
                <h3>❌ AI Email Generation Failed</h3>
                <p><strong>Error:</strong> {$result['error']}</p>";
        
        if (isset($result['response'])) {
            echo "<p><strong>API Response:</strong></p>";
            echo "<pre>" . htmlspecialchars(print_r($result['response'], true)) . "</pre>";
        }
        
        if (isset($result['raw_content'])) {
            echo "<p><strong>Raw Content:</strong></p>";
            echo "<pre>" . htmlspecialchars($result['raw_content']) . "</pre>";
        }
        
        echo "</div>";
        
        echo "<div class='info'>
                <h4>🔧 Troubleshooting:</h4>
                <ul>
                    <li>Check your internet connection</li>
                    <li>Verify the Groq API key is correct</li>
                    <li>Check if Groq API is experiencing downtime</li>
                    <li>Try again in a few seconds</li>
                </ul>
              </div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>
            <h3>❌ Test Failed</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
          </div>";
}

echo "    </div>
</body>
</html>";
?>
