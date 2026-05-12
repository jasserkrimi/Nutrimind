<?php
/**
 * Script de test pour la configuration SMTP
 * Utilisez ce script pour vérifier que votre configuration SMTP fonctionne
 */

require_once 'config/EmailSMTP.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test SMTP - Nutrimind</title>
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
            text-align: center;
        }
        .btn:hover {
            opacity: 0.9;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Test Configuration SMTP</h1>";

try {
    // Charger la configuration
    $configFile = __DIR__ . '/config/smtp_config.php';
    
    if (!file_exists($configFile)) {
        throw new Exception("Fichier de configuration SMTP introuvable!");
    }
    
    $config = require $configFile;
    
    echo "<div class='info'>
            <h3>📋 Configuration actuelle:</h3>
            <ul>
                <li><strong>Serveur SMTP:</strong> {$config['smtp_host']}</li>
                <li><strong>Port:</strong> {$config['smtp_port']}</li>
                <li><strong>Sécurité:</strong> {$config['smtp_secure']}</li>
                <li><strong>Utilisateur:</strong> {$config['smtp_username']}</li>
                <li><strong>Expéditeur:</strong> {$config['from_email']} ({$config['from_name']})</li>
            </ul>
          </div>";
    
    // Vérifier si la configuration est par défaut
    if ($config['smtp_username'] === 'votre-email@gmail.com' || 
        $config['smtp_password'] === 'votre-mot-de-passe-app') {
        
        echo "<div class='warning'>
                <h3>⚠️ Configuration par défaut détectée</h3>
                <p>Vous devez modifier le fichier <code>config/smtp_config.php</code> avec vos propres identifiants SMTP.</p>
                <h4>Pour Gmail:</h4>
                <ol>
                    <li>Allez sur <a href='https://myaccount.google.com/security' target='_blank'>https://myaccount.google.com/security</a></li>
                    <li>Activez la \"Validation en 2 étapes\"</li>
                    <li>Allez dans \"Mots de passe des applications\"</li>
                    <li>Sélectionnez \"Autre (nom personnalisé)\" et entrez \"Nutrimind\"</li>
                    <li>Copiez le mot de passe généré (16 caractères)</li>
                    <li>Collez-le dans <code>smtp_password</code> dans le fichier de configuration</li>
                </ol>
              </div>";
        
        echo "<div style='text-align: center;'>
                <a href='views/forgot_password.php' class='btn'>Aller à la page de test →</a>
              </div>";
        
    } else {
        // Tester l'envoi d'email
        echo "<div class='info'>
                <h3>📧 Test d'envoi d'email...</h3>
                <p>Envoi d'un email de test à <strong>{$config['smtp_username']}</strong></p>
              </div>";
        
        $emailService = new EmailSMTP();
        
        // Test avec un token fictif
        $testToken = bin2hex(random_bytes(32));
        $result = $emailService->sendPasswordResetToken(
            $config['smtp_username'], 
            $testToken, 
            'Utilisateur Test'
        );
        
        if ($result) {
            echo "<div class='success'>
                    <h3>✅ Email envoyé avec succès!</h3>
                    <p>Vérifiez votre boîte de réception (et les spams) à l'adresse: <strong>{$config['smtp_username']}</strong></p>
                    <p>Le système d'envoi d'emails SMTP fonctionne correctement!</p>
                  </div>";
            
            echo "<div class='info'>
                    <h3>🎯 Prochaines étapes:</h3>
                    <ol>
                        <li>Testez la fonctionnalité \"Mot de passe oublié\" sur votre site</li>
                        <li>Allez sur <a href='views/forgot_password.php'>views/forgot_password.php</a></li>
                        <li>Entrez votre email et demandez un lien de réinitialisation</li>
                        <li>Vérifiez votre email et cliquez sur le lien</li>
                    </ol>
                  </div>";
            
        } else {
            echo "<div class='error'>
                    <h3>❌ Échec de l'envoi</h3>
                    <p>L'email n'a pas pu être envoyé. Vérifiez:</p>
                    <ul>
                        <li>Vos identifiants SMTP sont corrects</li>
                        <li>Vous utilisez un mot de passe d'application (pas votre mot de passe Gmail normal)</li>
                        <li>La validation en 2 étapes est activée sur votre compte Gmail</li>
                        <li>Votre connexion internet fonctionne</li>
                    </ul>
                  </div>";
        }
        
        echo "<div style='text-align: center;'>
                <a href='views/forgot_password.php' class='btn'>Tester la réinitialisation →</a>
              </div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>
            <h3>❌ Erreur</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
          </div>";
}

echo "    </div>
</body>
</html>";
?>
