<?php
/**
 * Simple Email Test Script
 */

require_once 'config/EmailSimple.php';

$testEmail = 'rahoui.amine23@gmail.com';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Simple - Nutrimind</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #667eea;
            margin-top: 0;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
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
            border-radius: 5px;
            margin-bottom: 20px;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Test d'Envoi d'Email</h1>
        
        <div class="info">
            <strong>ℹ️ Information:</strong><br>
            Ce script teste l'envoi d'email à: <strong><?php echo $testEmail; ?></strong><br><br>
            <strong>Avant de tester, assurez-vous que:</strong>
            <ul>
                <li>Vous avez créé un mot de passe d'application Gmail</li>
                <li>Le mot de passe est configuré dans <code>config/email_config.php</code></li>
                <li><code>'enabled' => true</code> dans le fichier de configuration</li>
            </ul>
        </div>

        <form method="POST">
            <button type="submit" name="test" class="btn">🚀 Tester l'Envoi d'Email</button>
        </form>

        <?php
        if (isset($_POST['test'])) {
            echo "<div class='result ";
            
            try {
                $emailService = new EmailSimple();
                $result = $emailService->testEmail($testEmail);
                
                if ($result) {
                    echo "success'>";
                    echo "<strong>✅ Succès!</strong><br>";
                    echo "Un email de test a été envoyé à <strong>$testEmail</strong><br>";
                    echo "Vérifiez votre boîte de réception (et les spams).<br><br>";
                    echo "<small>Si vous ne recevez pas l'email, vérifiez la configuration dans config/email_config.php</small>";
                } else {
                    echo "error'>";
                    echo "<strong>❌ Échec!</strong><br>";
                    echo "L'email n'a pas pu être envoyé.<br><br>";
                    echo "<strong>Vérifiez:</strong><br>";
                    echo "<ul>";
                    echo "<li>Le mot de passe d'application Gmail est correct</li>";
                    echo "<li>Le fichier <code>config/email_config.php</code> est bien configuré</li>";
                    echo "<li><code>'enabled' => true</code> dans la configuration</li>";
                    echo "<li>La validation en deux étapes est activée sur Gmail</li>";
                    echo "</ul>";
                    echo "<br><strong>Guide de configuration:</strong> <a href='CONFIGURER_EMAIL_MAINTENANT.md'>CONFIGURER_EMAIL_MAINTENANT.md</a>";
                }
            } catch (Exception $e) {
                echo "error'>";
                echo "<strong>❌ Erreur!</strong><br>";
                echo "Message: " . $e->getMessage() . "<br><br>";
                echo "Consultez le guide: <a href='CONFIGURER_EMAIL_MAINTENANT.md'>CONFIGURER_EMAIL_MAINTENANT.md</a>";
            }
            
            echo "</div>";
        }
        ?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <h3>📚 Ressources:</h3>
            <ul>
                <li><a href="CONFIGURER_EMAIL_MAINTENANT.md">Guide de Configuration (2 minutes)</a></li>
                <li><a href="views/forgot_password.php">Tester la Réinitialisation de Mot de Passe</a></li>
                <li><a href="https://myaccount.google.com/apppasswords" target="_blank">Créer un Mot de Passe d'Application Gmail</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
