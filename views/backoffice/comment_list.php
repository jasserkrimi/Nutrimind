<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php'); exit;
}
require_once '../../controllers/CommentController.php';

$commentController = new CommentController();

// ─── Filters ─────────────────────────────────────────────────────────────────
$statut_filter = isset($_GET['statut']) ? trim($_GET['statut']) : '';
$comments      = $commentController->getAll($statut_filter);

// ─── Delete handler ───────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    if ($commentController->delete((int)$_GET['delete'])) {
        $_SESSION['success_message'] = 'Commentaire supprimé.';
    } else {
        $_SESSION['error_message'] = 'Erreur lors de la suppression.';
    }
    header('Location: comment_list.php'); exit;
}

// ─── Moderate handler (quick approve/reject) ──────────────────────────────────
if (isset($_GET['moderate']) && isset($_GET['statut'])) {
    $allowed = ['approuve','rejete','en_attente'];
    $newStat = trim($_GET['statut']);
    if (in_array($newStat, $allowed)) {
        $commentController->moderate((int)$_GET['moderate'], $newStat);
        $_SESSION['success_message'] = 'Statut du commentaire mis à jour.';
    }
    header('Location: comment_list.php' . ($statut_filter ? '?statut=' . urlencode($statut_filter) : '')); exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Commentaires - NutriMind Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="assets/images/logooo.png">
  <script type="module" crossorigin src="assets/js/main.js"></script>
  <link rel="stylesheet" crossorigin href="assets/css/main.css">
</head>
<body>
<div id="overlay" class="overlay"></div>

<!-- TOPBAR -->
<nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
  <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm">
    <i class="ti ti-layout-sidebar-left-expand"></i>
  </button>
  <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
    <i class="ti ti-layout-sidebar-left-expand"></i>
  </button>
  <div><h4 class="mb-0">Gestion des Commentaires</h4></div>
</nav>

<!-- SIDEBAR -->
<aside id="sidebar" class="sidebar">
  <div class="logo-area">
    <a href="index.php" class="d-inline-flex">
      <img src="assets/images/logooo.png" alt="Nutrimind" style="max-height:50px;width:auto;">
    </a>
  </div>
  <ul class="nav flex-column">
    <li class="px-4 py-2"><small class="nav-text">Main</small></li>
    <li><a class="nav-link" href="index.php"><i class="ti ti-home"></i><span class="nav-text">Dashboard</span></a></li>
    <li><a class="nav-link" href="users.php"><i class="ti ti-users"></i><span class="nav-text">Users</span></a></li>
    <li class="px-4 py-2"><small class="nav-text">Planning</small></li>
    <li><a class="nav-link" href="planning_list.php"><i class="ti ti-calendar-event"></i><span class="nav-text">Manage Plans</span></a></li>
    <li><a class="nav-link" href="planning_create.php"><i class="ti ti-plus"></i><span class="nav-text">Create Plan</span></a></li>
    <li class="px-4 py-2"><small class="nav-text">Posts</small></li>
    <li><a class="nav-link" href="post_list.php"><i class="ti ti-news"></i><span class="nav-text">Manage Posts</span></a></li>
    <li><a class="nav-link" href="post_create.php"><i class="ti ti-plus"></i><span class="nav-text">Create Post</span></a></li>
    <li><a class="nav-link active" href="comment_list.php"><i class="ti ti-message-2"></i><span class="nav-text">Comments</span></a></li>
    <li class="px-4 pt-4 pb-2"><small class="nav-text">Account</small></li>
    <li><a class="nav-link" href="#" onclick="logout(); return false;"><i class="ti ti-logout"></i><span class="nav-text">Déconnexion</span></a></li>
  </ul>
</aside>

<!-- MAIN CONTENT -->
<main id="content" class="content py-10">
  <div class="container-fluid">

    <!-- Header -->
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
          <h1 class="fs-3 mb-1">Gestion des Commentaires</h1>
          <p class="text-muted mb-0">Modérez les commentaires de la communauté</p>
        </div>
        <a href="post_list.php" class="btn btn-outline-secondary">
          <i class="ti ti-news me-1"></i> Voir les Posts
        </a>
      </div>
    </div>

    <!-- Flash messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Stats cards -->
    <div class="row g-3 mb-4">
      <?php
      $statDef = [
        'approuve'   => ['success','ti-check',    'Approuvés'],
        'en_attente' => ['warning','ti-clock',    'En attente'],
        'rejete'     => ['danger', 'ti-ban',      'Rejetés'],
      ];
      foreach ($statDef as $s => [$color, $icon, $label]):
          $cnt = $commentController->countByStatut($s);
      ?>
      <div class="col-lg-4">
        <div class="card p-3 bg-<?= $color ?>-subtle border border-<?= $color ?>-subtle">
          <div class="d-flex gap-3 align-items-center">
            <div class="icon-shape icon-md bg-<?= $color ?> text-white rounded-2">
              <i class="ti <?= $icon ?> fs-4"></i>
            </div>
            <div>
              <h6 class="mb-0 text-muted"><?= $label ?></h6>
              <h3 class="fw-bold mb-0"><?= $cnt ?></h3>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Filter tabs -->
    <div class="mb-3">
      <div class="btn-group" role="group">
        <a href="comment_list.php" class="btn btn-sm <?= !$statut_filter ? 'btn-dark' : 'btn-outline-secondary' ?>">
          Tous
        </a>
        <a href="comment_list.php?statut=approuve" class="btn btn-sm <?= $statut_filter === 'approuve' ? 'btn-success' : 'btn-outline-success' ?>">
          <i class="ti ti-check"></i> Approuvés
        </a>
        <a href="comment_list.php?statut=en_attente" class="btn btn-sm <?= $statut_filter === 'en_attente' ? 'btn-warning' : 'btn-outline-warning' ?>">
          <i class="ti ti-clock"></i> En attente
        </a>
        <a href="comment_list.php?statut=rejete" class="btn btn-sm <?= $statut_filter === 'rejete' ? 'btn-danger' : 'btn-outline-danger' ?>">
          <i class="ti ti-ban"></i> Rejetés
        </a>
      </div>
    </div>

    <!-- Comments Table -->
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Post</th>
                <th>Auteur</th>
                <th>Commentaire</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($comments)): ?>
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">
                    <i class="ti ti-message-off fs-3 mb-2 d-block opacity-50"></i>
                    Aucun commentaire trouvé
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($comments as $c): ?>
                  <tr class="<?= $c['statut'] === 'en_attente' ? 'table-warning' : '' ?>">
                    <td><small class="text-muted">#<?= $c['id_comment'] ?></small></td>
                    <td>
                      <small class="fw-semibold">
                        <?= htmlspecialchars(mb_strimwidth($c['post_titre'] ?? 'N/A', 0, 40, '…')) ?>
                      </small>
                    </td>
                    <td>
                      <span class="fw-semibold"><?= htmlspecialchars($c['auteur_nom'] ?? 'Anonyme') ?></span>
                      <br><small class="text-muted"><?= htmlspecialchars($c['auteur_email'] ?? '') ?></small>
                    </td>
                    <td>
                      <span title="<?= htmlspecialchars($c['contenu']) ?>">
                        <?= htmlspecialchars(mb_strimwidth($c['contenu'], 0, 60, '…')) ?>
                      </span>
                    </td>
                    <td>
                      <?php
                      $statBadge = ['approuve'=>'success','en_attente'=>'warning','rejete'=>'danger'];
                      $badge = $statBadge[$c['statut']] ?? 'secondary';
                      ?>
                      <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($c['statut']) ?></span>
                    </td>
                    <td><small><?= date('d/m/Y H:i', strtotime($c['date_creation'])) ?></small></td>
                    <td>
                      <div class="d-flex gap-1 flex-wrap">
                        <?php if ($c['statut'] !== 'approuve'): ?>
                          <a href="comment_list.php?moderate=<?= $c['id_comment'] ?>&statut=approuve<?= $statut_filter ? '&filtre='.urlencode($statut_filter) : '' ?>"
                             class="btn btn-xs btn-success" title="Approuver">
                            <i class="ti ti-check"></i>
                          </a>
                        <?php endif; ?>
                        <?php if ($c['statut'] !== 'rejete'): ?>
                          <a href="comment_list.php?moderate=<?= $c['id_comment'] ?>&statut=rejete<?= $statut_filter ? '&filtre='.urlencode($statut_filter) : '' ?>"
                             class="btn btn-xs btn-warning" title="Rejeter">
                            <i class="ti ti-ban"></i>
                          </a>
                        <?php endif; ?>
                        <a href="comment_edit.php?id=<?= $c['id_comment'] ?>"
                           class="btn btn-xs btn-primary" title="Modifier">
                          <i class="ti ti-edit"></i>
                        </a>
                        <a href="#" class="btn btn-xs btn-danger delete-comment"
                           data-id="<?= $c['id_comment'] ?>" title="Supprimer">
                          <i class="ti ti-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer text-muted small">
        <?= count($comments) ?> commentaire(s)
        <?= $statut_filter ? 'avec statut "' . htmlspecialchars($statut_filter) . '"' : 'au total' ?>
      </div>
    </div>
  </div>
</main>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmer la suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Êtes-vous sûr de vouloir supprimer ce commentaire ?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <a href="#" id="confirmDelete" class="btn btn-danger">Supprimer</a>
      </div>
    </div>
  </div>
</div>

<style>
.btn-xs { padding: 3px 8px; font-size: 11px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.delete-comment').forEach(btn => {
  btn.addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('confirmDelete').href = 'comment_list.php?delete=' + btn.dataset.id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
  });
});
function logout(){
  if(confirm('Se déconnecter ?'))
    fetch('../../controllers/UserController.php?action=logout')
      .then(r=>r.json()).then(d=>{ if(d.success) window.location.href='../index.php'; });
}
</script>
</body>
</html>
