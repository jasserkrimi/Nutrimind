<?php
session_start();
require_once '../controllers/ObjectiveController.php';
if (!isset($_SESSION['user_id'])) { header('Location: auth.php'); exit; }

$objectiveController = new ObjectiveController();
$objectives = $objectiveController->getAllForUser($_SESSION['user_id']);

if (isset($_GET['delete'])) {
    $id = htmlspecialchars($_GET['delete']);
    if ($objectiveController->delete($id)) {
        $_SESSION['success_message'] = "Objectif supprimé avec succès!";
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression.";
    }
    header('Location: objectif_list.php'); exit;
}

$total    = count($objectives);
$active   = count(array_filter($objectives, fn($o) => $o['statut'] === 'en_cours'));
$done     = count(array_filter($objectives, fn($o) => $o['statut'] === 'termine'));
$pending  = count(array_filter($objectives, fn($o) => $o['statut'] === 'en_attente'));

$statusCfg = [
    'en_attente' => ['label'=>'En attente', 'class'=>'nm-badge-amber'],
    'en_cours'   => ['label'=>'En cours',   'class'=>'nm-badge-indigo'],
    'termine'    => ['label'=>'Terminé',    'class'=>'nm-badge-green'],
    'annule'     => ['label'=>'Annulé',     'class'=>'nm-badge-red'],
];
$priorityCfg = [
    'faible' => ['label'=>'Faible', 'class'=>'nm-badge-slate'],
    'moyen'  => ['label'=>'Moyen',  'class'=>'nm-badge-amber'],
    'eleve'  => ['label'=>'Élevé',  'class'=>'nm-badge-red'],
];
?>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="assets/css/nutrimind-ui.css">

<!-- ── Floating food particles ── -->
<div class="food-particles" id="foodParticles" aria-hidden="true"></div>
<script>
(function(){
    var e=['🥗','🍎','🥦','🍋','🥕','🍇','🥑','🍓','🌽','🥝','🍊','🫐'];
    var c=document.getElementById('foodParticles');
    for(var i=0;i<20;i++){
        var s=document.createElement('span');
        s.textContent=e[i%e.length];
        s.style.left=(Math.random()*100)+'%';
        s.style.fontSize=(16+Math.random()*20)+'px';
        s.style.animationDuration=(12+Math.random()*20)+'s';
        s.style.animationDelay=(Math.random()*16)+'s';
        c.appendChild(s);
    }
}());
</script>

<div class="nm-page">

  <!-- Hero -->
  <div class="nm-hero nm-anim-hero">
    <div class="nm-hero-inner">
      <div class="nm-hero-eyebrow">🎯 Espace personnel</div>
      <h1>Mes <span>Objectifs</span></h1>
      <p>Suivez et gérez vos objectifs nutritionnels et de remise en forme</p>
      <div class="nm-hero-meta">
        <span class="nm-hero-badge">📋 <?php echo $total; ?> objectif<?php echo $total!==1?'s':''; ?></span>
        <span class="nm-hero-badge">⏳ <?php echo $active; ?> en cours</span>
        <span class="nm-hero-badge">✅ <?php echo $done; ?> terminé<?php echo $done!==1?'s':''; ?></span>
      </div>
    </div>
  </div>

  <!-- Sticky tabs -->
  <div class="nm-tabs-wrap nm-anim-tabs">
    <div class="nm-tabs">
      <a href="objectif_list.php"    class="nm-tab active">🎯 Mes Objectifs</a>
      <a href="mes_plans.php"        class="nm-tab">🥗 Mes Plans</a>
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

      <!-- Alerts -->
      <?php foreach(['success_message'=>'success','error_message'=>'danger','ai_report_error'=>'warning'] as $k=>$t): ?>
        <?php if(isset($_SESSION[$k])): ?>
          <div class="alert alert-<?php echo $t; ?> alert-dismissible fade show mb-3" role="alert" style="border-radius:12px;border:none;box-shadow:var(--shadow-sm);">
            <?php echo htmlspecialchars($_SESSION[$k]); unset($_SESSION[$k]); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>

      <!-- Stat row -->
      <div class="row g-3 mb-4">
        <?php
          $stats = [
            ['val'=>$total,   'lbl'=>'Total',      'icon'=>'fa-layer-group', 'grad'=>'linear-gradient(135deg,#0f172a,#1e293b)'],
            ['val'=>$active,  'lbl'=>'En cours',   'icon'=>'fa-spinner',     'grad'=>'linear-gradient(135deg,#6366f1,#818cf8)'],
            ['val'=>$pending, 'lbl'=>'En attente', 'icon'=>'fa-clock',       'grad'=>'linear-gradient(135deg,#f59e0b,#fbbf24)'],
            ['val'=>$done,    'lbl'=>'Terminés',   'icon'=>'fa-check-circle','grad'=>'linear-gradient(135deg,#10b981,#34d399)'],
          ];
          foreach($stats as $s):
        ?>
        <div class="col-6 col-md-3 nm-anim-stat">
          <div class="nm-stat-card" style="background:<?php echo $s['grad']; ?>;">
            <div class="nm-stat-icon-wrap"><i class="fas <?php echo $s['icon']; ?>"></i></div>
            <div class="nm-stat-val counter" data-target="<?php echo $s['val']; ?>">0</div>
            <div class="nm-stat-lbl"><?php echo $s['lbl']; ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Badges section -->
      <?php
        $highPriority = count(array_filter($objectives, fn($o) => $o['niveau_priorite'] === 'eleve'));

        // Define ALL badges with unlock condition
        $allBadges = [
          [
            'emoji'   => '🎯',
            'title'   => 'Premier pas',
            'desc'    => 'Créer votre premier objectif',
            'goal'    => '1 objectif créé',
            'color'   => '#6366f1',
            'unlocked'=> $total >= 1,
          ],
          [
            'emoji'   => '✅',
            'title'   => 'Objectif atteint',
            'desc'    => 'Terminer au moins un objectif',
            'goal'    => '1 objectif terminé',
            'color'   => '#10b981',
            'unlocked'=> $done >= 1,
          ],
          [
            'emoji'   => '⚡',
            'title'   => 'Haute priorité',
            'desc'    => 'Avoir un objectif de haute priorité',
            'goal'    => '1 objectif priorité élevée',
            'color'   => '#e07b39',
            'unlocked'=> $highPriority >= 1,
          ],
          [
            'emoji'   => '🔥',
            'title'   => 'En feu',
            'desc'    => '3 objectifs en cours simultanément',
            'goal'    => '3 objectifs actifs (vous en avez '.$active.')',
            'color'   => '#ef4444',
            'unlocked'=> $active >= 3,
          ],
          [
            'emoji'   => '⭐',
            'title'   => 'Ambitieux',
            'desc'    => '5 objectifs créés au total',
            'goal'    => '5 objectifs créés (vous en avez '.$total.')',
            'color'   => '#f59e0b',
            'unlocked'=> $total >= 5,
          ],
          [
            'emoji'   => '💎',
            'title'   => 'Persévérant',
            'desc'    => '3 objectifs terminés',
            'goal'    => '3 objectifs terminés (vous en avez '.$done.')',
            'color'   => '#06b6d4',
            'unlocked'=> $done >= 3,
          ],
          [
            'emoji'   => '🏆',
            'title'   => 'Champion',
            'desc'    => 'Tous vos objectifs sont terminés',
            'goal'    => 'Terminer tous vos objectifs actifs',
            'color'   => '#f59e0b',
            'unlocked'=> $total > 0 && $done === $total,
          ],
          [
            'emoji'   => '🚀',
            'title'   => 'Expert',
            'desc'    => '10 objectifs créés au total',
            'goal'    => '10 objectifs créés (vous en avez '.$total.')',
            'color'   => '#8b5cf6',
            'unlocked'=> $total >= 10,
          ],
        ];

        $unlockedCount = count(array_filter($allBadges, fn($b) => $b['unlocked']));
      ?>
      <div class="nm-card nm-anim-card-2 mb-4">
        <div class="nm-card-header">
          <h5>🏆 Badges & Récompenses</h5>
          <span class="nm-badge nm-badge-amber"><?php echo $unlockedCount; ?> / <?php echo count($allBadges); ?> débloqué<?php echo $unlockedCount>1?'s':''; ?></span>
        </div>
        <div class="nm-card-body">
          <!-- Progress bar -->
          <div style="margin-bottom:1.2rem;">
            <div style="display:flex;justify-content:space-between;font-size:.78rem;color:#64748b;margin-bottom:.4rem;">
              <span>Progression</span>
              <span><?php echo round($unlockedCount/count($allBadges)*100); ?>%</span>
            </div>
            <div style="height:8px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
              <div style="height:100%;width:<?php echo round($unlockedCount/count($allBadges)*100); ?>%;background:linear-gradient(90deg,#6366f1,#10b981);border-radius:99px;transition:width 1s ease;"></div>
            </div>
          </div>

          <!-- Badge grid -->
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.75rem;">
            <?php foreach($allBadges as $badge): ?>
              <?php if($badge['unlocked']): ?>
                <!-- UNLOCKED badge -->
                <div style="display:flex;align-items:center;gap:.65rem;background:<?php echo $badge['color']; ?>0f;border:1.5px solid <?php echo $badge['color']; ?>44;border-radius:12px;padding:.75rem 1rem;position:relative;overflow:hidden;">
                  <div style="position:absolute;top:6px;right:8px;font-size:.6rem;font-weight:700;color:<?php echo $badge['color']; ?>;background:<?php echo $badge['color']; ?>18;padding:2px 6px;border-radius:99px;">DÉBLOQUÉ</div>
                  <div style="width:44px;height:44px;border-radius:10px;background:<?php echo $badge['color']; ?>22;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;"><?php echo $badge['emoji']; ?></div>
                  <div>
                    <div style="font-weight:700;font-size:.88rem;color:#1e293b;"><?php echo $badge['title']; ?></div>
                    <div style="font-size:.73rem;color:#64748b;margin-top:.1rem;"><?php echo $badge['desc']; ?></div>
                  </div>
                </div>
              <?php else: ?>
                <!-- LOCKED badge -->
                <div style="display:flex;align-items:center;gap:.65rem;background:#f8fafc;border:1.5px dashed #e2e8f0;border-radius:12px;padding:.75rem 1rem;opacity:.65;position:relative;">
                  <div style="width:44px;height:44px;border-radius:10px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;filter:grayscale(1);">🔒</div>
                  <div>
                    <div style="font-weight:700;font-size:.88rem;color:#94a3b8;"><?php echo $badge['title']; ?></div>
                    <div style="font-size:.73rem;color:#94a3b8;margin-top:.1rem;">🎯 <?php echo $badge['goal']; ?></div>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Table card -->
      <div class="nm-card nm-anim-card">
        <!-- Toolbar -->
        <div class="nm-toolbar">
          <div class="nm-search">
            <span class="nm-search-icon" style="font-style:normal;">🔍</span>
            <input type="text" id="objSearch" placeholder="Rechercher un objectif…">
          </div>
          <a href="objectif_create.php" class="nm-btn nm-btn-brand nm-btn-lg">
            ＋ Nouvel objectif
          </a>
        </div>

        <!-- Table -->
        <div class="nm-table-wrap">
          <table class="nm-table">
            <thead>
              <tr>
                <th>Type d'objectif</th>
                <th>Valeur cible</th>
                <th>Poids initial</th>
                <th>Date limite</th>
                <th>Statut</th>
                <th>Priorité</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody id="objTableBody">
              <?php if(empty($objectives)): ?>
                <tr><td colspan="7">
                  <div class="nm-empty">
                    <span class="nm-empty-emoji">🎯</span>
                    <h5>Aucun objectif pour l'instant</h5>
                    <p>Définissez votre premier objectif pour commencer votre parcours.</p>
                    <a href="objectif_create.php" class="nm-btn nm-btn-brand">＋ Créer un objectif</a>                  </div>
                </td></tr>
              <?php else: ?>
                <?php foreach($objectives as $obj):
                  $s = $statusCfg[$obj['statut']]           ?? ['label'=>$obj['statut'],           'class'=>'nm-badge-slate'];
                  $p = $priorityCfg[$obj['niveau_priorite']] ?? ['label'=>$obj['niveau_priorite'], 'class'=>'nm-badge-slate'];
                ?>
                <tr>
                  <td>
                    <div style="font-weight:600;color:var(--c-text);">
                      <?php echo htmlspecialchars(ucwords(str_replace('_',' ',$obj['type_objectif']))); ?>
                    </div>
                    <?php if(!empty($obj['description'])): ?>
                      <div style="font-size:.78rem;color:var(--c-subtle);margin-top:.15rem;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <?php echo htmlspecialchars($obj['description']); ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td><strong><?php echo htmlspecialchars($obj['valeur_cible']); ?></strong></td>
                  <td><?php echo htmlspecialchars($obj['poids_initial'] ?? '—'); ?></td>
                  <td>
                    <?php if($obj['date_limite']): ?>
                      <span style="font-size:.82rem;color:var(--c-muted);">
                        <i class="fas fa-calendar-alt me-1" style="opacity:.5;display:none;"></i>
                        <?php echo htmlspecialchars($obj['date_limite']); ?>
                      </span>
                    <?php else: ?>
                      <span style="color:var(--c-subtle);">—</span>
                    <?php endif; ?>
                  </td>
                  <td><span class="nm-badge <?php echo $s['class']; ?>"><?php echo $s['label']; ?></span></td>
                  <td><span class="nm-badge <?php echo $p['class']; ?>"><?php echo $p['label']; ?></span></td>
                  <td style="text-align:right;white-space:nowrap;">
                    <a href="objectif_edit.php?id=<?php echo htmlspecialchars($obj['id_objectif']); ?>" class="nm-btn nm-btn-warn nm-btn-sm me-1" title="Modifier">
                      ✏️
                    </a>
                    <a href="#" class="nm-btn nm-btn-danger nm-btn-sm me-1 delete-obj" data-id="<?php echo htmlspecialchars($obj['id_objectif']); ?>" title="Supprimer">
                      🗑️
                    </a>
                    <a href="ai_planning.php?objectif_id=<?php echo htmlspecialchars($obj['id_objectif']); ?>" class="nm-btn nm-btn-indigo nm-btn-sm" title="IA Planning">
                      🤖 IA
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Delete modal -->
<div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content" style="border-radius:16px;border:none;box-shadow:var(--shadow-xl);">
      <div class="modal-body text-center p-4">
        <div style="font-size:2.5rem;margin-bottom:.8rem;">🗑️</div>
        <h5 style="font-weight:700;margin-bottom:.4rem;">Supprimer l'objectif ?</h5>
        <p style="color:var(--c-muted);font-size:.88rem;margin-bottom:1.5rem;">Cette action est irréversible.</p>
        <div class="d-flex gap-2 justify-content-center">
          <button class="nm-btn nm-btn-ghost" data-dismiss="modal">Annuler</button>
          <a href="#" id="delConfirm" class="nm-btn nm-btn-brand">Supprimer</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.querySelectorAll('.delete-obj').forEach(b => {
    b.addEventListener('click', e => {
      e.preventDefault();
      document.getElementById('delConfirm').href = 'objectif_list.php?delete=' + b.dataset.id;
      $('#delModal').modal('show');
    });
  });
  document.getElementById('objSearch').addEventListener('keyup', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#objTableBody tr').forEach(r => {
      r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });
  // Counter animation
  document.querySelectorAll('.counter').forEach(el => {
    const t = parseInt(el.dataset.target, 10);
    let c = 0;
    const timer = setInterval(() => {
      c += Math.max(1, Math.ceil(t / 40));
      if (c >= t) { el.textContent = t; clearInterval(timer); } else el.textContent = c;
    }, 22);
  });
</script>

<?php include 'footer.php'; ?>
