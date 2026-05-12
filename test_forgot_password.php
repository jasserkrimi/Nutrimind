<?php
/**
 * Test Script for Forgot Password Functionality
 * This will help diagnose any issues
 */

// Start session
session_start();

// Include required files
require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'config/Email.php';

echo "<h1>Test Forgot Password Functionality</h1>";
echo "<hr>";

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>";
try {
    $database = new Database();
    $db = $database->connect();
    if ($db) {
        echo "✅ <strong>SUCCESS:</strong> Database connection established<br>";
    } else {
        echo "❌ <strong>FAILED:</strong> Could not connect to database<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 2: User Model
echo "<h2>Test 2: User Model</h2>";
try {
    $user = new User($db);
    echo "✅ <strong>SUCCESS:</strong> User model instantiated<br>";
    
    // Test getUserByEmail
    $testEmail = "admin@nutrimind.com"; // Change this to an existing email
    $userData = $user->getUserByEmail($testEmail);
    
    if ($userData) {
        echo "✅ <strong>SUCCESS:</strong> Found user with email: $testEmail<br>";
        echo "User name: " . $userData['nom'] . "<br>";
    } else {
        echo "⚠️ <strong>WARNING:</strong> No user found with email: $testEmail<br>";
        echo "Please change the test email to an existing user<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 3: Generate Reset Code
echo "<h2>Test 3: Generate Reset Code</h2>";
try {
    if (isset($userData) && $userData) {
        $resetCode = $user->generateResetCode($testEmail);
        if ($resetCode) {
            echo "✅ <strong>SUCCESS:</strong> Reset code generated: <strong>$resetCode</strong><br>";
            echo "Code expires in 1 hour<br>";
        } else {
            echo "❌ <strong>FAILED:</strong> Could not generate reset code<br>";
        }
    } else {
        echo "⚠️ <strong>SKIPPED:</strong> No user to test with<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 4: Email Class
echo "<h2>Test 4: Email Class</h2>";
try {
    $emailService = new Email();
    echo "✅ <strong>SUCCESS:</strong> Email service instantiated<br>";
    
    // Check if mail function exists
    if (function_exists('mail')) {
        echo "✅ <strong>SUCCESS:</strong> PHP mail() function is available<br>";
    } else {
        echo "❌ <strong>FAILED:</strong> PHP mail() function is not available<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 5: Send Test Email (Optional - uncomment to test)
echo "<h2>Test 5: Send Test Email</h2>";
echo "<p><em>Uncomment the code below to test email sending</em></p>";

/*
if (isset($resetCode) && $resetCode && isset($userData) && $userData) {
    try {
        $emailSent = $emailService->sendPasswordResetCode($testEmail, $resetCode, $userData['nom']);
        
        if ($emailSent) {
            echo "✅ <strong>SUCCESS:</strong> Email sent to $testEmail<br>";
            echo "Check your inbox (and spam folder)<br>";
        } else {
            echo "❌ <strong>FAILED:</strong> Email could not be sent<br>";
            echo "This is normal on localhost without SMTP configuration<br>";
        }
    } catch (Exception $e) {
        echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
    }
}
*/

echo "<hr>";

// Summary
echo "<h2>Summary</h2>";
echo "<p>If all tests passed (except email sending on localhost), the forgot password functionality should work.</p>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ul>";
echo "<li>Go to: <a href='views/forgot_password.php'>Forgot Password Page</a></li>";
echo "<li>Enter your email: <strong>$testEmail</strong></li>";
echo "<li>You should receive a reset code (displayed on screen if email fails)</li>";
echo "</ul>";

echo "<hr>";
echo "<p><small>Test completed at: " . date('Y-m-d H:i:s') . "</small></p>";

// Styling
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
    h1 { color: #667eea; }
    h2 { color: #333; margin-top: 20px; }
    hr { margin: 20px 0; border: none; border-top: 2px solid #e0e0e0; }
    ul { line-height: 1.8; }
    a { color: #667eea; text-decoration: none; }
    a:hover { text-decoration: underline; }
</style>";
?>
