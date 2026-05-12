<?php
// Checks coherence between a selected objective and a nutrition plan.
// Step 1 (GET, no params): selection form
// Step 2 (GET, ?analyse=1):  loading screen → auto-POST
// Step 3 (POST):             AI call → save to session → redirect
session_start();
set_time_limit(300);
require_once '../controllers/ObjectiveController.php';
require_once '../controllers/PlanningController.php';
require_once '../config/Database.php';
require_once '../config/secrets.php';
require_once '../models/User.php';
require_once 'ai_helper.php';

if (!isset($_SESSION['user_id'])) { header('Location: auth.php'); exit; }

$objectives = (new ObjectiveController())->getAllForUser($_SESSION['user_id']);
$plans      = (new PlanningController())->getAllForUser($_SESSION['user_id']);
$db         = (new Database())->connect();
$user       = (new User($db))->getUserById($_SESSION['user_id']);

// ── Step 1: selection form (GET, no ?analyse param) ──────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['analyse'])) {
    include 'header.php';
?>
<link rel="stylesheet" href="assets/css/nutrimind-ui.css">
<div class="nm-page">

  <div class="nm-hero nm-anim-hero">
    <div class="nm-hero-inner">
      <div class="nm-hero-eyebrow">🔍 Cohérence IA</div>
      <h1>Analyser la <span>Cohérence</span></h1>
      <p>Choisissez un objectif et un plan à comparer avant de lancer l'analyse</p>
    </div>
  </div>

  <div class="nm-tabs-wrap nm-anim-tabs">
    <div class="nm-tabs">
      <a href="objectif_list.php"     class="nm-tab">🎯 Mes Objectifs</a>
      <a href="mes_plans.php"         class="nm-tab">🥗 Mes Plans</a>
      <a href="mes_statistiques.php"  class="nm-tab">📊 Statistiques</a>
      <a href="suivi_progression.php" class="nm-tab">📈 Suivi IA</a>
      <a href="bilan_bienetre.php"    class="nm-tab">🧘 Bilan</a>
      <a href="ai_coherence.php"      class="nm-tab ai-tab active">🔍 Cohérence IA</a>
      <a href="ai_report.php"         class="nm-tab ai-tab">📋 Rapport IA</a>
      <a href="ai_mutation.php"       class="nm-tab ai-tab">🔄 Mutation IA</a>
    </div>
  </div>

  <div class="nm-content">
    <div class="container">

      <?php if (empty($objectives)): ?>
        <div class="nm-card nm-anim-card" style="max-width:520px;">
          <div class="nm-card-body">
            <div class="nm-empty">
              <span class="nm-empty-emoji">🎯</span>
              <h5>Aucun objectif trouvé</h5>
              <p>Créez un objectif avant de lancer l'analyse de cohérence.</p>
              <a href="objectif_create.php" class="nm-btn nm-btn-brand mt-2">＋ Créer un objectif</a>
            </div>
          </div>
        </div>

      <?php elseif (empty($plans)): ?>
        <div class="nm-card nm-anim-card" style="max-width:520px;">
          <div class="nm-card-body">
            <div class="nm-empty">
              <span class="nm-empty-emoji">📋</span>
              <h5>Aucun plan assigné</h5>
              <p>Aucun plan nutritionnel ne vous a été assigné. Contactez un expert NutriMind.</p>
            </div>
          </div>
        </div>

      <?php else: ?>
        <div class="nm-card nm-anim-card" style="max-width:560px;">
          <div class="nm-card-header">
            <h5>🔗 Associer un objectif à un plan</h5>
          </div>
          <div class="nm-card-body">
            <form method="GET" action="ai_coherence.php">
              <input type="hidden" name="analyse" value="1">

              <div style="margin-bottom:1.2rem;">
                <label style="font-size:.82rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:.5rem;">🎯 Objectif</label>
                <select name="objectif_id" required style="width:100%;padding:.65rem 1rem;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.9rem;color:#1e293b;background:#fff;outline:none;">
                  <option value="">Sélectionner un objectif…</option>
                  <?php foreach ($objectives as $obj): ?>
                    <option value="<?php echo $obj['id_objectif']; ?>">
                      <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $obj['type_objectif']))); ?>
                      — cible: <?php echo $obj['valeur_cible']; ?> kg (<?php echo $obj['statut']; ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div style="margin-bottom:1.5rem;">
                <label style="font-size:.82rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:.5rem;">🥗 Plan nutritionnel</label>
                <select name="plan_id" required style="width:100%;padding:.65rem 1rem;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.9rem;color:#1e293b;background:#fff;outline:none;">
                  <option value="">Sélectionner un plan…</option>
                  <?php foreach ($plans as $plan): ?>
                    <option value="<?php echo $plan['id_planning']; ?>">
                      <?php echo htmlspecialchars($plan['titre'] ?? 'Sans titre'); ?>
                      — <?php echo $plan['calories_par_jour']; ?> kcal/j (<?php echo $plan['statut']; ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <button type="submit" class="nm-btn nm-btn-brand nm-btn-lg" style="width:100%;justify-content:center;">
                🔍 Analyser la cohérence
              </button>
            </form>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>
<?php
    include 'footer.php';
    exit;
}

// ── Step 2: loading screen (GET with ?analyse=1) ──────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['analyse'])) {
    $objectif_id = (int)($_GET['objectif_id'] ?? 0);
    $plan_id     = (int)($_GET['plan_id']     ?? 0);
    if (!$objectif_id || !$plan_id) { header('Location: ai_coherence.php'); exit; }

    showLoadingScreen(
        "ai_coherence.php",
        '🔍',
        'Analyse de cohérence en cours',
        "L'IA compare votre objectif avec votre plan nutritionnel sélectionné.",
        '#e07b39',
        'linear-gradient(135deg,#1e293b,#334155)',
        // Extra hidden fields to carry the IDs through the POST
        "<input type='hidden' name='objectif_id' value='{$objectif_id}'>"
        . "<input type='hidden' name='plan_id' value='{$plan_id}'>"
    );
    exit;
}

// ── Step 3: AI call (POST) ────────────────────────────────────────────
$objectif_id = (int)($_POST['objectif_id'] ?? 0);
$plan_id     = (int)($_POST['plan_id']     ?? 0);
if (!$objectif_id || !$plan_id) { header('Location: ai_coherence.php'); exit; }

// Find the selected objective and plan
$selectedObj  = null;
$selectedPlan = null;
foreach ($objectives as $o) { if ($o['id_objectif'] == $objectif_id) { $selectedObj  = $o; break; } }
foreach ($plans      as $p) { if ($p['id_planning']  == $plan_id)     { $selectedPlan = $p; break; } }
if (!$selectedObj || !$selectedPlan) { header('Location: ai_coherence.php'); exit; }

$prompt = "Expert nutrition. Analyse cohérence EN FRANÇAIS.\n"
    . "Profil: {$user['nom']}, {$user['age']}ans, {$user['poids']}kg\n\n"
    . "Objectif: type={$selectedObj['type_objectif']}, cible={$selectedObj['valeur_cible']}kg, "
    . "statut={$selectedObj['statut']}, priorité={$selectedObj['niveau_priorite']}\n\n"
    . "Plan: " . ($selectedPlan['titre'] ?? 'Sans titre') . ", "
    . "{$selectedPlan['calories_par_jour']}kcal/j, prot={$selectedPlan['objectif_proteines']}g, "
    . "gluc={$selectedPlan['objectif_glucides']}g, lip={$selectedPlan['objectif_lipides']}g, "
    . "sport={$selectedPlan['heures_entrainement_par_jour']}h/j\n\n"
    . "JSON uniquement:\n"
    . "{\"score_global\":75,\"verdict\":\"Partiellement aligné\",\"resume\":\"...\","
    . "\"points_forts\":[\"...\"],\"incompatibilites\":[\"...\"],\"ajustements\":[\"...\"],\"conseil_final\":\"...\"}";

$raw = askOllama($prompt, true);

if (!$raw) {
    $_SESSION['coherence_error'] = "Erreur Ollama. Assurez-vous qu'Ollama tourne (start_ollama.bat).";
    header('Location: objectif_list.php'); exit;
}

$result = json_decode($raw, true);
if (!$result || !isset($result['score_global'])) {
    preg_match('/\{.*\}/s', $raw, $m);
    $result = json_decode($m[0] ?? '{}', true) ?: null;
}

if (!$result) {
    $_SESSION['coherence_error'] = "L'IA n'a pas retourné de résultat valide. Réessayez.";
    header('Location: objectif_list.php'); exit;
}

$_SESSION['ai_coherence'] = [
    'result'       => $result,
    'user_nom'     => $user['nom'] ?? '',
    'objectif'     => ucwords(str_replace('_', ' ', $selectedObj['type_objectif'])),
    'plan'         => $selectedPlan['titre'] ?? 'Sans titre',
    'nb_obj'       => count($objectives),
    'nb_plans'     => count($plans),
    'generated_at' => date('d/m/Y H:i'),
];

header('Location: ai_coherence_view.php'); exit;
