<?php
/**
 * Email Test Script
 * Use this to test if your email configuration is working
 */

require_once 'config/Email.php';

// Get email from URL parameter or use default
$testEmail = isset($_GET['email']) ? $_GET['email'] : 'test@example.com';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email - Nutrimind</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #667eea;
            margin-top: 0;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="email"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info h3 {
            margin-top: 0;
        }
        .info ul {
            margin: 10px 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Test d'Envoi d'Email</h1>
        
        <div class="info">
            <h3>ℹ️ Instructions:</h3>
            <ul>
                <li>Entrez votre adresse email</li>
                <li>Cliquez sur "Envoyer un email de test"</li>
                <li>Vérifiez votre boîte de réception (et les spams)</li>
            </ul>
        </div>

        <form method="POST" id="testForm">
            <div class="form-group">
                <label for="email">Adresse Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($testEmail); ?>" required>
            </div>
            
            <button type="submit" class="btn">Envoyer un Email de Test</button>
        </form>

        <div id="result" class="result"></div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailService = new Email();
                
                // Test with a sample reset code
                $testCode = '123456';
                $result = $emailService->sendPasswordResetCode($email, $testCode, 'Utilisateur Test');
                
                if ($result) {
                    echo "<script>
                        document.getElementById('result').className = 'result success';
                        document.getElementById('result').style.display = 'block';
                        document.getElementById('result').innerHTML = '<strong>✅ Succès!</strong><br>Un email de test a été envoyé à <strong>$email</strong>.<br>Vérifiez votre boîte de réception (et les spams).';
                    </script>";
                } else {
                    echo "<script>
                        document.getElementById('result').className = 'result error';
                        document.getElementById('result').style.display = 'block';
                        document.getElementById('result').innerHTML = '<strong>❌ Échec!</strong><br>L\\'email n\\'a pas pu être envoyé.<br><br><strong>Solutions possibles:</strong><ul style=\"text-align: left; margin-top: 10px;\"><li>Vérifiez la configuration SMTP dans config/email_config.php</li><li>Vérifiez que votre serveur peut envoyer des emails</li><li>Consultez EMAIL_SETUP_GUIDE.md pour plus d\\'aide</li></ul>';
                    </script>";
                }
            } else {
                echo "<script>
                    document.getElementById('result').className = 'result error';
                    document.getElementById('result').style.display = 'block';
                    document.getElementById('result').innerHTML = '<strong>❌ Erreur!</strong><br>Adresse email invalide.';
                </script>";
            }
        }
        ?>

        <div style="margin-top: 30px; text-align: center; color: #666; font-size: 14px;">
            <p><strong>Note:</strong> Sur localhost (XAMPP/WAMP), l'envoi d'emails peut ne pas fonctionner sans configuration SMTP.</p>
            <p>Consultez <a href="EMAIL_SETUP_GUIDE.md" style="color: #667eea;">EMAIL_SETUP_GUIDE.md</a> pour la configuration.</p>
        </div>
    </div>
</body>
</html>
