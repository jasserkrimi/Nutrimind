<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php'); exit;
}
require_once '../../controllers/CommentController.php';

$commentController = new CommentController();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$comment = $commentController->getById($id);
if (!$comment) { $_SESSION['error_message'] = 'Commentaire introuvable.'; header('Location: comment_list.php'); exit; }

$errors = [];
$values = $comment;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $commentController->update($id, $_POST);
    if ($result['success']) {
        $_SESSION['success_message'] = 'Commentaire mis à jour avec succès.';
        header('Location: comment_list.php'); exit;
    } else {
        $errors = $result['errors'];
        $values = array_merge($values, $_POST);
    }
}

$statuts = ['approuve', 'en_attente', 'rejete'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier Commentaire - NutriMind Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="assets/images/logooo.png">
  <script type="module" crossorigin src="assets/js/main.js"></script>
  <link rel="stylesheet" crossorigin href="assets/css/main.css">
</head>
<body>
<div id="overlay" class="overlay"></div>
<nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
  <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm">
    <i class="ti ti-layout-sidebar-left-expand"></i></button>
  <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
    <i class="ti ti-layout-sidebar-left-expand"></i></button>
  <div><h4 class="mb-0">Modifier Commentaire #<?= $id ?></h4></div>
</nav>
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
    <li class="px-4 py-2"><small class="nav-text">Posts</small></li>
    <li><a class="nav-link" href="post_list.php"><i class="ti ti-news"></i><span class="nav-text">Manage Posts</span></a></li>
    <li><a class="nav-link" href="post_create.php"><i class="ti ti-plus"></i><span class="nav-text">Create Post</span></a></li>
    <li><a class="nav-link active" href="comment_list.php"><i class="ti ti-message-2"></i><span class="nav-text">Comments</span></a></li>
    <li class="px-4 pt-4 pb-2"><small class="nav-text">Account</small></li>
    <li><a class="nav-link" href="#" onclick="logout(); return false;"><i class="ti ti-logout"></i><span class="nav-text">Déconnexion</span></a></li>
  </ul>
</aside>
<main id="content" class="content py-10">
  <div class="container-fluid">
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
          <h1 class="fs-3 mb-1">Modifier le Commentaire</h1>
          <p class="text-muted mb-0">Éditez le contenu ou le statut de ce commentaire</p>
        </div>
        <a href="comment_list.php" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left me-1"></i> Retour
        </a>
      </div>
    </div>

    <?php if (isset($errors['general'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <!-- Info banner -->
    <div class="alert alert-info mb-4">
      <div class="row">
        <div class="col-md-4"><strong>Post :</strong> <?= htmlspecialchars($comment['post_titre'] ?? 'N/A') ?></div>
        <div class="col-md-4"><strong>Auteur :</strong> <?= htmlspecialchars($comment['auteur_nom'] ?? 'Anonyme') ?></div>
        <div class="col-md-4"><strong>Posté le :</strong> <?= date('d/m/Y H:i', strtotime($comment['date_creation'])) ?></div>
      </div>
    </div>

    <!-- Quick moderation buttons -->
    <div class="card mb-4">
      <div class="card-body d-flex gap-2 align-items-center">
        <span class="fw-semibold me-2">Modération rapide :</span>
        <a href="comment_list.php?moderate=<?= $id ?>&statut=approuve"
           class="btn btn-sm btn-success <?= $comment['statut'] === 'approuve' ? 'disabled' : '' ?>">
          <i class="ti ti-check me-1"></i> Approuver
        </a>
        <a href="comment_list.php?moderate=<?= $id ?>&statut=en_attente"
           class="btn btn-sm btn-warning <?= $comment['statut'] === 'en_attente' ? 'disabled' : '' ?>">
          <i class="ti ti-clock me-1"></i> Mettre en attente
        </a>
        <a href="comment_list.php?moderate=<?= $id ?>&statut=rejete"
           class="btn btn-sm btn-danger <?= $comment['statut'] === 'rejete' ? 'disabled' : '' ?>">
          <i class="ti ti-ban me-1"></i> Rejeter
        </a>
      </div>
    </div>

    <!-- Edit form -->
    <div class="card">
      <div class="card-header bg-transparent py-3">
        <h5 class="mb-0"><i class="ti ti-pencil me-2"></i>Modifier le contenu</h5>
      </div>
      <div class="card-body p-4">
        <form method="POST" action="comment_edit.php?id=<?= $id ?>" id="editCommentForm" novalidate>
          <div class="row g-3">

            <!-- Contenu -->
            <div class="col-12">
              <label class="form-label fw-semibold">
                Contenu du commentaire <span class="text-danger">*</span>
              </label>
              <textarea name="contenu" rows="5"
                        class="form-control <?= isset($errors['contenu']) ? 'is-invalid' : '' ?>"
                        placeholder="Contenu du commentaire (2 à 1000 caractères)"
                        maxlength="1000" required><?= htmlspecialchars($values['contenu']) ?></textarea>
              <?php if (isset($errors['contenu'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['contenu']) ?></div>
              <?php endif; ?>
              <div class="form-text"><span id="contenuCount">0</span>/1000 caractères</div>
            </div>

            <!-- Statut -->
            <div class="col-md-4">
              <label class="form-label fw-semibold">Statut</label>
              <select name="statut" class="form-select <?= isset($errors['statut']) ? 'is-invalid' : '' ?>">
                <?php foreach ($statuts as $s): ?>
                  <option value="<?= $s ?>" <?= $values['statut'] === $s ? 'selected' : '' ?>>
                    <?= ucfirst(str_replace('_', ' ', $s)) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errors['statut'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['statut']) ?></div>
              <?php endif; ?>
            </div>

            <!-- Buttons -->
            <div class="col-12 d-flex gap-2 pt-2">
              <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy me-1"></i> Enregistrer
              </button>
              <a href="comment_list.php" class="btn btn-outline-secondary">Annuler</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const ta = document.querySelector('[name="contenu"]');
const counter = document.getElementById('contenuCount');
if (ta && counter) {
  const update = () => {
    counter.textContent = ta.value.length;
    counter.style.color = ta.value.length > 900 ? 'red' : '';
  };
  ta.addEventListener('input', update);
  update();
}
document.getElementById('editCommentForm').addEventListener('submit', function(e) {
  const val = ta.value.trim();
  if (val.length < 2) {
    ta.classList.add('is-invalid');
    e.preventDefault();
  } else {
    ta.classList.remove('is-invalid');
  }
});
function logout(){
  if(confirm('Se déconnecter ?'))
    fetch('../../controllers/UserController.php?action=logout')
      .then(r=>r.json()).then(d=>{ if(d.success) window.location.href='../index.php'; });
}
</script>
</body>
</html>
