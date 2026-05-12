<?php
/**
 * PHPMailer Installation Script
 * This script downloads and installs PHPMailer automatically
 */

echo "<h1>📧 Installation de PHPMailer</h1>";
echo "<hr>";

// Create vendor directory if it doesn't exist
$vendorDir = __DIR__ . '/vendor';
if (!file_exists($vendorDir)) {
    mkdir($vendorDir, 0777, true);
    echo "✅ Dossier vendor créé<br>";
}

// PHPMailer directory
$phpmailerDir = $vendorDir . '/PHPMailer';

// Check if PHPMailer is already installed
if (file_exists($phpmailerDir . '/src/PHPMailer.php')) {
    echo "✅ <strong>PHPMailer est déjà installé!</strong><br>";
    echo "<p>Emplacement: <code>$phpmailerDir</code></p>";
    echo "<hr>";
    echo "<h2>✅ Installation terminée!</h2>";
    echo "<p>Vous pouvez maintenant:</p>";
    echo "<ol>";
    echo "<li>Configurer votre mot de passe Gmail dans <code>config/email_config.php</code></li>";
    echo "<li>Tester l'envoi d'email: <a href='test_email.php'>test_email.php</a></li>";
    echo "<li>Utiliser la réinitialisation de mot de passe: <a href='views/forgot_password.php'>forgot_password.php</a></li>";
    echo "</ol>";
    exit;
}

echo "<h2>📥 Téléchargement de PHPMailer...</h2>";

// Download PHPMailer from GitHub
$zipUrl = 'https://github.com/PHPMailer/PHPMailer/archive/refs/heads/master.zip';
$zipFile = $vendorDir . '/phpmailer.zip';

echo "URL: $zipUrl<br>";
echo "Téléchargement en cours...<br>";

// Download the file
$zipContent = @file_get_contents($zipUrl);

if ($zipContent === false) {
    echo "<p style='color: red;'>❌ <strong>Échec du téléchargement automatique</strong></p>";
    echo "<h3>Installation Manuelle:</h3>";
    echo "<ol>";
    echo "<li>Téléchargez: <a href='$zipUrl' target='_blank'>PHPMailer Master ZIP</a></li>";
    echo "<li>Extrayez le fichier ZIP</li>";
    echo "<li>Renommez le dossier en <code>PHPMailer</code></li>";
    echo "<li>Placez-le dans: <code>$vendorDir</code></li>";
    echo "</ol>";
    echo "<p>Structure finale:</p>";
    echo "<pre>";
    echo "vendor/\n";
    echo "└── PHPMailer/\n";
    echo "    └── src/\n";
    echo "        ├── PHPMailer.php\n";
    echo "        ├── SMTP.php\n";
    echo "        └── Exception.php\n";
    echo "</pre>";
    exit;
}

// Save the ZIP file
file_put_contents($zipFile, $zipContent);
echo "✅ Fichier téléchargé: " . round(filesize($zipFile) / 1024 / 1024, 2) . " MB<br>";

echo "<h2>📦 Extraction...</h2>";

// Extract the ZIP file
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($vendorDir);
    $zip->close();
    echo "✅ Fichiers extraits<br>";
    
    // Rename the extracted folder
    $extractedDir = $vendorDir . '/PHPMailer-master';
    if (file_exists($extractedDir)) {
        rename($extractedDir, $phpmailerDir);
        echo "✅ Dossier renommé en PHPMailer<br>";
    }
    
    // Delete the ZIP file
    unlink($zipFile);
    echo "✅ Fichier ZIP supprimé<br>";
    
    echo "<hr>";
    echo "<h2>✅ Installation réussie!</h2>";
    echo "<p>PHPMailer a été installé dans: <code>$phpmailerDir</code></p>";
    
    echo "<h3>Prochaines étapes:</h3>";
    echo "<ol>";
    echo "<li><strong>Créer un mot de passe d'application Gmail:</strong>";
    echo "<ul>";
    echo "<li>Allez sur: <a href='https://myaccount.google.com/security' target='_blank'>Google Account Security</a></li>";
    echo "<li>Activez la validation en deux étapes</li>";
    echo "<li>Créez un mot de passe d'application pour 'Nutrimind'</li>";
    echo "</ul>";
    echo "</li>";
    echo "<li><strong>Configurer le mot de passe:</strong>";
    echo "<ul>";
    echo "<li>Ouvrez: <code>config/email_config.php</code></li>";
    echo "<li>Remplacez <code>VOTRE_MOT_DE_PASSE_APPLICATION_ICI</code> par votre mot de passe d'application</li>";
    echo "<li>Vérifiez que <code>'enabled' => true</code></li>";
    echo "</ul>";
    echo "</li>";
    echo "<li><strong>Tester l'envoi d'email:</strong>";
    echo "<ul>";
    echo "<li><a href='test_email.php'>Tester l'envoi d'email</a></li>";
    echo "</ul>";
    echo "</li>";
    echo "</ol>";
    
    echo "<hr>";
    echo "<p><strong>Documentation complète:</strong> <a href='GMAIL_SMTP_SETUP.md'>GMAIL_SMTP_SETUP.md</a></p>";
    
} else {
    echo "<p style='color: red;'>❌ <strong>Échec de l'extraction</strong></p>";
    echo "<p>Veuillez installer manuellement (voir instructions ci-dessus)</p>";
}

echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
    h1 { color: #667eea; }
    h2 { color: #333; margin-top: 20px; }
    hr { margin: 20px 0; border: none; border-top: 2px solid #e0e0e0; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
    ul, ol { line-height: 1.8; }
    a { color: #667eea; text-decoration: none; }
    a:hover { text-decoration: underline; }
</style>";
?>
