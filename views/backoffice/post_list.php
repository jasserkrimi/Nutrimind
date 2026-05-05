<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php'); exit;
}
require_once '../../controllers/PostController.php';
require_once '../../controllers/CommentController.php';

$postController    = new PostController();
$commentController = new CommentController();

// ─── Filters ─────────────────────────────────────────────────────────────────
$search    = isset($_GET['search'])    ? trim($_GET['search'])    : '';
$categorie = isset($_GET['categorie']) ? trim($_GET['categorie']) : '';
$statut    = isset($_GET['statut'])    ? trim($_GET['statut'])    : '';

$posts      = $postController->getAll($search, $categorie, $statut);
$categories = \Post::getCategories();
$statuts    = \Post::getStatuts();

// ─── Delete handler ───────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    if ($postController->delete((int)$_GET['delete'])) {
        $_SESSION['success_message'] = 'Post supprimé avec succès.';
    } else {
        $_SESSION['error_message'] = 'Erreur lors de la suppression.';
    }
    header('Location: post_list.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Posts - NutriMind Admin</title>
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
  <div><h4 class="mb-0">Gestion des Posts</h4></div>
</nav>

<!-- SIDEBAR -->
<aside id="sidebar" class="sidebar">
  <div class="logo-area">
    <a href="index.php" class="d-inline-flex"><img src="assets/images/logooo.png" alt="Nutrimind" style="max-height:50px;width:auto;"></a>
  </div>
  <ul class="nav flex-column">
    <li class="px-4 py-2"><small class="nav-text">Main</small></li>
    <li><a class="nav-link" href="index.php"><i class="ti ti-home"></i><span class="nav-text">Dashboard</span></a></li>
    <li><a class="nav-link" href="users.php"><i class="ti ti-users"></i><span class="nav-text">Users</span></a></li>
    <li class="px-4 py-2"><small class="nav-text">Planning</small></li>
    <li><a class="nav-link" href="planning_list.php"><i class="ti ti-calendar-event"></i><span class="nav-text">Manage Plans</span></a></li>
    <li><a class="nav-link" href="planning_create.php"><i class="ti ti-plus"></i><span class="nav-text">Create Plan</span></a></li>
    <li class="px-4 py-2"><small class="nav-text">Posts</small></li>
    <li><a class="nav-link active" href="post_list.php"><i class="ti ti-news"></i><span class="nav-text">Manage Posts</span></a></li>
    <li><a class="nav-link" href="post_create.php"><i class="ti ti-plus"></i><span class="nav-text">Create Post</span></a></li>
    <li><a class="nav-link" href="comment_list.php"><i class="ti ti-message-2"></i><span class="nav-text">Comments</span></a></li>
    <li class="px-4 pt-4 pb-2"><small class="nav-text">Account</small></li>
    <li><a class="nav-link" href="#" onclick="logout(); return false;"><i class="ti ti-logout"></i><span class="nav-text">Déconnexion</span></a></li>
  </ul>
</aside>

<!-- MAIN CONTENT -->
<main id="content" class="content py-10">
  <div class="container-fluid">

    <!-- Header row -->
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
          <h1 class="fs-3 mb-1">Gestion des Posts</h1>
          <p class="text-muted mb-0">Administrez tous les posts de la communauté</p>
        </div>
        <a href="post_create.php" class="btn btn-primary">
          <i class="ti ti-plus me-1"></i> Créer un Post
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
      $statColors = ['publie'=>'success','brouillon'=>'warning','archive'=>'secondary'];
      $statIcons  = ['publie'=>'ti-eye','brouillon'=>'ti-pencil','archive'=>'ti-archive'];
      foreach ($statuts as $s):
          $cnt = $postController->countByStatut($s);
      ?>
      <div class="col-lg-4">
        <div class="card p-3 bg-<?= $statColors[$s] ?? 'light' ?>-subtle border border-<?= $statColors[$s] ?? 'secondary' ?>-subtle">
          <div class="d-flex gap-3 align-items-center">
            <div class="icon-shape icon-md bg-<?= $statColors[$s] ?? 'secondary' ?> text-white rounded-2">
              <i class="ti <?= $statIcons[$s] ?? 'ti-file' ?> fs-4"></i>
            </div>
            <div>
              <h6 class="mb-0 text-muted">Posts <?= ucfirst($s) ?></h6>
              <h3 class="fw-bold mb-0"><?= $cnt ?></h3>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="post_list.php" class="row g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Recherche</label>
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Titre ou contenu..." value="<?= htmlspecialchars($search) ?>" maxlength="100">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-semibold">Catégorie</label>
            <select name="categorie" class="form-select form-select-sm">
              <option value="">Toutes</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $categorie === $cat ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-semibold">Statut</label>
            <select name="statut" class="form-select form-select-sm">
              <option value="">Tous</option>
              <?php foreach ($statuts as $s): ?>
                <option value="<?= $s ?>" <?= $statut === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm w-100">
              <i class="ti ti-search"></i> Filtrer
            </button>
            <a href="post_list.php" class="btn btn-outline-secondary btn-sm">
              <i class="ti ti-x"></i>
            </a>
          </div>
        </form>
      </div>
    </div>

    <!-- Table -->
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Commentaires</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($posts)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Aucun post trouvé</td></tr>
              <?php else: ?>
                <?php foreach ($posts as $p): ?>
                  <tr>
                    <td><small class="text-muted">#<?= $p['id_post'] ?></small></td>
                    <td>
                      <span class="fw-semibold"><?= htmlspecialchars(mb_strimwidth($p['titre'], 0, 50, '…')) ?></span>
                    </td>
                    <td><?= htmlspecialchars($p['auteur_nom'] ?? 'Anonyme') ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($p['categorie']) ?></span></td>
                    <td>
                      <span class="badge bg-<?= ['publie'=>'success','brouillon'=>'warning','archive'=>'secondary'][$p['statut']] ?? 'light' ?>">
                        <?= htmlspecialchars($p['statut']) ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-info-subtle text-info border border-info">
                        <?= (int)$p['nb_comments'] ?>
                      </span>
                    </td>
                    <td><small><?= date('d/m/Y', strtotime($p['date_creation'])) ?></small></td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="post_edit.php?id=<?= $p['id_post'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                          <i class="ti ti-edit"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-danger delete-post" data-id="<?= $p['id_post'] ?>" title="Supprimer">
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
        <?= count($posts) ?> post(s) trouvé(s)
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
      <div class="modal-body">Supprimer ce post supprimera aussi tous ses commentaires. Continuer ?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <a href="#" id="confirmDelete" class="btn btn-danger">Supprimer</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.delete-post').forEach(btn => {
  btn.addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('confirmDelete').href = 'post_list.php?delete=' + btn.dataset.id;
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
