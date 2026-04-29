<?php
session_start();
if (!isset($_SESSION['user_id']))  { header('Location: auth.php');        exit; }
if (empty($_SESSION['ai_plans']))  { header('Location: objectif_list.php'); exit; }

require_once 'ai_render.php';

$plans    = $_SESSION['ai_plans'];
$objectif = htmlspecialchars($plans['objectif']);
$nom      = htmlspecialchars($plans['user_nom']);
$date     = htmlspecialchars($plans['generated_at']);
?>
<?php include 'header.php'; ?>

<style>
<?php include 'ai_styles.css.php'; ?>
</style>

<div class="product-section mt-150 mb-150">
  <div class="container">

    <div class="row mb-3">
      <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">
          <h3><span class="orange-text">IA</span> Planning Personnalisé</h3>
          <p class="ai-meta-top">
            <i class="fas fa-user-circle me-1"></i><strong><?php echo $nom; ?></strong>
            &nbsp;·&nbsp;
            <i class="fas fa-bullseye me-1"></i><strong><?php echo $objectif; ?></strong>
            &nbsp;·&nbsp;
            <i class="fas fa-clock me-1"></i><?php echo $date; ?>
          </p>
        </div>
      </div>
    </div> 

    <!-- Nav -->
    <div class="row mb-5">
      <div class="col-12 text-center d-flex flex-wrap justify-content-center gap-2">
        <a href="ai_sport.php"     class="ai-tab" style="--c:#f59e0b;--cs:#d97706;">
          <i class="fas fa-dumbbell"></i> Plan Sportif
        </a>
        <a href="ai_sommeil.php"   class="ai-tab active" style="--c:#6366f1;--cs:#4f46e5;">
          <i class="fas fa-moon"></i> Plan Sommeil
        </a>
        <a href="ai_nutrition.php" class="ai-tab" style="--c:#10b981;--cs:#059669;">
          <i class="fas fa-apple-alt"></i> Plan Nutrition
        </a>
        <a href="objectif_list.php" class="ai-tab-back">
          <i class="fas fa-arrow-left"></i> Retour
        </a>
      </div>
    </div>

    <!-- Content -->
    <div class="row">
      <div class="col-lg-10 offset-lg-1">
        <div class="ai-card" style="--accent:#6366f1;--accent-light:#ede9fe;">
          <div class="ai-card-header" style="background:linear-gradient(135deg,#6366f1,#818cf8);">
            <span class="ai-card-icon">🌙</span>
            <div>
              <h4 class="ai-card-title">Plan Sommeil</h4>
              <p class="ai-card-sub">Horaires, routines &amp; qualité du sommeil</p>
            </div>
          </div>
          <div class="ai-card-body">
            <?php echo renderAIPlan($plans['sommeil']); ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>
