<?php
session_start();
if (!isset($_SESSION['user_id']))    { header('Location: auth.php');        exit; }
if (empty($_SESSION['ai_report']))   { header('Location: objectif_list.php'); exit; }

require_once 'ai_render.php';

$report   = $_SESSION['ai_report'];
$content  = $report['content'];
$nom      = htmlspecialchars($report['user_nom']);
$total    = (int)$report['total_obj'];
$date     = htmlspecialchars($report['generated_at']);
?>
<?php include 'header.php'; ?>

<style>
<?php include 'ai_styles.css.php'; ?>

/* Report-specific extras */
.report-hero {
  background: linear-gradient(135deg,#6366f1,#4f46e5);
  border-radius: 20px;
  padding: 2.5rem 2rem;
  color: #fff;
  text-align: center;
  margin-bottom: 2rem;
}
.report-hero h2 { font-size:1.8rem; font-weight:700; margin-bottom:.4rem; }
.report-hero p  { opacity:.85; font-size:.95rem; margin:0; }
.report-badge {
  display:inline-block; background:rgba(255,255,255,.2);
  border-radius:30px; padding:.3rem 1rem; font-size:.85rem;
  margin:.5rem .25rem 0;
}
.report-card {
  border-radius: 20px;
  box-shadow: 0 6px 32px rgba(0,0,0,.08);
  border: none;
  overflow: hidden;
  margin-bottom: 1.5rem;
}
.report-card-header {
  background: linear-gradient(135deg,#6366f1,#818cf8);
  padding: 1.2rem 1.8rem;
  display: flex;
  align-items: center;
  gap: .8rem;
}
.report-card-header h4 { color:#fff; margin:0; font-size:1.1rem; font-weight:700; }
.report-card-body { padding: 2rem 2.2rem; background:#fff; }
.export-btn {
  display:inline-flex; align-items:center; gap:.5rem;
  padding:.6rem 1.5rem; border-radius:30px; font-weight:600;
  background:#10b981; color:#fff; border:none; cursor:pointer;
  text-decoration:none; transition:background .2s;
}
.export-btn:hover { background:#059669; color:#fff; text-decoration:none; }
@media print {
  nav, footer, .no-print { display:none !important; }
  .report-hero { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
}
</style>

<div class="product-section mt-150 mb-150">
  <div class="container">

    <!-- Hero -->
    <div class="row mb-2">
      <div class="col-lg-10 offset-lg-1">
        <div class="report-hero">
          <div style="font-size:3rem;margin-bottom:.8rem;">📊</div>
          <h2>Rapport IA Personnalisé</h2>
          <p>Analyse complète de vos objectifs nutritionnels et de bien-être</p>
          <div>
            <span class="report-badge"><i class="fas fa-user me-1"></i><?php echo $nom; ?></span>
            <span class="report-badge"><i class="fas fa-bullseye me-1"></i><?php echo $total; ?> objectif<?php echo $total > 1 ? 's' : ''; ?></span>
            <span class="report-badge"><i class="fas fa-clock me-1"></i><?php echo $date; ?></span>
            <span class="report-badge"><i class="fas fa-robot me-1"></i>qwen3.5:cloud</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Action buttons -->
    <div class="row mb-4 no-print">
      <div class="col-12 text-center d-flex flex-wrap justify-content-center gap-2">
        <a href="objectif_list.php" class="ai-tab" style="--c:#6366f1;--cs:#4f46e5;">
          <i class="fas fa-arrow-left"></i> Retour aux objectifs
        </a>
        <button onclick="exportPDF()" class="export-btn">
          <i class="fas fa-file-pdf"></i> Exporter en PDF
        </button>
        <a href="ai_report.php" class="ai-tab" style="--c:#10b981;--cs:#059669;">
          <i class="fas fa-sync-alt"></i> Régénérer
        </a>
      </div>
    </div>

    <!-- Report content -->
    <div class="row">
      <div class="col-lg-10 offset-lg-1">
        <div class="report-card">
          <div class="report-card-header">
            <span style="font-size:1.5rem;">🤖</span>
            <h4>Analyse complète — Modèle qwen3.5:cloud</h4>
          </div>
          <div class="report-card-body" style="--accent:#6366f1;--accent-light:#ede9fe;">
            <?php echo renderAIPlan($content); ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom actions -->
    <div class="row mt-2 no-print">
      <div class="col-12 text-center">
        <a href="objectif_list.php" class="boxed-btn" style="background:#6366f1;border-color:#6366f1;">
          <i class="fas fa-arrow-left me-2"></i> Retour aux objectifs
        </a>
      </div>
    </div>

  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function exportPDF() {
  const btn = document.querySelector('.export-btn');
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération…';
  btn.disabled = true;

  const bodyContent = document.querySelector('.report-card-body').innerHTML;

  const el = document.createElement('div');
  el.innerHTML = `
    <style>
      * { box-sizing: border-box; margin: 0; padding: 0; }
      body { font-family: Arial, Helvetica, sans-serif; color: #1e293b; }

      .pdf-hero {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border-radius: 16px;
        padding: 28px 24px;
        color: #fff;
        text-align: center;
        margin-bottom: 24px;
      }
      .pdf-hero-emoji { font-size: 2.4rem; margin-bottom: 8px; }
      .pdf-hero h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: 4px; }
      .pdf-hero p  { opacity: .8; font-size: .85rem; margin-bottom: 10px; }
      .pdf-badge {
        display: inline-block;
        background: rgba(255,255,255,.18);
        border-radius: 99px;
        padding: 3px 12px;
        font-size: .75rem;
        margin: 2px;
        color: #fff;
      }

      /* Markdown-rendered content */
      .ai-h2 {
        font-size: 1.05rem;
        font-weight: 700;
        color: #6366f1;
        border-left: 4px solid #6366f1;
        padding-left: 10px;
        margin: 20px 0 8px;
      }
      .ai-h3 {
        font-size: .95rem;
        font-weight: 600;
        color: #374151;
        margin: 14px 0 5px;
      }
      .ai-h4 {
        font-size: .85rem;
        font-weight: 600;
        color: #6b7280;
        margin: 10px 0 4px;
        text-transform: uppercase;
        letter-spacing: .04em;
      }
      .ai-p {
        color: #374151;
        line-height: 1.75;
        margin: 4px 0;
        font-size: .88rem;
      }
      .ai-ul { list-style: none; padding-left: 14px; margin: 4px 0 10px; }
      .ai-ol { padding-left: 18px; margin: 4px 0 10px; }
      .ai-ul li, .ai-ol li {
        color: #374151;
        line-height: 1.7;
        margin-bottom: 3px;
        font-size: .88rem;
        position: relative;
      }
      .ai-ul li::before {
        content: "▸";
        color: #6366f1;
        position: absolute;
        left: -14px;
        font-size: .8rem;
      }
      .ai-hr {
        border: none;
        border-top: 2px solid #ede9fe;
        margin: 14px 0;
      }
      .ai-code {
        background: #ede9fe;
        color: #6366f1;
        padding: 1px 5px;
        border-radius: 4px;
        font-size: .82em;
      }

      .pdf-footer {
        text-align: center;
        margin-top: 32px;
        padding-top: 14px;
        border-top: 1px solid #e2e8f0;
        color: #94a3b8;
        font-size: .75rem;
      }
    </style>

    <div class="pdf-hero">
      <div class="pdf-hero-emoji">📊</div>
      <h1>Rapport IA Personnalisé — NutriMind</h1>
      <p>Analyse complète de vos objectifs nutritionnels et de bien-être</p>
      <span class="pdf-badge">👤 <?php echo $nom; ?></span>
      <span class="pdf-badge">🎯 <?php echo $total; ?> objectif<?php echo $total > 1 ? 's' : ''; ?></span>
      <span class="pdf-badge">🕐 <?php echo $date; ?></span>
      <span class="pdf-badge">🤖 qwen3.5:cloud</span>
    </div>

    <div style="padding: 0 4px;">
      ${bodyContent}
    </div>

    <div class="pdf-footer">
      NutriMind · Rapport généré par Intelligence Artificielle (qwen3.5:cloud) · <?php echo $date; ?>
    </div>
  `;

  const opt = {
    margin:      [8, 10, 8, 10],
    filename:    'NutriMind_Rapport_IA_<?php echo date('Y-m-d'); ?>.pdf',
    image:       { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2, useCORS: true, logging: false, letterRendering: true },
    jsPDF:       { unit: 'mm', format: 'a4', orientation: 'portrait' }
  };

  html2pdf().set(opt).from(el).save().then(() => {
    btn.innerHTML = originalText;
    btn.disabled = false;
  });
}
</script>

<?php include 'footer.php'; ?>
