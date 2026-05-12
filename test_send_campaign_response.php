<?php
/**
 * Test de la réponse du controller pour l'envoi de campagne
 */

session_start();

// Simuler une session admin
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';

// Simuler une requête POST
$_POST['action'] = 'send_email_campaign';
$_POST['user_id'] = 1; // Remplacez par un ID utilisateur valide
$_POST['subject'] = 'Test Email Campaign';
$_POST['body'] = '<h2>Test</h2><p>Ceci est un test d\'envoi de campagne.</p>';
$_POST['template_name'] = 'test';
$_POST['ai_generated'] = 1;

// Inclure le controller
require_once 'controllers/UserController.php';

$controller = new UserController();
$response = $controller->sendEmailCampaign();

echo "<h1>🧪 Test Réponse Controller</h1>";
echo "<hr>";

echo "<h2>📊 Réponse JSON:</h2>";
echo "<pre style='background: #f4f4f4; padding: 15px; border-radius: 5px;'>";
echo json_encode($response, JSON_PRETTY_PRINT);
echo "</pre>";

echo "<h2>✅ Analyse:</h2>";
if ($response['success']) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;'>";
    echo "<strong>SUCCESS = TRUE</strong><br>";
    echo "Message: " . ($response['message'] ?? 'Pas de message') . "<br>";
    echo "User Email: " . ($response['user_email'] ?? 'N/A') . "<br>";
    echo "User Name: " . ($response['user_name'] ?? 'N/A');
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545;'>";
    echo "<strong>SUCCESS = FALSE</strong><br>";
    echo "Error: " . ($response['error'] ?? 'Pas d\'erreur spécifiée') . "<br>";
    if (isset($response['errors'])) {
        echo "Errors: " . implode(', ', $response['errors']);
    }
    echo "</div>";
}

echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
    h1 { color: #667eea; }
    h2 { color: #333; margin-top: 20px; }
    hr { margin: 20px 0; border: none; border-top: 2px solid #e0e0e0; }
</style>";
?>
