<?php
// Generates 3 personalised AI plans (sport, sleep, nutrition) for a given objective.
session_start();
set_time_limit(300);
require_once '../controllers/ObjectiveController.php';
require_once '../config/Database.php';
require_once '../config/secrets.php';
require_once '../models/User.php';
require_once 'ai_helper.php';

if (!isset($_SESSION['user_id'])) { header('Location: auth.php'); exit; }

$objectif_id = (int)($_GET['objectif_id'] ?? 0);
if (!$objectif_id) { header('Location: objectif_list.php'); exit; }

// Step 1 (GET): show loading screen, then auto-POST back to this page.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    showLoadingScreen(
        "ai_planning.php?objectif_id={$objectif_id}",
        '🤖',
        'Génération de vos plans IA',
        "L'IA crée 3 plans personnalisés (sport, sommeil, nutrition) en français."
    );
    exit;
}

// Step 2 (POST): fetch data, call AI, save to session, redirect.
$objectif = (new ObjectiveController())->getById($objectif_id);
if (!$objectif || $objectif['user_id'] != $_SESSION['user_id']) {
    header('Location: objectif_list.php'); exit;
}

$db   = (new Database())->connect();
$user = (new User($db))->getUserById($_SESSION['user_id']);

$prompt = "Expert nutrition/sport. 3 plans EN FRANÇAIS.\n"
    . "Profil: {$user['age']}ans, {$user['poids']}kg, {$user['taille']}cm, allergique=" . ($user['allergique'] ? 'oui' : 'non') . "\n"
    . "Objectif: type={$objectif['type_objectif']}, cible={$objectif['valeur_cible']}, priorité={$objectif['niveau_priorite']}\n\n"
    . "JSON uniquement: {\"sport\":\"...\",\"sommeil\":\"...\",\"nutrition\":\"...\"}\n"
    . "Chaque valeur = plan détaillé en Markdown (##, listes à puces).";

$raw = askOllama($prompt, true);

if (!$raw) {
    $_SESSION['ai_error'] = "Erreur Ollama. Assurez-vous qu'Ollama tourne (start_ollama.bat).";
    header('Location: objectif_list.php'); exit;
}

$plans = json_decode($raw, true);
if (!$plans || !isset($plans['sport'])) {
    preg_match('/\{.*\}/s', $raw, $m);
    $plans = json_decode($m[0] ?? '{}', true) ?: [];
}

$_SESSION['ai_plans'] = [
    'sport'        => $plans['sport']     ?? $raw,
    'sommeil'      => $plans['sommeil']   ?? '',
    'nutrition'    => $plans['nutrition'] ?? '',
    'objectif'     => $objectif['type_objectif'],
    'user_nom'     => $user['nom'] ?? '',
    'generated_at' => date('d/m/Y H:i'),
];

header('Location: ai_sport.php'); exit;
