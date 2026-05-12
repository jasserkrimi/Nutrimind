<?php
// ── Suivi de Progression IA ───────────────────────────────────────────
session_start();
set_time_limit(300);
require_once '../controllers/ObjectiveController.php';
require_once '../config/Database.php';
require_once '../config/secrets.php';
require_once '../models/User.php';
require_once 'ai_helper.php';

if (!isset($_SESSION['user_id'])) { header('Location: auth.php'); exit; }

$objectives = (new ObjectiveController())->getAllForUser($_SESSION['user_id']);
$db         = (new Database())->connect();
$user       = (new User($db))->getUserById($_SESSION['user_id']);

$feedback     = null;
$error        = null;
$poids_actuel = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['poids_actuel'])) {
    $poids_actuel = floatval($_POST['poids_actuel']);

    if ($poids_actuel <= 0 || $poids_actuel > 500) {
        $error = "Veuillez entrer un poids valide entre 1 et 500 kg.";
    } elseif (empty($objectives)) {
        $error = "Vous n'avez aucun objectif. Créez un objectif d'abord.";
    } else {
        // Résumer les objectifs
        $objText = '';
        foreach ($objectives as $i => $obj) {
            $objText .= "- Obj".($i+1).": {$obj['type_objectif']}, cible={$obj['valeur_cible']}kg, initial=".($obj['poids_initial']??'?')."kg\n";
        }

        // Construire le prompt
        $prompt = "Coach nutrition expert. Analyse de progression EN FRANÇAIS.\n"
            . "Profil: {$user['nom']}, {$user['age']}ans\nPoids actuel: {$poids_actuel}kg\n"
            . "Objectifs:\n{$objText}\n"
            . "Fournis une analyse détaillée incluant:\n"
            . "1. Verdict sur la progression actuelle\n"
            . "2. Estimation de la date d'atteinte de l'objectif basée sur la progression\n"
            . "3. Ce qui va se passer physiquement et mentalement pendant la période restante\n"
            . "4. Un conseil concret et actionnable\n\n"
            . "JSON: {\"verdict\":\"En bonne voie\",\"progression\":\"...\",\"prediction\":\"...\",\"periode\":\"...\",\"conseil\":\"...\",\"emoji\":\"...\"}";

        // Appeler l'IA
        $raw = askOllama($prompt, true);

        if ($raw) {
            $feedback = json_decode($raw, true);
            if (!$feedback) {
                preg_match('/\{.*\}/s', $raw, $m);
                $feedback = json_decode($m[0] ?? '{}', true) ?: null;
            }
        }

        if (!$feedback) $error = "L'IA n'a pas pu analyser. Réessayez.";
    }
}
?>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="assets/css/nutrimind-ui.css">

<div class="nm-page">

  <!-- Hero -->
  <div class="nm-hero nm-anim-hero">
    <div class="nm-hero-inner">
      <div class="nm-hero-eyebrow">📈 Suivi</div>
      <h1>Suivi de <span>Progression</span></h1>
      <p>Entrez votre poids actuel et l'IA analyse votre progression vers vos objectifs</p>
    </div>
  </div>

  <!-- Tabs -->
  <div class="nm-tabs-wrap nm-anim-tabs">
    <div class="nm-tabs">
      <a href="objectif_list.php"    class="nm-tab">🎯 Mes Objectifs</a>
      <a href="mes_plans.php"        class="nm-tab">🥗 Mes Plans</a>
      <a href="mes_statistiques.php" class="nm-tab">📊 Statistiques</a>
      <a href="suivi_progression.php" class="nm-tab active">📈 Suivi IA</a>
      <a href="ai_coherence.php"     class="nm-tab ai-tab">🔍 Cohérence IA</a>
      <a href="ai_report.php"        class="nm-tab ai-tab">📋 Rapport IA</a>
      <a href="ai_mutation.php"      class="nm-tab ai-tab">🔄 Mutation IA</a>
    </div>
  </div>

  <div class="nm-content">
    <div class="container">

      <?php if ($error): ?>
        <div class="alert alert-danger" style="border-radius:12px;margin-bottom:1.5rem;">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <!-- Input form -->
      <div class="nm-card nm-anim-card mb-4" style="max-width:480px;">
        <div class="nm-card-header">
          <h5>⚖️ Entrez votre poids actuel</h5>
        </div>
        <div class="nm-card-body">
          <form method="POST">
            <div style="display:flex;gap:.75rem;align-items:flex-end;">
              <div style="flex:1;">
                <label style="font-size:.82rem;font-weight:600;color:var(--c-muted);display:block;margin-bottom:.4rem;">Poids actuel (kg)</label>
                <input type="number" name="poids_actuel" step="0.1" min="1" max="500"
                       value="<?php echo htmlspecialchars($poids_actuel); ?>"
                       placeholder="Ex: 74.5"
                       style="width:100%;padding:.6rem 1rem;border:1.5px solid var(--c-border);border-radius:10px;font-size:.95rem;outline:none;"
                       required>
              </div>
              <button type="submit" class="nm-btn nm-btn-brand" style="padding:.65rem 1.4rem;white-space:nowrap;">
                🤖 Analyser
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- AI Feedback -->
      <?php if ($feedback): ?>
        <?php
          $verdictColor = match($feedback['verdict'] ?? '') {
            'Objectif atteint'  => 'linear-gradient(135deg,#10b981,#34d399)',
            'En bonne voie'     => 'linear-gradient(135deg,#6366f1,#818cf8)',
            'Attention requise' => 'linear-gradient(135deg,#f59e0b,#fbbf24)',
            default             => 'linear-gradient(135deg,#64748b,#94a3b8)'
          };
        ?>
        <div class="nm-card nm-anim-card" style="max-width:480px;overflow:hidden;">
          <!-- Colored header -->
          <div style="background:<?php echo $verdictColor; ?>;padding:1.5rem;text-align:center;">
            <div style="font-size:3rem;margin-bottom:.5rem;"><?php echo htmlspecialchars($feedback['emoji'] ?? '📊'); ?></div>
            <div style="color:#fff;font-size:1.2rem;font-weight:800;"><?php echo htmlspecialchars($feedback['verdict'] ?? ''); ?></div>
            <div style="color:rgba(255,255,255,.75);font-size:.82rem;margin-top:.3rem;">Poids actuel : <?php echo $poids_actuel; ?> kg</div>
          </div>
          <!-- Content -->
          <div class="nm-card-body">
            <div style="margin-bottom:1rem;">
              <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--c-subtle);margin-bottom:.4rem;">📈 Progression actuelle</div>
              <p style="color:var(--c-text);font-size:.92rem;line-height:1.65;margin:0;"><?php echo htmlspecialchars($feedback['progression'] ?? ''); ?></p>
            </div>
            <?php if(!empty($feedback['prediction'])): ?>
            <div style="margin-bottom:1rem;background:#f0f9ff;border-radius:10px;padding:1rem;border-left:4px solid #0ea5e9;">
              <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#0369a1;margin-bottom:.4rem;">🎯 Prédiction d'atteinte</div>
              <p style="color:#0c4a6e;font-size:.92rem;line-height:1.65;margin:0;"><?php echo htmlspecialchars($feedback['prediction']); ?></p>
            </div>
            <?php endif; ?>
            <?php if(!empty($feedback['periode'])): ?>
            <div style="margin-bottom:1rem;background:#fdf4ff;border-radius:10px;padding:1rem;border-left:4px solid #a855f7;">
              <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#7e22ce;margin-bottom:.4rem;">🔮 Ce qui vous attend</div>
              <p style="color:#581c87;font-size:.92rem;line-height:1.65;margin:0;"><?php echo htmlspecialchars($feedback['periode']); ?></p>
            </div>
            <?php endif; ?>
            <div style="background:#f8fafc;border-radius:10px;padding:1rem;border-left:4px solid var(--c-brand);">
              <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--c-subtle);margin-bottom:.4rem;">💡 Conseil</div>
              <p style="color:var(--c-text);font-size:.92rem;line-height:1.65;margin:0;"><?php echo htmlspecialchars($feedback['conseil'] ?? ''); ?></p>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Objectives summary -->
      <?php if (!empty($objectives)): ?>
        <div class="nm-card nm-anim-card-2 mt-4" style="max-width:480px;">
          <div class="nm-card-header"><h5>🎯 Vos objectifs</h5></div>
          <div class="nm-card-body" style="padding:0;">
            <?php foreach ($objectives as $obj): ?>
              <?php
                $pct = 0;
                if ($obj['poids_initial'] && $obj['valeur_cible'] && $poids_actuel) {
                    $range = abs($obj['poids_initial'] - $obj['valeur_cible']);
                    $done  = abs($obj['poids_initial'] - $poids_actuel);
                    $pct   = $range > 0 ? min(100, round($done / $range * 100)) : 0;
                }
                $days = $obj['date_limite'] ? max(0, (strtotime($obj['date_limite']) - time()) / 86400) : null;
              ?>
              <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--c-border);">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
                  <span style="font-weight:600;font-size:.88rem;color:var(--c-text);"><?php echo htmlspecialchars(ucwords(str_replace('_',' ',$obj['type_objectif']))); ?></span>
                  <span style="font-size:.78rem;color:var(--c-muted);">Cible : <?php echo $obj['valeur_cible']; ?> kg</span>
                </div>
                <?php if ($poids_actuel && $obj['poids_initial']): ?>
                  <div style="height:8px;background:#f1f5f9;border-radius:99px;overflow:hidden;margin-bottom:.4rem;">
                    <div style="height:100%;width:<?php echo $pct; ?>%;background:linear-gradient(90deg,#6366f1,#10b981);border-radius:99px;transition:width 1s ease;"></div>
                  </div>
                  <div style="font-size:.75rem;color:var(--c-muted);"><?php echo $pct; ?>% accompli</div>
                <?php endif; ?>
                <?php if ($days !== null): ?>
                  <div style="font-size:.75rem;color:<?php echo $days < 7 ? '#ef4444' : 'var(--c-muted)'; ?>;margin-top:.2rem;">
                    <?php echo $days < 1 ? '⚠️ Date dépassée' : '⏳ '.round($days).' jours restants'; ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
