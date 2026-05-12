<?php
/**
 * Test d'envoi d'email de campagne avec SMTP
 */

require_once 'config/EmailSMTP.php';

echo "<h1>🧪 Test Email de Campagne</h1>";
echo "<hr>";

// Créer une instance de EmailSMTP
$emailSender = new EmailSMTP();

// Données de test
$toEmail = 'aminebglisc@gmail.com'; // Votre email
$toName = 'Amine Test';
$subject = 'Test Email de Campagne - Nutrimind';
$body = '
<h2>Bienvenue sur Nutrimind!</h2>
<p>Ceci est un email de test pour vérifier que le système d\'envoi de campagnes fonctionne correctement.</p>
<p><strong>Fonctionnalités testées:</strong></p>
<ul>
    <li>✅ Envoi via SMTP Gmail</li>
    <li>✅ Template HTML professionnel</li>
    <li>✅ Personnalisation avec nom d\'utilisateur</li>
    <li>✅ Intégration avec EmailCampaignManager</li>
</ul>
<p>Si vous recevez cet email, le système fonctionne parfaitement!</p>
<p style="margin-top: 30px;">
    <a href="http://localhost/nutrimind" style="display: inline-block; padding: 12px 30px; background-color: #1161ee; color: #ffffff; text-decoration: none; border-radius: 25px;">
        Accéder à Nutrimind
    </a>
</p>
';

echo "<h2>📧 Envoi de l'email de test...</h2>";
echo "<p><strong>À:</strong> $toEmail</p>";
echo "<p><strong>Sujet:</strong> $subject</p>";
echo "<hr>";

// Envoyer l'email
$result = $emailSender->sendEmail($toEmail, $toName, $subject, $body);

if ($result) {
    echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745;'>";
    echo "<h3>✅ Email envoyé avec succès!</h3>";
    echo "<p>Vérifiez votre boîte de réception: <strong>$toEmail</strong></p>";
    echo "<p>Le système d'envoi de campagnes fonctionne correctement!</p>";
    echo "</div>";
    
    echo "<h3>🎯 Prochaines étapes:</h3>";
    echo "<ol>";
    echo "<li>Vérifiez votre email (et les spams)</li>";
    echo "<li>Testez l'envoi depuis le backoffice: <a href='views/backoffice/users.php'>users.php</a></li>";
    echo "<li>Générez un email AI et cliquez sur 'Send Email'</li>";
    echo "</ol>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; border-left: 4px solid #dc3545;'>";
    echo "<h3>❌ Échec de l'envoi</h3>";
    echo "<p>L'email n'a pas pu être envoyé. Vérifiez:</p>";
    echo "<ul>";
    echo "<li>La configuration SMTP dans <code>config/smtp_config.php</code></li>";
    echo "<li>Votre connexion internet</li>";
    echo "<li>Les logs d'erreur PHP</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
    h1 { color: #667eea; }
    h2 { color: #333; margin-top: 20px; }
    hr { margin: 20px 0; border: none; border-top: 2px solid #e0e0e0; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    a { color: #667eea; text-decoration: none; }
    a:hover { text-decoration: underline; }
</style>";
?>
