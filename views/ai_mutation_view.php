<?php
session_start();
if (!isset($_SESSION['user_id']))    { header('Location: auth.php');       exit; }
if (empty($_SESSION['ai_mutation'])) { header('Location: ai_mutation.php'); exit; }

$data   = $_SESSION['ai_mutation'];
$result = $data['result'];
$nom    = htmlspecialchars($data['user_nom']);
$date   = htmlspecialchars($data['generated_at']);
?>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="assets/css/nutrimind-ui.css">

<div class="nm-page">

  <!-- Hero -->
  <div class="nm-hero nm-anim-hero">
    <div class="nm-hero-inner">
      <div class="nm-hero-eyebrow">🔄 Mutation IA</div>
      <h1>Plan <span>Muté</span></h1>
      <p>Votre plan a été réécrit pour contourner votre obstacle</p>
      <div class="nm-hero-meta">
        <span class="nm-hero-badge">👤 <?php echo $nom; ?></span>
        <span class="nm-hero-badge">📋 <?php echo htmlspecialchars($data['plan_titre']); ?></span>
        <span class="nm-hero-badge"><?php echo $data['obstacle_emoji']; ?> <?php echo htmlspecialchars($data['obstacle_label']); ?></span>
        <span class="nm-hero-badge">📅 <?php echo htmlspecialchars($data['duree']); ?></span>
        <span class="nm-hero-badge">🕐 <?php echo $date; ?></span>
      </div>
    </div>
  </div>

  <!-- Tabs -->
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

      <!-- Action bar -->
      <div class="d-flex flex-wrap gap-2 justify-content-end mb-4 nm-anim-card">
        <a href="ai_mutation.php" class="nm-btn nm-btn-brand">🔄 Nouvelle mutation</a>
        <a href="mes_plans.php"   class="nm-btn nm-btn-ghost">← Mes plans</a>
      </div>

      <!-- Before / After header -->
      <div class="row g-3 mb-4 nm-anim-card">
        <div class="col-md-6">
          <div style="padding:1.2rem 1.4rem;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;">
            <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Plan original</div>
            <div style="font-weight:800;font-size:1rem;color:#1e293b;"><?php echo htmlspecialchars($data['plan_titre']); ?></div>
            <div style="font-size:.82rem;color:#64748b;margin-top:.3rem;"><?php echo $data['plan_calories']; ?> kcal/j</div>
          </div>
        </div>
        <div class="col-md-6">
          <div style="padding:1.2rem 1.4rem;background:linear-gradient(135deg,#fff7f0,#ffedd5);border:1.5px solid #fed7aa;border-radius:14px;">
            <div style="font-size:.72rem;font-weight:700;color:#c2410c;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Plan muté · <?php echo htmlspecialchars($data['duree']); ?></div>
            <div style="font-weight:800;font-size:1rem;color:#1e293b;"><?php echo htmlspecialchars($result['titre_mute'] ?? ''); ?></div>
            <div style="font-size:.82rem;color:#64748b;margin-top:.3rem;"><?php echo $result['calories_adaptees'] ?? '—'; ?> kcal/j</div>
          </div>
        </div>
      </div>

      <!-- Resume -->
      <div class="nm-card mb-4 nm-anim-card">
        <div class="nm-card-header">
          <h5><?php echo $data['obstacle_emoji']; ?> Adaptation pour : <?php echo htmlspecialchars($data['obstacle_label']); ?></h5>
        </div>
        <div class="nm-card-body">
          <p style="color:var(--c-muted);line-height:1.8;margin:0;font-size:.93rem;">
            <?php echo htmlspecialchars($result['resume_adaptation'] ?? ''); ?>
          </p>
        </div>
      </div>

      <!-- 3 plan sections: sport, nutrition, sleep -->
      <div class="row g-3 mb-4">

        <div class="col-lg-4 nm-anim-card">
          <div class="nm-card h-100">
            <div class="nm-card-header" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
              <h5 style="color:#fff;margin:0;">🏋️ Sport adapté</h5>
            </div>
            <div class="nm-card-body">
              <p style="color:#374151;line-height:1.75;font-size:.88rem;margin:0;">
                <?php echo htmlspecialchars($result['sport_adapte'] ?? '—'); ?>
              </p>
            </div>
          </div>
        </div>

        <div class="col-lg-4 nm-anim-card-2">
          <div class="nm-card h-100">
            <div class="nm-card-header" style="background:linear-gradient(135deg,#10b981,#34d399);">
              <h5 style="color:#fff;margin:0;">🥗 Nutrition adaptée</h5>
            </div>
            <div class="nm-card-body">
              <p style="color:#374151;line-height:1.75;font-size:.88rem;margin:0;">
                <?php echo htmlspecialchars($result['nutrition_adaptee'] ?? '—'); ?>
              </p>
            </div>
          </div>
        </div>

        <div class="col-lg-4 nm-anim-card-3">
          <div class="nm-card h-100">
            <div class="nm-card-header" style="background:linear-gradient(135deg,#6366f1,#818cf8);">
              <h5 style="color:#fff;margin:0;">🌙 Sommeil adapté</h5>
            </div>
            <div class="nm-card-body">
              <p style="color:#374151;line-height:1.75;font-size:.88rem;margin:0;">
                <?php echo htmlspecialchars($result['sommeil_adapte'] ?? '—'); ?>
              </p>
            </div>
          </div>
        </div>

      </div>

      <!-- What changes vs what stays -->
      <div class="row g-3 mb-4">

        <div class="col-md-6 nm-anim-card">
          <div class="nm-card h-100">
            <div class="nm-card-header">
              <h5>🔀 Ce qui change</h5>
              <span class="nm-badge nm-badge-red"><?php echo count($result['ce_qui_change'] ?? []); ?></span>
            </div>
            <div class="nm-card-body">
              <?php foreach (($result['ce_qui_change'] ?? []) as $item): ?>
                <div style="display:flex;gap:.6rem;align-items:flex-start;margin-bottom:.5rem;padding:.6rem .8rem;background:#fef2f2;border-radius:8px;border:1px solid #fecaca;">
                  <span style="color:#ef4444;flex-shrink:0;font-weight:700;">✕</span>
                  <span style="font-size:.87rem;color:#991b1b;"><?php echo htmlspecialchars($item); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="col-md-6 nm-anim-card-2">
          <div class="nm-card h-100">
            <div class="nm-card-header">
              <h5>✅ Ce qui reste</h5>
              <span class="nm-badge nm-badge-green"><?php echo count($result['ce_qui_reste'] ?? []); ?></span>
            </div>
            <div class="nm-card-body">
              <?php foreach (($result['ce_qui_reste'] ?? []) as $item): ?>
                <div style="display:flex;gap:.6rem;align-items:flex-start;margin-bottom:.5rem;padding:.6rem .8rem;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;">
                  <span style="color:#10b981;flex-shrink:0;font-weight:700;">✓</span>
                  <span style="font-size:.87rem;color:#065f46;"><?php echo htmlspecialchars($item); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      </div>

      <!-- Warnings -->
      <?php if (!empty($result['avertissements'])): ?>
      <div class="nm-card mb-4 nm-anim-card-2">
        <div class="nm-card-header">
          <h5>⚠️ Points d'attention</h5>
        </div>
        <div class="nm-card-body">
          <?php foreach ($result['avertissements'] as $warn): ?>
            <div style="display:flex;gap:.6rem;align-items:flex-start;margin-bottom:.5rem;padding:.7rem 1rem;background:#fef9c3;border-radius:8px;border:1px solid #fde68a;">
              <span style="color:#f59e0b;flex-shrink:0;">⚠</span>
              <span style="font-size:.88rem;color:#92400e;"><?php echo htmlspecialchars($warn); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Return to normal -->
      <?php if (!empty($result['retour_normal'])): ?>
      <div class="nm-card mb-4 nm-anim-card-3" style="background:linear-gradient(135deg,#1e293b,#334155);border:none;">
        <div class="nm-card-body d-flex gap-3 align-items-start">
          <div style="font-size:2rem;flex-shrink:0;line-height:1;">🏁</div>
          <div>
            <h5 style="color:#fff;font-weight:700;margin-bottom:.5rem;">
              Reprendre le plan normal après <?php echo htmlspecialchars($data['duree']); ?>
            </h5>
            <p style="color:rgba(255,255,255,.75);margin:0;line-height:1.75;font-size:.92rem;">
              <?php echo htmlspecialchars($result['retour_normal']); ?>
            </p>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Bottom CTA -->
      <div class="text-center nm-anim-card-3">
        <a href="ai_mutation.php" class="nm-btn nm-btn-brand nm-btn-lg me-2">
          🔄 Muter un autre plan
        </a>
        <a href="mes_plans.php" class="nm-btn nm-btn-ghost nm-btn-lg">
          ← Retour aux plans
        </a>
      </div>

    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
