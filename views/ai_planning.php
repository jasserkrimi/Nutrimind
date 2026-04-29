<?php
session_start();
require_once '../controllers/ObjectiveController.php';
require_once '../config/Database.php';
require_once '../config/secrets.php';
require_once '../models/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$objectif_id = isset($_GET['objectif_id']) ? (int)$_GET['objectif_id'] : 0;
if (!$objectif_id) {
    header('Location: objectif_list.php');
    exit;
}

// ── Show loading screen on GET, process on POST ──────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Génération IA en cours…</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;min-height:100vh;font-family:'Segoe UI',sans-serif}
    .box{text-align:center;padding:3rem 2.5rem;background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(0,0,0,.2);max-width:440px;width:90%}
    .icon{font-size:3.5rem;margin-bottom:1rem;animation:pulse 1.5s ease-in-out infinite}
    @keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.15)}}
    .spinner{width:56px;height:56px;border:5px solid #e5e7eb;border-top-color:#6366f1;border-radius:50%;animation:spin .9s linear infinite;margin:0 auto 1.5rem}
    @keyframes spin{to{transform:rotate(360deg)}}
    h2{color:#1f2937;font-size:1.4rem;margin-bottom:.6rem}
    p{color:#6b7280;font-size:.93rem;line-height:1.6}
    .steps{display:flex;justify-content:center;gap:1rem;margin-top:1.5rem}
    .step{width:10px;height:10px;border-radius:50%;background:#e5e7eb;animation:blink 1.4s ease-in-out infinite}
    .step:nth-child(2){animation-delay:.2s}
    .step:nth-child(3){animation-delay:.4s}
    @keyframes blink{0%,80%,100%{background:#e5e7eb}40%{background:#6366f1}}
  </style>
</head>
<body>
  <div class="box">
    <div class="icon">🤖</div>
    <div class="spinner"></div>
    <h2>Génération de vos plans IA</h2>
    <p>L'intelligence artificielle analyse votre profil et votre objectif pour créer 3 plans personnalisés en français.</p>
    <div class="steps"><div class="step"></div><div class="step"></div><div class="step"></div></div>
  </div>
  <form id="f" method="POST" action="ai_planning.php?objectif_id=<?php echo (int)$_GET['objectif_id']; ?>" style="display:none"></form>
  <script>document.addEventListener('DOMContentLoaded',()=>document.getElementById('f').submit())</script>
</body>
</html>
<?php
    exit;
}

// ── POST: fetch data & call Groq ─────────────────────────────────────
$objectiveController = new ObjectiveController();
$objectif = $objectiveController->getById($objectif_id);
if (!$objectif || $objectif['user_id'] != $_SESSION['user_id']) {
    header('Location: objectif_list.php');
    exit;
}

$database = new Database();
$db       = $database->connect();
$userModel = new User($db);
$user     = $userModel->getUserById($_SESSION['user_id']);

$age        = $user['age']       ?? 'non renseigné';
$poids      = $user['poids']     ?? 'non renseigné';
$taille     = $user['taille']    ?? 'non renseigné';
$allergique = ($user['allergique'] ?? 0) ? 'oui' : 'non';
$type_obj   = $objectif['type_objectif'];
$valeur     = $objectif['valeur_cible'];
$priorite   = $objectif['niveau_priorite'] ?? 'moyen';
$date_lim   = $objectif['date_limite']     ?? 'non définie';
$description = $objectif['description']   ?? '';

// ── Prompt: ask for JSON with 3 keys ────────────────────────────────
$prompt = <<<PROMPT
Tu es un expert en nutrition, sport et bien-être. Génère 3 plans détaillés et personnalisés EN FRANÇAIS UNIQUEMENT pour cet utilisateur.

Profil :
- Âge : {$age} ans
- Poids : {$poids} kg
- Taille : {$taille} cm
- Allergique : {$allergique}

Objectif :
- Type : {$type_obj}
- Valeur cible : {$valeur}
- Priorité : {$priorite}
- Date limite : {$date_lim}
- Description : {$description}

Réponds UNIQUEMENT avec un objet JSON valide (sans texte avant ni après) ayant exactement ces 3 clés :
{
  "sport": "...",
  "sommeil": "...",
  "nutrition": "..."
}

Chaque valeur doit être un plan détaillé en Markdown (utilise ##, ###, **, -, des listes à puces).
- "sport" : exercices quotidiens, fréquence, durée, intensité, progression hebdomadaire, récupération
- "sommeil" : horaires, durée, routine du soir, routine du matin, conseils qualité du sommeil
- "nutrition" : repas journaliers, aliments recommandés et à éviter, calories, macronutriments, hydratation

Tout en français. Sois précis et détaillé.
PROMPT;

$groq_key = GROQ_API_KEY;
$groq_url = GROQ_API_URL;

$payload = json_encode([
    "model"       => GROQ_MODEL,
    "messages"    => [["role" => "user", "content" => $prompt]],
    "temperature" => 0.7,
    "max_tokens"  => 4000,
    "response_format" => ["type" => "json_object"]
]);

$ch = curl_init($groq_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        "Content-Type: application/json",
        "Authorization: Bearer {$groq_key}"
    ],
    CURLOPT_TIMEOUT => 90,
]);

$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!$response || $http_code !== 200) {
    $_SESSION['ai_error'] = "Erreur API IA (code {$http_code}). Veuillez réessayer.";
    header('Location: objectif_list.php');
    exit;
}

$api_data = json_decode($response, true);
$raw      = $api_data['choices'][0]['message']['content'] ?? '';

if (empty($raw)) {
    $_SESSION['ai_error'] = "L'IA n'a pas retourné de contenu. Veuillez réessayer.";
    header('Location: objectif_list.php');
    exit;
}

// Parse JSON response
$plans_json = json_decode($raw, true);

if (!$plans_json || !isset($plans_json['sport'])) {
    // Fallback: try to extract JSON from the raw string
    if (preg_match('/\{.*\}/s', $raw, $m)) {
        $plans_json = json_decode($m[0], true);
    }
}

$plan_sport     = $plans_json['sport']     ?? $raw;
$plan_sommeil   = $plans_json['sommeil']   ?? '';
$plan_nutrition = $plans_json['nutrition'] ?? '';

$_SESSION['ai_plans'] = [
    'sport'        => $plan_sport,
    'sommeil'      => $plan_sommeil,
    'nutrition'    => $plan_nutrition,
    'objectif'     => $type_obj,
    'user_nom'     => $user['nom'] ?? '',
    'generated_at' => date('d/m/Y H:i'),
];

header('Location: ai_sport.php');
exit;
?>
