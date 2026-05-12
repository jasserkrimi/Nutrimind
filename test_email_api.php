<?php
/**
 * Test Email API Configuration
 * 
 * Run this to test your email API setup
 * URL: http://localhost/nutrimind/test_email_api.php
 */

require_once 'config/EmailAPI.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Email API</title>
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
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #ffc107;
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
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📧 Test Email API</h1>";

try {
    // Load config
    $config = require 'config/email_api_config.php';
    
    echo "<div class='info'>
            <strong>📊 Current Configuration:</strong><br>
            Provider: <code>{$config['provider']}</code><br>
            From Email: <code>{$config['from_email']}</code><br>
            From Name: <code>{$config['from_name']}</code><br>
            API Key: <code>" . (empty($config['api_key']) ? '❌ NOT SET' : '✅ SET (' . substr($config['api_key'], 0, 10) . '...)') . "</code>
          </div>";
    
    if (empty($config['api_key'])) {
        echo "<div class='warning'>
                <h3>⚠️ API Key Not Configured</h3>
                <p>Please add your API key to <code>config/email_api_config.php</code></p>
                <p><strong>Steps:</strong></p>
                <ol>
                    <li>Open <code>config/email_api_config.php</code></li>
                    <li>Choose your provider (resend, sendgrid, or mailgun)</li>
                    <li>Add your API key</li>
                    <li>Save the file</li>
                    <li>Refresh this page</li>
                </ol>
              </div>";
        
        echo "<div class='info'>
                <h4>🚀 Recommended: Resend (Easiest)</h4>
                <ol>
                    <li>Go to: <a href='https://resend.com/signup' target='_blank'>https://resend.com/signup</a></li>
                    <li>Sign up (free)</li>
                    <li>Go to API Keys section</li>
                    <li>Create new API key</li>
                    <li>Copy and paste into config file</li>
                </ol>
                <p><strong>Free Tier:</strong> 100 emails/day, 3,000/month</p>
              </div>";
    } else {
        echo "<div class='info'>
                <p>🧪 Testing email API connection...</p>
              </div>";
        
        $emailAPI = new EmailAPI();
        
        // Send test email
        $testResult = $emailAPI->sendEmail(
            $config['from_email'],
            'Test User',
            '🧪 Test Email from Nutrimind',
            '<h1>Test Successful!</h1><p>Your email API is configured correctly and working.</p><p>You can now send emails from your Nutrimind application.</p>'
        );
        
        if ($testResult) {
            echo "<div class='success'>
                    <h3>✅ Email Sent Successfully!</h3>
                    <p>Check your inbox at <strong>{$config['from_email']}</strong></p>
                    <p>Your email API is working correctly!</p>
                  </div>";
            
            echo "<div class='success'>
                    <h4>🎉 Next Steps:</h4>
                    <ol>
                        <li>Go to the Users page</li>
                        <li>Use the AI Email Campaign system</li>
                        <li>Generate and send personalized emails</li>
                    </ol>
                    <a href='views/backoffice/users.php' class='btn'>Go to Users Page →</a>
                  </div>";
        } else {
            echo "<div class='error'>
                    <h3>❌ Email Sending Failed</h3>
                    <p>The API request failed. Please check:</p>
                    <ul>
                        <li>API key is correct</li>
                        <li>Provider is correct (resend, sendgrid, or mailgun)</li>
                        <li>From email is verified with your provider</li>
                        <li>Internet connection is working</li>
                    </ul>
                  </div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>
            <h3>❌ Error</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
          </div>";
}

echo "    </div>
</body>
</html>";
?>
