<?php
// Single view for all three AI plan tabs: sport, sommeil, nutrition.
// Usage: ai_plan_view.php?tab=sport  (or sommeil / nutrition)
session_start();
if (!isset($_SESSION['user_id']))  { header('Location: auth.php');         exit; }
if (empty($_SESSION['ai_plans']))  { header('Location: objectif_list.php'); exit; }

require_once 'ai_render.php';

$plans    = $_SESSION['ai_plans'];
$objectif = htmlspecialchars($plans['objectif']);
$nom      = htmlspecialchars($plans['user_nom']);
$date     = htmlspecialchars($plans['generated_at']);

// Tab configuration: key => [emoji, label, subtitle, accent color, gradient]
$tabs = [
    'sport'     => ['🏋️', 'Plan Sportif',   'Exercices, fréquence, intensité & récupération', '#f59e0b', 'linear-gradient(135deg,#f59e0b,#fbbf24)'],
    'sommeil'   => ['🌙', 'Plan Sommeil',   'Horaires, routines & qualité du sommeil',         '#6366f1', 'linear-gradient(135deg,#6366f1,#818cf8)'],
    'nutrition' => ['🥗', 'Plan Nutrition', 'Repas, macronutriments & hydratation',            '#10b981', 'linear-gradient(135deg,#10b981,#34d399)'],
];

$activeTab = $_GET['tab'] ?? 'sport';
if (!array_key_exists($activeTab, $tabs)) $activeTab = 'sport';

[$icon, $label, $subtitle, $color, $gradient] = $tabs[$activeTab];
$content = $plans[$activeTab] ?? '';
?>
<?php include 'header.php'; ?>

<style>
<?php include 'ai_styles.css.php'; ?>
</style>

<div class="product-section mt-150 mb-150">
  <div class="container">

    <!-- Page heading -->
    <div class="row mb-3">
      <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">
          <h3><span class="orange-text">IA</span> Planning Personnalisé</h3>
          <p class="ai-meta-top">
            👤 <strong><?php echo $nom; ?></strong>
            &nbsp;·&nbsp;
            🎯 <strong><?php echo $objectif; ?></strong>
            &nbsp;·&nbsp;
            🕐 <?php echo $date; ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Tab navigation -->
    <div class="row mb-5">
      <div class="col-12 text-center d-flex flex-wrap justify-content-center gap-2">
        <?php foreach ($tabs as $key => $tabCfg): ?>
          <a href="ai_plan_view.php?tab=<?php echo $key; ?>"
             class="ai-tab <?php echo $key === $activeTab ? 'active' : ''; ?>"
             style="--c:<?php echo $tabCfg[3]; ?>;--cs:<?php echo $tabCfg[3]; ?>;">
            <?php echo $tabCfg[0] . ' ' . $tabCfg[1]; ?>
          </a>
        <?php endforeach; ?>
        <a href="objectif_list.php" class="ai-tab-back">← Retour</a>
      </div>
    </div>

    <!-- Plan content card -->
    <div class="row">
      <div class="col-lg-10 offset-lg-1">
        <div class="ai-card" style="--accent:<?php echo $color; ?>;">
          <div class="ai-card-header" style="background:<?php echo $gradient; ?>;">
            <span class="ai-card-icon"><?php echo $icon; ?></span>
            <div>
              <h4 class="ai-card-title"><?php echo htmlspecialchars($label); ?></h4>
              <p class="ai-card-sub"><?php echo htmlspecialchars($subtitle); ?></p>
            </div>
          </div>
          <div class="ai-card-body">
            <?php echo renderAIPlan($content); ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>
