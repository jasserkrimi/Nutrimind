<?php
// AI Plan Mutation Engine
// Takes an existing plan + a real-life obstacle and rewrites the plan around it.
// Step 1 (GET, no params):  selection form (pick plan + obstacle)
// Step 2 (GET, ?mutate=1):  loading screen → auto-POST
// Step 3 (POST):            AI call → save to session → redirect to ai_mutation_view.php
session_start();
set_time_limit(300);
require_once '../controllers/PlanningController.php';
require_once '../controllers/ObjectiveController.php';
require_once '../config/Database.php';
require_once '../config/secrets.php';
require_once '../models/User.php';
require_once 'ai_helper.php';

if (!isset($_SESSION['user_id'])) { header('Location: auth.php'); exit; }

$plans      = (new PlanningController())->getAllForUser($_SESSION['user_id']);
$objectives = (new ObjectiveController())->getAllForUser($_SESSION['user_id']);
$db         = (new Database())->connect();
$user       = (new User($db))->getUserById($_SESSION['user_id']);

// Obstacle options shown in the form
$obstacles = [
    'blessure'    => ['🤕', 'Blessure',          'Douleur, blessure musculaire ou articulaire'],
    'voyage'      => ['✈️', 'Voyage / Déplacement','Pas de cuisine, pas de salle de sport'],
    'maladie'     => ['🤒', 'Maladie',            'Fatigue, fièvre, récupération'],
    'no_temps'    => ['⏰', 'Manque de temps',    'Semaine chargée, pas plus de 30 min/jour'],
    'no_equipement'=> ['🏠','Pas d\'équipement',  'Entraînement à la maison uniquement'],
    'stress'      => ['😰', 'Stress intense',     'Examens, surcharge mentale, anxiété'],
    'plateau'     => ['📉', 'Plateau de poids',   'Plus de progression depuis 2+ semaines'],
];

// ── Step 1: selection form (GET, no ?mutate param) ────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['mutate'])) {
    include 'header.php';
?>
<link rel="stylesheet" href="assets/css/nutrimind-ui.css">
<div class="nm-page">

  <div class="nm-hero nm-anim-hero">
    <div class="nm-hero-inner">
      <div class="nm-hero-eyebrow">🔄 Mutation IA</div>
      <h1>Adapter votre plan à la <span>réalité</span></h1>
      <p>Choisissez votre plan et l'obstacle du moment — l'IA le réécrit pour vous</p>
    </div>
  </div>

  <div class="nm-tabs-wrap nm-anim-tabs">
    <div class="nm-tabs">
      <a href="objectif_list.php"  class="nm-tab">🎯 Mes Objectifs</a>
      <a href="mes_plans.php"      class="nm-tab">🥗 Mes Plans</a>
      <a href="mes_statistiques.php" class="nm-tab">📊 Statistiques</a>
      <a href="suivi_progression.php" class="nm-tab">📈 Suivi IA</a>
      <a href="bilan_bienetre.php" class="nm-tab">🧘 Bilan</a>
      <a href="ai_coherence.php"   class="nm-tab ai-tab">🔍 Cohérence IA</a>
      <a href="ai_report.php"      class="nm-tab ai-tab">📋 Rapport IA</a>
      <a href="ai_mutation.php"    class="nm-tab ai-tab active">🔄 Mutation IA</a>
    </div>
  </div>

  <div class="nm-content">
    <div class="container">

      <?php if (empty($plans)): ?>
        <div class="nm-card nm-anim-card" style="max-width:520px;">
          <div class="nm-card-body">
            <div class="nm-empty">
              <span class="nm-empty-emoji">📋</span>
              <h5>Aucun plan trouvé</h5>
              <p>Vous n'avez pas encore de plan nutritionnel assigné.</p>
            </div>
          </div>
        </div>
      <?php else: ?>

      <form method="GET" action="ai_mutation.php" id="mutationForm">
        <input type="hidden" name="mutate" value="1">

        <div class="row g-4">

          <!-- Left: plan selector -->
          <div class="col-lg-5">
            <div class="nm-card nm-anim-card h-100">
              <div class="nm-card-header">
                <h5>🥗 Votre plan actuel</h5>
              </div>
              <div class="nm-card-body">
                <?php foreach ($plans as $plan): ?>
                  <label style="display:block;cursor:pointer;margin-bottom:.75rem;">
                    <input type="radio" name="plan_id" value="<?php echo $plan['id_planning']; ?>"
                           style="display:none;" class="plan-radio"
                           <?php echo $plan['statut'] === 'actif' ? 'checked' : ''; ?>>
                    <div class="plan-card-option" style="padding:.9rem 1rem;border:2px solid #e2e8f0;border-radius:12px;transition:all .15s;">
                      <div style="font-weight:700;font-size:.9rem;color:#1e293b;">
                        <?php echo htmlspecialchars($plan['titre'] ?? 'Sans titre'); ?>
                      </div>
                      <div style="font-size:.78rem;color:#64748b;margin-top:.25rem;">
                        <?php echo $plan['calories_par_jour']; ?> kcal/j
                        · sport: <?php echo $plan['heures_entrainement_par_jour']; ?>h
                        · sommeil: <?php echo $plan['heures_sommeil_par_jour']; ?>h
                      </div>
                      <div style="margin-top:.3rem;">
                        <span style="font-size:.72rem;padding:.15rem .6rem;border-radius:20px;background:<?php echo $plan['statut']==='actif'?'#dcfce7':'#f1f5f9'; ?>;color:<?php echo $plan['statut']==='actif'?'#166534':'#64748b'; ?>;">
                          <?php echo $plan['statut']; ?>
                        </span>
                      </div>
                    </div>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <!-- Right: obstacle selector -->
          <div class="col-lg-7">
            <div class="nm-card nm-anim-card-2 h-100">
              <div class="nm-card-header">
                <h5>⚡ Quel est votre obstacle ?</h5>
              </div>
              <div class="nm-card-body">
                <div class="row g-2">
                  <?php foreach ($obstacles as $key => [$emoji, $label, $desc]): ?>
                    <div class="col-6">
                      <label style="display:block;cursor:pointer;">
                        <input type="radio" name="obstacle" value="<?php echo $key; ?>"
                               style="display:none;" class="obstacle-radio"
                               <?php echo $key === 'blessure' ? 'checked' : ''; ?>>
                        <div class="obstacle-option" style="padding:.8rem;border:2px solid #e2e8f0;border-radius:12px;transition:all .15s;text-align:center;">
                          <div style="font-size:1.6rem;margin-bottom:.3rem;"><?php echo $emoji; ?></div>
                          <div style="font-weight:700;font-size:.82rem;color:#1e293b;"><?php echo $label; ?></div>
                          <div style="font-size:.72rem;color:#94a3b8;margin-top:.2rem;line-height:1.3;"><?php echo $desc; ?></div>
                        </div>
                      </label>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Duration -->
        <div class="nm-card mt-4 nm-anim-card-2" style="max-width:560px;">
          <div class="nm-card-header">
            <h5>📅 Pour combien de temps ?</h5>
          </div>
          <div class="nm-card-body">
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
              <?php foreach (['1 jour' => '1j', '3 jours' => '3j', '1 semaine' => '1sem', '2 semaines' => '2sem'] as $label => $val): ?>
                <label style="cursor:pointer;">
                  <input type="radio" name="duree" value="<?php echo $val; ?>"
                         style="display:none;" class="duree-radio"
                         <?php echo $val === '1sem' ? 'checked' : ''; ?>>
                  <div class="duree-option" style="padding:.5rem 1.2rem;border:2px solid #e2e8f0;border-radius:30px;font-weight:600;font-size:.85rem;color:#64748b;transition:all .15s;">
                    <?php echo $label; ?>
                  </div>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="mt-4 nm-anim-card-3">
          <button type="submit" class="nm-btn nm-btn-brand nm-btn-lg">
            🔄 Muter mon plan
          </button>
        </div>

      </form>

      <?php endif; ?>
    </div>
  </div>
</div>

<style>
  /* Highlight selected plan card */
  .plan-radio:checked + .plan-card-option {
    border-color: var(--c-brand) !important;
    background: #fff7f0;
  }
  /* Highlight selected obstacle */
  .obstacle-radio:checked + .obstacle-option {
    border-color: #6366f1 !important;
    background: #ede9fe;
  }
  .obstacle-radio:checked + .obstacle-option div:first-child { transform: scale(1.15); }
  /* Highlight selected duration */
  .duree-radio:checked + .duree-option {
    border-color: var(--c-brand) !important;
    background: var(--c-brand);
    color: #fff !important;
  }
</style>

<script>
  // Highlight selected radio options on page load
  document.querySelectorAll('.plan-radio, .obstacle-radio, .duree-radio').forEach(input => {
    if (input.checked) {
      const target = input.nextElementSibling;
      if (target) target.click && target.click();
      // Manually trigger the visual state via CSS — the :checked selector handles it
    }
  });
</script>

<?php
    include 'footer.php';
    exit;
}

// ── Step 2: loading screen (GET with ?mutate=1) ───────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['mutate'])) {
    $plan_id  = (int)($_GET['plan_id']  ?? 0);
    $obstacle = $_GET['obstacle'] ?? '';
    $duree    = $_GET['duree']    ?? '1sem';
    if (!$plan_id || !$obstacle) { header('Location: ai_mutation.php'); exit; }

    $obstacleLabels = array_map(fn($o) => $o[1], $obstacles);
    $obstacleLabel  = $obstacleLabels[$obstacle] ?? $obstacle;

    showLoadingScreen(
        'ai_mutation.php',
        '🔄',
        'Mutation du plan en cours…',
        "L'IA adapte votre plan à votre contrainte : {$obstacleLabel}.",
        '#e07b39',
        'linear-gradient(135deg,#1e293b,#312e81)',
        "<input type='hidden' name='plan_id'  value='{$plan_id}'>"
        . "<input type='hidden' name='obstacle' value='" . htmlspecialchars($obstacle) . "'>"
        . "<input type='hidden' name='duree'    value='" . htmlspecialchars($duree) . "'>"
    );
    exit;
}

// ── Step 3: AI call (POST) ────────────────────────────────────────────
$plan_id  = (int)($_POST['plan_id']  ?? 0);
$obstacle = $_POST['obstacle'] ?? '';
$duree    = $_POST['duree']    ?? '1sem';

if (!$plan_id || !$obstacle) { header('Location: ai_mutation.php'); exit; }

// Load the selected plan
$selectedPlan = null;
foreach ($plans as $p) {
    if ($p['id_planning'] == $plan_id) { $selectedPlan = $p; break; }
}
if (!$selectedPlan) { header('Location: ai_mutation.php'); exit; }

// Find linked objective (if any)
$linkedObj = null;
foreach ($objectives as $o) {
    if ($o['id_objectif'] == ($selectedPlan['objectif_id'] ?? 0)) { $linkedObj = $o; break; }
}

$obstacleDescriptions = [
    'blessure'     => 'blessure musculaire ou articulaire — éviter tout exercice douloureux',
    'voyage'       => 'voyage ou déplacement — pas de cuisine personnelle, pas de salle de sport',
    'maladie'      => 'maladie ou fatigue intense — priorité à la récupération',
    'no_temps'     => 'manque de temps — maximum 30 minutes par jour disponibles',
    'no_equipement'=> 'pas d\'équipement — entraînement à domicile uniquement, poids du corps',
    'stress'       => 'stress intense ou surcharge mentale — besoin de simplicité et de douceur',
    'plateau'      => 'plateau de poids — aucune progression depuis 2+ semaines malgré le suivi',
];
$obstacleDesc = $obstacleDescriptions[$obstacle] ?? $obstacle;

$objContext = $linkedObj
    ? "Objectif lié: {$linkedObj['type_objectif']}, cible={$linkedObj['valeur_cible']}kg"
    : "Aucun objectif lié.";

$prompt = "Tu es un coach sportif et nutritionniste expert. Réponds EN FRANÇAIS.\n"
    . "Profil: {$user['nom']}, {$user['age']}ans, {$user['poids']}kg, taille={$user['taille']}cm\n\n"
    . "Plan actuel à muter:\n"
    . "- Titre: " . ($selectedPlan['titre'] ?? 'Sans titre') . "\n"
    . "- Calories: {$selectedPlan['calories_par_jour']} kcal/j\n"
    . "- Protéines: {$selectedPlan['objectif_proteines']}g, Glucides: {$selectedPlan['objectif_glucides']}g, Lipides: {$selectedPlan['objectif_lipides']}g\n"
    . "- Repas/jour: {$selectedPlan['nombre_repas_par_jour']}\n"
    . "- Sport: {$selectedPlan['heures_entrainement_par_jour']}h/j\n"
    . "- Sommeil: {$selectedPlan['heures_sommeil_par_jour']}h/j\n"
    . "{$objContext}\n\n"
    . "Obstacle: {$obstacleDesc}\n"
    . "Durée de l'adaptation: {$duree}\n\n"
    . "Réécris ce plan pour contourner cet obstacle tout en préservant l'objectif au maximum.\n"
    . "JSON uniquement:\n"
    . "{\n"
    . "  \"titre_mute\": \"Nom du plan adapté\",\n"
    . "  \"resume_adaptation\": \"Ce qui change et pourquoi\",\n"
    . "  \"calories_adaptees\": 1800,\n"
    . "  \"sport_adapte\": \"Description du sport modifié\",\n"
    . "  \"nutrition_adaptee\": \"Description de la nutrition modifiée\",\n"
    . "  \"sommeil_adapte\": \"Conseils sommeil adaptés\",\n"
    . "  \"ce_qui_change\": [\"changement 1\", \"changement 2\"],\n"
    . "  \"ce_qui_reste\": [\"élément conservé 1\", \"élément conservé 2\"],\n"
    . "  \"avertissements\": [\"attention 1\"],\n"
    . "  \"retour_normal\": \"Comment reprendre le plan original après {$duree}\"\n"
    . "}";

$raw = askOllama($prompt, true);

if (!$raw) {
    $_SESSION['mutation_error'] = "Erreur Ollama. Assurez-vous qu'Ollama tourne (start_ollama.bat).";
    header('Location: ai_mutation.php'); exit;
}

$result = json_decode($raw, true);
if (!$result || !isset($result['titre_mute'])) {
    preg_match('/\{.*\}/s', $raw, $m);
    $result = json_decode($m[0] ?? '{}', true) ?: null;
}

if (!$result) {
    $_SESSION['mutation_error'] = "L'IA n'a pas retourné de résultat valide. Réessayez.";
    header('Location: ai_mutation.php'); exit;
}

$obstacleLabels = array_map(fn($o) => $o[1], $obstacles);
$obstacleEmojis = array_map(fn($o) => $o[0], $obstacles);

$_SESSION['ai_mutation'] = [
    'result'          => $result,
    'user_nom'        => $user['nom'] ?? '',
    'plan_titre'      => $selectedPlan['titre'] ?? 'Sans titre',
    'plan_calories'   => $selectedPlan['calories_par_jour'],
    'obstacle_key'    => $obstacle,
    'obstacle_label'  => $obstacleLabels[$obstacle]  ?? $obstacle,
    'obstacle_emoji'  => $obstacleEmojis[$obstacle]  ?? '⚡',
    'duree'           => $duree,
    'generated_at'    => date('d/m/Y H:i'),
];

header('Location: ai_mutation_view.php'); exit;
