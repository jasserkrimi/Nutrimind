<?php
// Generates a full AI report covering all of the user's objectives.
session_start();
set_time_limit(300);
require_once '../controllers/ObjectiveController.php';
require_once '../config/Database.php';
require_once '../config/secrets.php';
require_once '../models/User.php';
require_once 'ai_helper.php';

if (!isset($_SESSION['user_id'])) { header('Location: auth.php'); exit; }

// Step 1 (GET): show loading screen, then auto-POST back to this page.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    showLoadingScreen(
        'ai_report.php',
        '📊',
        'Génération du rapport IA',
        "L'IA analyse vos objectifs et génère un rapport personnalisé.",
        '#6366f1',
        'linear-gradient(135deg,#6366f1,#4f46e5)'
    );
    exit;
}

// Step 2 (POST): fetch data, call AI, save to session, redirect.
$objectives = (new ObjectiveController())->getAllForUser($_SESSION['user_id']);
$db         = (new Database())->connect();
$user       = (new User($db))->getUserById($_SESSION['user_id']);

// Summarise objectives for the prompt
$objLines = '';
foreach ($objectives as $i => $obj) {
    $objLines .= "- Obj" . ($i + 1) . ": {$obj['type_objectif']}, cible={$obj['valeur_cible']}, statut={$obj['statut']}\n";
}
if (!$objLines) $objLines = "Aucun objectif.";

$prompt = "Expert nutrition. Rapport EN FRANÇAIS pour {$user['nom']} ({$user['age']}ans, {$user['poids']}kg).\n"
    . "Objectifs:\n{$objLines}\n"
    . "Génère un rapport Markdown avec: ## Résumé, ## Analyse, ## Recommandations, ## Conclusion.";

$content = askOllama($prompt);

if (!$content) {
    $_SESSION['ai_report_error'] = "Erreur Ollama. Assurez-vous qu'Ollama tourne (start_ollama.bat).";
    header('Location: objectif_list.php'); exit;
}

$_SESSION['ai_report'] = [
    'content'      => $content,
    'user_nom'     => $user['nom'] ?? '',
    'total_obj'    => count($objectives),
    'generated_at' => date('d/m/Y H:i'),
];

header('Location: ai_report_view.php'); exit;
