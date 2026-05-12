<?php
session_start();
require_once '../controllers/PlanningController.php';
if (!isset($_SESSION['user_id'])) { header('Location: auth.php'); exit; }

$planningController = new PlanningController();
$plans = $planningController->getAllForUser($_SESSION['user_id']);

$statusCfg = [
    'actif'   => ['label'=>'Actif',   'class'=>'nm-badge-green',  'dot'=>'#10b981'],
    'inactif' => ['label'=>'Inactif', 'class'=>'nm-badge-slate',  'dot'=>'#94a3b8'],
    'termine' => ['label'=>'Terminé', 'class'=>'nm-badge-blue',   'dot'=>'#3b82f6'],
];
?>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="assets/css/nutrimind-ui.css">

<div class="nm-page">

  <!-- Hero -->
  <div class="nm-hero nm-anim-hero">
    <div class="nm-hero-inner">
      <div class="nm-hero-eyebrow">🥗 Nutrition</div>
      <h1>Mes Plans <span>Nutritionnels</span></h1>
      <p>Vos programmes personnalisés créés par nos experts en nutrition</p>
      <div class="nm-hero-meta">
        <span class="nm-hero-badge">📋 <?php echo count($plans); ?> plan<?php echo count($plans)!==1?'s':''; ?> assigné<?php echo count($plans)!==1?'s':''; ?></span>
      </div>
    </div>
  </div>

  <!-- Sticky tabs -->
  <div class="nm-tabs-wrap nm-anim-tabs">
    <div class="nm-tabs">
      <a href="objectif_list.php"    class="nm-tab">🎯 Mes Objectifs</a>
      <a href="mes_plans.php"        class="nm-tab active">🥗 Mes Plans</a>
      <a href="mes_statistiques.php" class="nm-tab">📊 Statistiques</a>
      <a href="suivi_progression.php" class="nm-tab">📈 Suivi IA</a>
      <a href="bilan_bienetre.php"   class="nm-tab">🧘 Bilan</a>
      <a href="ai_coherence.php"     class="nm-tab ai-tab">🔍 Cohérence IA</a>
      <a href="ai_report.php"        class="nm-tab ai-tab">📋 Rapport IA</a>
      <a href="ai_mutation.php"      class="nm-tab ai-tab">🔄 Mutation IA</a>
    </div>
  </div>

  <div class="nm-content">
    <div class="container">

      <?php if(empty($plans)): ?>
        <div class="nm-card nm-anim-card">
          <div class="nm-card-body">
            <div class="nm-empty">
              <span class="nm-empty-emoji">📋</span>
              <h5>Aucun plan assigné</h5>
              <p>Aucun plan nutritionnel ne vous a encore été assigné. Contactez un expert NutriMind.</p>
            </div>
          </div>
        </div>
      <?php else: ?>

        <!-- Search toolbar -->
        <div class="nm-card nm-anim-card mb-4" style="border-radius:var(--r-md);">
          <div class="nm-toolbar" style="border-radius:var(--r-md);">
            <div class="nm-search">
            <span class="nm-search-icon" style="font-style:normal;">🔍</span>
            <input type="text" id="planSearch" placeholder="Rechercher un plan…">
            </div>
            <span class="nm-badge nm-badge-slate"><?php echo count($plans); ?> plan<?php echo count($plans)!==1?'s':''; ?></span>
          </div>
        </div>

        <!-- Plan cards grid -->
        <div class="row g-3" id="plansGrid">
          <?php foreach($plans as $i => $plan):
            $s = $statusCfg[$plan['statut']] ?? ['label'=>$plan['statut']??'—','class'=>'nm-badge-slate','dot'=>'#94a3b8'];
            $delay = min($i * 0.08, 0.4);
            $metrics = [
              ['icon'=>'fa-fire',          'color'=>'#ef4444', 'bg'=>'#fee2e2', 'lbl'=>'Calories',      'val'=>($plan['calories_par_jour']??'—').' kcal'],
              ['icon'=>'fa-drumstick-bite','color'=>'#6366f1', 'bg'=>'#ede9fe', 'lbl'=>'Protéines',     'val'=>($plan['objectif_proteines']??'—').'g'],
              ['icon'=>'fa-bread-slice',   'color'=>'#f59e0b', 'bg'=>'#fef9c3', 'lbl'=>'Glucides',      'val'=>($plan['objectif_glucides']??'—').'g'],
              ['icon'=>'fa-tint',          'color'=>'#10b981', 'bg'=>'#dcfce7', 'lbl'=>'Lipides',       'val'=>($plan['objectif_lipides']??'—').'g'],
              ['icon'=>'fa-utensils',      'color'=>'#e07b39', 'bg'=>'#fef3e8', 'lbl'=>'Repas/jour',    'val'=>$plan['nombre_repas_par_jour']??'—'],
              ['icon'=>'fa-moon',          'color'=>'#6366f1', 'bg'=>'#ede9fe', 'lbl'=>'Sommeil',       'val'=>($plan['heures_sommeil_par_jour']??'—').'h'],
              ['icon'=>'fa-dumbbell',      'color'=>'#10b981', 'bg'=>'#dcfce7', 'lbl'=>'Entraînement',  'val'=>($plan['heures_entrainement_par_jour']??'—').'h'],
            ];
          ?>
          <div class="col-lg-6 plan-card-wrap" style="animation:nm-fadeUp .5s ease <?php echo $delay; ?>s both;">
            <div class="nm-card h-100">
              <!-- Card header -->
              <div class="nm-card-header">
                <div style="min-width:0;">
                  <h5 style="font-size:1rem;margin-bottom:.2rem;">
                    <?php echo htmlspecialchars($plan['titre'] ?? 'Plan sans titre'); ?>
                  </h5>
                  <div style="font-size:.78rem;color:var(--c-subtle);display:flex;align-items:center;gap:.5rem;">
                    <i class="fas fa-calendar-alt" style="display:none;"></i>
                    <?php echo htmlspecialchars($plan['date_debut']??'—'); ?> → <?php echo htmlspecialchars($plan['date_fin']??'—'); ?>
                  </div>
                </div>
                <span class="nm-badge <?php echo $s['class']; ?>"><?php echo $s['label']; ?></span>
              </div>
              <!-- Metrics grid -->
              <div class="nm-card-body">
                <div class="row g-2">
                  <?php foreach($metrics as $m): ?>
                    <div class="col-6 col-md-4">
                      <div class="nm-metric">
                        <div class="nm-metric-icon" style="background:<?php echo $m['bg']; ?>;color:<?php echo $m['color']; ?>;">
                          <i class="fas <?php echo $m['icon']; ?>"></i>
                        </div>
                        <div>
                          <div class="nm-metric-lbl"><?php echo $m['lbl']; ?></div>
                          <div class="nm-metric-val"><?php echo $m['val']; ?></div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  document.getElementById('planSearch') && document.getElementById('planSearch').addEventListener('keyup', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.plan-card-wrap').forEach(c => {
      c.style.display = c.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });
</script>

<?php include 'footer.php'; ?>
