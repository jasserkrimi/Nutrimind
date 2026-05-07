<?php
session_start();
if (!isset($_SESSION['user_id']))     { header('Location: auth.php');         exit; }
if (empty($_SESSION['ai_coherence'])) { header('Location: objectif_list.php'); exit; }

$data    = $_SESSION['ai_coherence'];
$result  = $data['result'];
$score   = max(0, min(100, (int)($result['score_global'] ?? 0)));
$verdict = $result['verdict'] ?? '—';
$nom     = htmlspecialchars($data['user_nom']);
$date    = htmlspecialchars($data['generated_at']);

$scoreColor = $score >= 75 ? '#10b981' : ($score >= 45 ? '#f59e0b' : '#ef4444');
$scoreGrad  = $score >= 75
  ? 'linear-gradient(135deg,#10b981,#34d399)'
  : ($score >= 45 ? 'linear-gradient(135deg,#f59e0b,#fbbf24)' : 'linear-gradient(135deg,#ef4444,#f87171)');
$verdictClass = $score >= 75 ? 'nm-badge-green' : ($score >= 45 ? 'nm-badge-amber' : 'nm-badge-red');

// SVG ring params
$r = 54; $cx = 70; $cy = 70;
$circ = 2 * M_PI * $r;
$dash = $circ * $score / 100;
?>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="assets/css/nutrimind-ui.css">

<style>
  .score-svg-ring { transform: rotate(-90deg); }
  .score-track { fill: none; stroke: #f1f5f9; stroke-width: 10; }
  .score-fill  { fill: none; stroke-width: 10; stroke-linecap: round;
    stroke-dasharray: <?php echo $dash; ?> <?php echo $circ; ?>;
    stroke: <?php echo $scoreColor; ?>;
    animation: drawRing 1.2s cubic-bezier(.4,0,.2,1) both .3s;
  }
  @keyframes drawRing {
    from { stroke-dasharray: 0 <?php echo $circ; ?>; }
    to   { stroke-dasharray: <?php echo $dash; ?> <?php echo $circ; ?>; }
  }
  .score-text-wrap {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
  }
  .score-num { font-size: 2rem; font-weight: 900; color: var(--c-text); line-height: 1; }
  .score-sub { font-size: .68rem; font-weight: 600; color: var(--c-subtle); }
</style>

<div class="nm-page">

  <!-- Hero -->
  <div class="nm-hero nm-anim-hero">
    <div class="nm-hero-inner">
      <div class="nm-hero-eyebrow">🔍 Intelligence Artificielle</div>
      <h1>Analyse de <span>Cohérence</span></h1>
      <p>Compatibilité entre l'objectif et le plan sélectionnés</p>
      <div class="nm-hero-meta">
        <span class="nm-hero-badge">👤 <?php echo $nom; ?></span>
        <span class="nm-hero-badge">🎯 <?php echo htmlspecialchars($data['objectif'] ?? ($data['nb_obj'].' objectif')); ?></span>
        <span class="nm-hero-badge">🥗 <?php echo htmlspecialchars($data['plan'] ?? ($data['nb_plans'].' plan')); ?></span>
        <span class="nm-hero-badge">🕐 <?php echo $date; ?></span>
      </div>
    </div>
  </div>

  <!-- Sticky tabs -->
  <div class="nm-tabs-wrap nm-anim-tabs">
    <div class="nm-tabs">
      <a href="objectif_list.php"    class="nm-tab">🎯 Mes Objectifs</a>
      <a href="mes_plans.php"        class="nm-tab">🥗 Mes Plans</a>
      <a href="mes_statistiques.php" class="nm-tab">📊 Statistiques</a>
      <a href="ai_coherence.php"     class="nm-tab ai-tab active">🔍 Cohérence IA</a>
      <a href="ai_report.php"        class="nm-tab ai-tab">📋 Rapport IA</a>
      <a href="ai_mutation.php"      class="nm-tab ai-tab">🔄 Mutation IA</a>
    </div>
  </div>

  <div class="nm-content">
    <div class="container">

      <!-- Action bar -->
      <div class="d-flex flex-wrap gap-2 justify-content-end mb-4 nm-anim-card">
        <a href="ai_coherence.php" class="nm-btn nm-btn-indigo">🔄 Nouvelle analyse</a>
        <a href="objectif_list.php" class="nm-btn nm-btn-ghost">← Retour</a>
      </div>

      <!-- Score card -->
      <div class="nm-card mb-4 nm-anim-card">
        <div class="nm-card-body">
          <div class="d-flex flex-wrap align-items-center gap-4">

            <!-- SVG ring -->
            <div style="position:relative;width:140px;height:140px;flex-shrink:0;">
              <svg width="140" height="140" class="score-svg-ring">
                <circle class="score-track" cx="70" cy="70" r="<?php echo $r; ?>"/>
                <circle class="score-fill"  cx="70" cy="70" r="<?php echo $r; ?>"/>
              </svg>
              <div class="score-text-wrap">
                <div class="score-num"><?php echo $score; ?></div>
                <div class="score-sub">/ 100</div>
              </div>
            </div>

            <!-- Info -->
            <div class="flex-grow-1">
              <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                <h4 style="font-weight:800;font-size:1.3rem;color:var(--c-text);margin:0;">Score de cohérence</h4>
                <span class="nm-badge <?php echo $verdictClass; ?>" style="font-size:.8rem;"><?php echo htmlspecialchars($verdict); ?></span>
              </div>
              <!-- Progress bar -->
              <div class="nm-progress mb-3" style="height:10px;">
                <div class="nm-progress-fill" style="width:0;background:<?php echo $scoreGrad; ?>;" data-width="<?php echo $score; ?>"></div>
              </div>
              <p style="color:var(--c-muted);line-height:1.7;margin:0;font-size:.92rem;">
                <?php echo htmlspecialchars($result['resume'] ?? ''); ?>
              </p>
            </div>

          </div>
        </div>
      </div>

      <!-- Points forts + Incompatibilités -->
      <div class="row g-3 mb-4">
        <div class="col-lg-6 nm-anim-card">
          <div class="nm-card h-100">
            <div class="nm-card-header">
              <h5>✅ Points forts</h5>
              <span class="nm-badge nm-badge-green"><?php echo count($result['points_forts']??[]); ?></span>
            </div>
            <div class="nm-card-body">
              <?php if(!empty($result['points_forts'])): ?>
                <?php foreach($result['points_forts'] as $pt): ?>
                  <div class="nm-check-item">
                    <span>✓</span>
                    <span><?php echo htmlspecialchars($pt); ?></span>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p style="color:var(--c-muted);font-size:.88rem;">Aucun point fort identifié.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="col-lg-6 nm-anim-card-2">
          <div class="nm-card h-100">
            <div class="nm-card-header">
              <h5>⚠️ Incompatibilités</h5>
              <span class="nm-badge nm-badge-red"><?php echo count($result['incompatibilites']??[]); ?></span>
            </div>
            <div class="nm-card-body">
              <?php if(!empty($result['incompatibilites'])): ?>
                <?php foreach($result['incompatibilites'] as $inc): ?>
                  <div class="nm-warn-item">
                    <i class="fas fa-times"></i>
                    <span><?php echo htmlspecialchars($inc); ?></span>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="nm-check-item"><i class="fas fa-check"></i><span>Aucune incompatibilité détectée !</span></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Ajustements -->
      <?php if(!empty($result['ajustements'])): ?>
      <div class="nm-card mb-4 nm-anim-card-3">
        <div class="nm-card-header">
          <h5><i class="fas fa-sliders-h" style="color:var(--c-indigo);"></i> Ajustements recommandés</h5>
          <span class="nm-badge nm-badge-indigo"><?php echo count($result['ajustements']); ?></span>
        </div>
        <div class="nm-card-body">
          <div class="row g-2">
            <?php foreach($result['ajustements'] as $adj): ?>
              <div class="col-lg-6">
                <div class="nm-fix-item">
                  <i class="fas fa-arrow-right"></i>
                  <span><?php echo htmlspecialchars($adj); ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Conseil final -->
      <?php if(!empty($result['conseil_final'])): ?>
      <div class="nm-card mb-4 nm-anim-card-3" style="background:linear-gradient(135deg,#0f172a,#1e293b);border:none;">
        <div class="nm-card-body">
          <div class="d-flex align-items-start gap-3">
            <div style="font-size:2rem;flex-shrink:0;line-height:1;">💡</div>
            <div>
              <h5 style="color:#fff;font-weight:700;margin-bottom:.5rem;">Conseil personnalisé</h5>
              <p style="color:rgba(255,255,255,.65);margin:0;line-height:1.75;font-size:.92rem;">
                <?php echo htmlspecialchars($result['conseil_final']); ?>
              </p>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Bottom CTA -->
      <div class="text-center nm-anim-card-3">
        <a href="ai_coherence.php" class="nm-btn nm-btn-indigo nm-btn-lg me-2">
          <i class="fas fa-sync-alt"></i> Régénérer l'analyse
        </a>
        <a href="objectif_list.php" class="nm-btn nm-btn-ghost nm-btn-lg">
          <i class="fas fa-arrow-left"></i> Retour aux objectifs
        </a>
      </div>

    </div>
  </div>
</div>

<script>
  setTimeout(() => {
    document.querySelectorAll('.nm-progress-fill').forEach(el => {
      el.style.width = el.dataset.width + '%';
    });
  }, 400);
</script>

<?php include 'footer.php'; ?>
