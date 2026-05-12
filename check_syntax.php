<?php
echo "<h1>Vérification de la syntaxe de users.php</h1>";

$file = 'views/backoffice/users.php';

// Vérifier si le fichier existe
if (!file_exists($file)) {
    echo "<p style='color: red;'>❌ Le fichier n'existe pas!</p>";
    exit;
}

echo "<p>✅ Le fichier existe</p>";
echo "<p>Taille: " . filesize($file) . " bytes</p>";

// Essayer de vérifier la syntaxe
$output = [];
$return_var = 0;
exec("php -l $file 2>&1", $output, $return_var);

echo "<h2>Résultat de la vérification:</h2>";
echo "<pre>";
echo implode("\n", $output);
echo "</pre>";

if ($return_var === 0) {
    echo "<p style='color: green;'>✅ Pas d'erreur de syntaxe PHP</p>";
} else {
    echo "<p style='color: red;'>❌ Erreur de syntaxe détectée!</p>";
}

// Essayer de charger le fichier pour voir s'il y a des erreurs
echo "<h2>Test de chargement:</h2>";
echo "<p>Tentative d'inclusion du fichier...</p>";

ob_start();
try {
    // Ne pas exécuter, juste parser
    $content = file_get_contents($file);
    echo "<p style='color: green;'>✅ Fichier lu avec succès</p>";
    echo "<p>Nombre de lignes: " . substr_count($content, "\n") . "</p>";
    
    // Chercher des erreurs JavaScript potentielles
    if (preg_match('/`[^`]*$/', $content)) {
        echo "<p style='color: orange;'>⚠️ Template literal potentiellement non fermé détecté</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}
ob_end_clean();

echo "<hr>";
echo "<p><a href='views/backoffice/users.php'>Essayer d'ouvrir users.php</a></p>";
?>
