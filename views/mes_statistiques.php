<?php
session_start();
require_once '../controllers/ObjectiveController.php';
if (!isset($_SESSION['user_id'])) { header('Location: auth.php'); exit; }

$objectiveController = new ObjectiveController();
$objectives = $objectiveController->getAllForUser($_SESSION['user_id']);
$total  = count($objectives);
$counts = [];
foreach ($objectives as $obj) {
    $s = $obj['statut'] ?? 'inconnu';
    $counts[$s] = ($counts[$s] ?? 0) + 1;
}
$statCfg = [
    'en_attente' => ['label'=>'En attente','icon'=>'fa-clock',        'grad'=>'linear-gradient(135deg,#f59e0b,#fbbf24)','color'=>'#f59e0b'],
    'en_cours'   => ['label'=>'En cours',  'icon'=>'fa-spinner',      'grad'=>'linear-gradient(135deg,#6366f1,#818cf8)','color'=>'#6366f1'],
    'termine'    => ['label'=>'Terminé',   'icon'=>'fa-check-circle', 'grad'=>'linear-gradient(135deg,#10b981,#34d399)','color'=>'#10b981'],
    'annule'     => ['label'=>'Annulé',    'icon'=>'fa-times-circle', 'grad'=>'linear-gradient(135deg,#ef4444,#f87171)','color'=>'#ef4444'],
];
?>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="assets/css/nutrimind-ui.css">

<div class="nm-page">

  <!-- Hero -->
  <div class="nm-hero nm-anim-hero">
    <div class="nm-hero-inner">
      <div class="nm-hero-eyebrow">📊 Tableau de bord</div>
      <h1>Mes <span>Statistiques</span></h1>
      <p>Vue d'ensemble de votre progression et de vos objectifs</p>
      <div class="nm-hero-meta">
        <span class="nm-hero-badge">🎯 <?php echo $total; ?> objectif<?php echo $total!==1?'s':''; ?></span>
      </div>
    </div>
  </div>

  <!-- Sticky tabs -->
  <div class="nm-tabs-wrap nm-anim-tabs">
    <div class="nm-tabs">
      <a href="objectif_list.php"    class="nm-tab">🎯 Mes Objectifs</a>
      <a href="mes_plans.php"        class="nm-tab">🥗 Mes Plans</a>
      <a href="mes_statistiques.php" class="nm-tab active">📊 Statistiques</a>
      <a href="suivi_progression.php" class="nm-tab">📈 Suivi IA</a>
      <a href="ai_coherence.php"     class="nm-tab ai-tab">🔍 Cohérence IA</a>
      <a href="ai_report.php"        class="nm-tab ai-tab">📋 Rapport IA</a>
      <a href="ai_mutation.php"      class="nm-tab ai-tab">🔄 Mutation IA</a>
    </div>
  </div>

  <div class="nm-content">
    <div class="container">

      <?php if($total === 0): ?>
        <div class="nm-card nm-anim-card">
          <div class="nm-card-body">
            <div class="nm-empty">
              <span class="nm-empty-emoji">📊</span>
              <h5>Aucune donnée disponible</h5>
              <p>Créez des objectifs pour voir vos statistiques apparaître ici.</p>
              <a href="objectif_create.php" class="nm-btn nm-btn-brand">＋ Créer un objectif</a>
            </div>
          </div>
        </div>
      <?php else: ?>

      <!-- Stat cards -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 nm-anim-stat">
          <div class="nm-stat-card" style="background:linear-gradient(135deg,#0f172a,#1e293b);">
            <div class="nm-stat-icon-wrap">📋</div>
            <div class="nm-stat-val counter" data-target="<?php echo $total; ?>">0</div>
            <div class="nm-stat-lbl">Total objectifs</div>
          </div>
        </div>
        <?php foreach($statCfg as $key => $cfg): ?>
          <div class="col-6 col-md-3 nm-anim-stat">
            <div class="nm-stat-card" style="background:<?php echo $cfg['grad']; ?>;">
              <div class="nm-stat-icon-wrap"><i class="fas <?php echo $cfg['icon']; ?>"></i></div>
              <div class="nm-stat-val counter" data-target="<?php echo $counts[$key]??0; ?>">0</div>
              <div class="nm-stat-lbl"><?php echo $cfg['label']; ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Chart + breakdown -->
      <div class="row g-3">
        <div class="col-lg-5 nm-anim-card">
          <div class="nm-card h-100">
            <div class="nm-card-header">
              <h5>📊 Répartition par statut</h5>
            </div>
            <div class="nm-card-body d-flex align-items-center justify-content-center" style="min-height:280px;">
              <canvas id="statsPie" style="max-width:260px;max-height:260px;"></canvas>
            </div>
          </div>
        </div>

        <div class="col-lg-7 nm-anim-card-2">
          <div class="nm-card h-100">
            <div class="nm-card-header">
              <h5>📊 Détail par statut</h5>
            </div>
            <div class="nm-card-body">
              <?php foreach($statCfg as $key => $cfg):
                $cnt = $counts[$key] ?? 0;
                $pct = $total > 0 ? round($cnt / $total * 100) : 0;
              ?>
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div style="display:flex;align-items:center;gap:.6rem;">
                    <div style="width:10px;height:10px;border-radius:50%;background:<?php echo $cfg['color']; ?>;flex-shrink:0;"></div>
                    <span style="font-size:.88rem;font-weight:600;color:var(--c-text);"><?php echo $cfg['label']; ?></span>
                  </div>
                  <div style="display:flex;align-items:center;gap:.75rem;">
                    <span style="font-size:.82rem;color:var(--c-muted);"><?php echo $cnt; ?> objectif<?php echo $cnt!==1?'s':''; ?></span>
                    <span style="font-size:.82rem;font-weight:700;color:var(--c-text);min-width:36px;text-align:right;"><?php echo $pct; ?>%</span>
                  </div>
                </div>
                <div class="nm-progress">
                  <div class="nm-progress-fill" style="width:0;background:<?php echo $cfg['grad']; ?>;" data-width="<?php echo $pct; ?>"></div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
  // Counters
  document.querySelectorAll('.counter').forEach(el => {
    const t = parseInt(el.dataset.target, 10);
    let c = 0;
    const timer = setInterval(() => {
      c += Math.max(1, Math.ceil(t / 40));
      if (c >= t) { el.textContent = t; clearInterval(timer); } else el.textContent = c;
    }, 22);
  });

  // Progress bars
  setTimeout(() => {
    document.querySelectorAll('.nm-progress-fill').forEach(el => {
      el.style.width = el.dataset.width + '%';
    });
  }, 300);

  // Chart
  (function() {
    const objectives = <?php echo json_encode($objectives); ?>;
    if (!objectives.length) return;
    const statuts = {};
    objectives.forEach(o => { const s = o.statut||'Inconnu'; statuts[s]=(statuts[s]||0)+1; });
    const colorMap = {'en_attente':'#f59e0b','en_cours':'#6366f1','termine':'#10b981','annule':'#ef4444'};
    const labels = Object.keys(statuts).map(l => l.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase()));
    const data   = Object.values(statuts);
    const colors = Object.keys(statuts).map(l => colorMap[l]||'#94a3b8');
    new Chart(document.getElementById('statsPie').getContext('2d'), {
      type: 'doughnut',
      data: { labels, datasets: [{ data, backgroundColor: colors, borderColor:'#fff', borderWidth:4, hoverOffset:12 }] },
      options: {
        responsive: true, cutout:'65%',
        animation: { animateRotate:true, animateScale:true, duration:1000, easing:'easeOutQuart' },
        plugins: {
          legend: { position:'bottom', labels:{ padding:18, font:{size:12,family:'Inter'}, usePointStyle:true, pointStyleWidth:8 } },
          tooltip: { callbacks: { label: ctx => ' '+ctx.label+': '+ctx.parsed+' objectif'+(ctx.parsed!==1?'s':'') } }
        }
      }
    });
  })();
</script>

<?php include 'footer.php'; ?>
