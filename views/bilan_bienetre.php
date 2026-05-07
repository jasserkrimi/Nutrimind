<?php
// ── Bilan de Bien-être Hebdomadaire ───────────────────────────────────
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

$result = null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $energie    = (int)($_POST['energie']    ?? 3);
    $sommeil    = (int)($_POST['sommeil']    ?? 3);
    $faim       = (int)($_POST['faim']       ?? 3);
    $stress     = (int)($_POST['stress']     ?? 3);
    $motivation = (int)($_POST['motivation'] ?? 3);

    // Summarise objectives
    $objText = '';
    foreach ($objectives as $obj) {
        $objText .= "- {$obj['type_objectif']}, cible={$obj['valeur_cible']}, statut={$obj['statut']}\n";
    }
    if (!$objText) $objText = "Aucun objectif.";

    // Active plan summary
    $planText = "Aucun plan.";
    foreach ($plans as $plan) {
        if ($plan['statut'] === 'actif') {
            $planText = "{$plan['calories_par_jour']}kcal/j, sommeil={$plan['heures_sommeil_par_jour']}h, sport={$plan['heures_entrainement_par_jour']}h";
            break;
        }
    }

    $prompt = "Coach bien-être. Bilan hebdomadaire EN FRANÇAIS.\n"
        . "Profil: {$user['nom']}, {$user['age']}ans\n"
        . "Scores (1=très mauvais, 5=excellent):\n"
        . "- Énergie: {$energie}/5\n"
        . "- Qualité du sommeil: {$sommeil}/5\n"
        . "- Gestion de la faim: {$faim}/5\n"
        . "- Niveau de stress: {$stress}/5 (1=très stressé)\n"
        . "- Motivation: {$motivation}/5\n"
        . "Objectifs: {$objText}"
        . "Plan actif: {$planText}\n\n"
        . "JSON: {\"score_global\":75,\"humeur\":\"Bien\",\"analyse\":\"...\",\"points_positifs\":[\"...\"],\"points_ameliorer\":[\"...\"],\"conseil_semaine\":\"...\",\"emoji\":\"😊\"}";

    $raw = askOllama($prompt, true);

    if ($raw) {
        $result = json_decode($raw, true);
        if (!$result) {
            preg_match('/\{.*\}/s', $raw, $m);
            $result = json_decode($m[0] ?? '{}', true) ?: null;
        }
    }

    if (!$result) $error = "L'IA n'a pas pu analyser. Assurez-vous qu'Ollama tourne.";
}
?>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="assets/css/nutrimind-ui.css">

<div class="nm-page">

  <!-- Hero -->
  <div class="nm-hero nm-anim-hero">
    <div class="nm-hero-inner">
      <div class="nm-hero-eyebrow">🧘 Bien-être</div>
      <h1>Bilan <span>Hebdomadaire</span></h1>
      <p>Évaluez votre semaine en 5 questions et recevez un bilan personnalisé par l'IA</p>
    </div>
  </div>

  <!-- Tabs -->
  <div class="nm-tabs-wrap nm-anim-tabs">
    <div class="nm-tabs">
      <a href="objectif_list.php"     class="nm-tab">🎯 Mes Objectifs</a>
      <a href="mes_plans.php"         class="nm-tab">🥗 Mes Plans</a>
      <a href="mes_statistiques.php"  class="nm-tab">📊 Statistiques</a>
      <a href="suivi_progression.php" class="nm-tab">📈 Suivi IA</a>
      <a href="bilan_bienetre.php"    class="nm-tab active">🧘 Bilan</a>
      <a href="ai_coherence.php"      class="nm-tab ai-tab">🔍 Cohérence IA</a>
      <a href="ai_report.php"         class="nm-tab ai-tab">📋 Rapport IA</a>
      <a href="ai_mutation.php"       class="nm-tab ai-tab">🔄 Mutation IA</a>
    </div>
  </div>

  <div class="nm-content">
    <div class="container">

      <?php if ($error): ?>
        <div class="alert alert-danger" style="border-radius:12px;margin-bottom:1.5rem;"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <?php if (!$result): ?>
      <!-- Check-in form -->
      <div class="nm-card nm-anim-card" style="max-width:560px;">
        <div class="nm-card-header">
          <h5>📝 Comment s'est passée votre semaine ?</h5>
        </div>
        <div class="nm-card-body">
          <form method="POST">
            <?php
              $questions = [
                ['name'=>'energie',    'label'=>'⚡ Niveau d\'énergie',      'low'=>'Épuisé',        'high'=>'Plein d\'énergie'],
                ['name'=>'sommeil',    'label'=>'🌙 Qualité du sommeil',     'low'=>'Très mauvais',  'high'=>'Excellent'],
                ['name'=>'faim',       'label'=>'🍽️ Gestion de la faim',    'low'=>'Très difficile','high'=>'Très bien'],
                ['name'=>'stress',     'label'=>'😤 Niveau de stress',       'low'=>'Très stressé',  'high'=>'Très calme'],
                ['name'=>'motivation', 'label'=>'💪 Motivation',             'low'=>'Aucune',        'high'=>'Maximale'],
              ];
            ?>
            <?php foreach ($questions as $q): ?>
              <div style="margin-bottom:1.5rem;">
                <div style="font-weight:600;font-size:.9rem;color:#1e293b;margin-bottom:.6rem;"><?php echo $q['label']; ?></div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                  <span style="font-size:.75rem;color:#94a3b8;min-width:80px;"><?php echo $q['low']; ?></span>
                  <div style="display:flex;gap:.4rem;flex:1;justify-content:center;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <label style="cursor:pointer;text-align:center;">
                        <input type="radio" name="<?php echo $q['name']; ?>" value="<?php echo $i; ?>" <?php echo $i === 3 ? 'checked' : ''; ?> style="display:none;" class="rating-input">
                        <div class="rating-btn" style="width:40px;height:40px;border-radius:50%;border:2px solid #e2e8f0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.9rem;color:#94a3b8;transition:all .15s;background:#f8fafc;"><?php echo $i; ?></div>
                      </label>
                    <?php endfor; ?>
                  </div>
                  <span style="font-size:.75rem;color:#94a3b8;min-width:80px;text-align:right;"><?php echo $q['high']; ?></span>
                </div>
              </div>
            <?php endforeach; ?>

            <button type="submit" class="nm-btn nm-btn-brand nm-btn-lg" style="width:100%;justify-content:center;margin-top:.5rem;">
              🧘 Analyser ma semaine
            </button>
          </form>
        </div>
      </div>

      <?php else: ?>
      <!-- AI Result -->
      <?php
        $score      = (int)($result['score_global'] ?? 50);
        $scoreGrad  = $score >= 70
          ? 'linear-gradient(135deg,#10b981,#34d399)'
          : ($score >= 45 ? 'linear-gradient(135deg,#f59e0b,#fbbf24)' : 'linear-gradient(135deg,#ef4444,#f87171)');
      ?>

      <!-- Score card -->
      <div class="nm-card nm-anim-card mb-4" style="max-width:560px;overflow:hidden;">
        <div style="background:<?php echo $scoreGrad; ?>;padding:2rem;text-align:center;">
          <div style="font-size:3rem;margin-bottom:.5rem;"><?php echo htmlspecialchars($result['emoji'] ?? '😊'); ?></div>
          <div style="color:#fff;font-size:1.8rem;font-weight:900;"><?php echo $score; ?><span style="font-size:1rem;opacity:.7;">/100</span></div>
          <div style="color:rgba(255,255,255,.85);font-size:1rem;font-weight:600;margin-top:.3rem;"><?php echo htmlspecialchars($result['humeur'] ?? ''); ?></div>
        </div>
        <div class="nm-card-body">
          <p style="color:#475569;line-height:1.7;margin:0;"><?php echo htmlspecialchars($result['analyse'] ?? ''); ?></p>
        </div>
      </div>

      <div class="row g-3" style="max-width:560px;">

        <!-- Points positifs -->
        <div class="col-12">
          <div class="nm-card">
            <div class="nm-card-header"><h5>✅ Points positifs</h5></div>
            <div class="nm-card-body">
              <?php foreach (($result['points_positifs'] ?? []) as $pt): ?>
                <div style="display:flex;gap:.6rem;align-items:flex-start;margin-bottom:.5rem;padding:.6rem .8rem;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;">
                  <span style="color:#10b981;flex-shrink:0;">✓</span>
                  <span style="font-size:.88rem;color:#065f46;"><?php echo htmlspecialchars($pt); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Points à améliorer -->
        <div class="col-12">
          <div class="nm-card">
            <div class="nm-card-header"><h5>💡 À améliorer</h5></div>
            <div class="nm-card-body">
              <?php foreach (($result['points_ameliorer'] ?? []) as $pt): ?>
                <div style="display:flex;gap:.6rem;align-items:flex-start;margin-bottom:.5rem;padding:.6rem .8rem;background:#fef9c3;border-radius:8px;border:1px solid #fde68a;">
                  <span style="color:#f59e0b;flex-shrink:0;">→</span>
                  <span style="font-size:.88rem;color:#92400e;"><?php echo htmlspecialchars($pt); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Conseil de la semaine -->
        <div class="col-12">
          <div class="nm-card" style="background:linear-gradient(135deg,#1e293b,#334155);border:none;">
            <div class="nm-card-body d-flex gap-3 align-items-start">
              <span style="font-size:1.8rem;flex-shrink:0;">🌟</span>
              <div>
                <div style="color:#fff;font-weight:700;margin-bottom:.4rem;">Conseil de la semaine</div>
                <p style="color:rgba(255,255,255,.7);margin:0;font-size:.9rem;line-height:1.7;"><?php echo htmlspecialchars($result['conseil_semaine'] ?? ''); ?></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Refaire -->
        <div class="col-12 text-center pb-3">
          <a href="bilan_bienetre.php" class="nm-btn nm-btn-ghost">↩ Refaire le bilan</a>
        </div>

      </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<style>
  input[type=radio]:checked + .rating-btn {
    background: var(--c-brand) !important;
    border-color: var(--c-brand) !important;
    color: #fff !important;
  }
  .rating-btn:hover {
    border-color: var(--c-brand) !important;
    color: var(--c-brand) !important;
  }
</style>

<script>
  document.querySelectorAll('.rating-input').forEach(input => {
    input.addEventListener('change', function () {
      document.querySelectorAll(`input[name="${this.name}"]`).forEach(r => {
        r.nextElementSibling.style.background  = '';
        r.nextElementSibling.style.borderColor = '';
        r.nextElementSibling.style.color       = '';
      });
      this.nextElementSibling.style.background  = '#e07b39';
      this.nextElementSibling.style.borderColor = '#e07b39';
      this.nextElementSibling.style.color       = '#fff';
    });
    if (input.checked) {
      input.nextElementSibling.style.background  = '#e07b39';
      input.nextElementSibling.style.borderColor = '#e07b39';
      input.nextElementSibling.style.color       = '#fff';
    }
  });
</script>

<?php include 'footer.php'; ?>
