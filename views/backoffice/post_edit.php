<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php'); exit;
}
require_once '../../controllers/PostController.php';

$postController = new PostController();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $postController->getById($id);
if (!$post) { $_SESSION['error_message'] = 'Post introuvable.'; header('Location: post_list.php'); exit; }

$categories = \Post::getCategories();
$statuts    = \Post::getStatuts();
$errors     = [];
$values     = $post;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $postController->update($id, $_POST);
    if ($result['success']) {
        $_SESSION['success_message'] = 'Post mis à jour avec succès.';
        header('Location: post_list.php'); exit;
    } else {
        $errors = $result['errors'];
        $values = array_merge($values, $_POST);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier Post - NutriMind Admin</title>
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
  <div><h4 class="mb-0">Modifier le Post #<?= $id ?></h4></div>
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
    <li><a class="nav-link active" href="post_list.php"><i class="ti ti-news"></i><span class="nav-text">Manage Posts</span></a></li>
    <li><a class="nav-link" href="post_create.php"><i class="ti ti-plus"></i><span class="nav-text">Create Post</span></a></li>
    <li><a class="nav-link" href="comment_list.php"><i class="ti ti-message-2"></i><span class="nav-text">Comments</span></a></li>
    <li class="px-4 pt-4 pb-2"><small class="nav-text">Account</small></li>
    <li><a class="nav-link" href="#" onclick="logout(); return false;"><i class="ti ti-logout"></i><span class="nav-text">Déconnexion</span></a></li>
  </ul>
</aside>
<main id="content" class="content py-10">
  <div class="container-fluid">
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
          <h1 class="fs-3 mb-1">Modifier le Post</h1>
          <p class="text-muted mb-0">Modifiez les informations du post</p>
        </div>
        <a href="post_list.php" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left me-1"></i> Retour
        </a>
      </div>
    </div>

    <?php if (isset($errors['general'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <!-- Post info banner -->
    <div class="alert alert-info mb-4">
      <strong>Post :</strong> <?= htmlspecialchars($post['titre']) ?>
      &nbsp;|&nbsp; <strong>Auteur :</strong> <?= htmlspecialchars($post['auteur_nom'] ?? 'Anonyme') ?>
      &nbsp;|&nbsp; <strong>Créé le :</strong> <?= date('d/m/Y', strtotime($post['date_creation'])) ?>
    </div>

    <div class="card">
      <div class="card-body p-4">
        <form method="POST" action="post_edit.php?id=<?= $id ?>" id="adminEditForm" novalidate>
          <div class="row g-3">

            <!-- Catégorie + Statut -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
              <select name="categorie" class="form-select <?= isset($errors['categorie']) ? 'is-invalid' : '' ?>" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= htmlspecialchars($cat) ?>" <?= $values['categorie'] === $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errors['categorie'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['categorie']) ?></div>
              <?php endif; ?>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Statut</label>
              <select name="statut" class="form-select">
                <?php foreach ($statuts as $s): ?>
                  <option value="<?= $s ?>" <?= $values['statut'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Titre -->
            <div class="col-12">
              <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
              <input type="text" name="titre"
                     class="form-control <?= isset($errors['titre']) ? 'is-invalid' : '' ?>"
                     value="<?= htmlspecialchars($values['titre']) ?>"
                     maxlength="200" required>
              <?php if (isset($errors['titre'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['titre']) ?></div>
              <?php endif; ?>
              <div class="form-text"><span id="titreCount">0</span>/200 caractères</div>
            </div>

            <!-- Contenu -->
            <div class="col-12">
              <label class="form-label fw-semibold">Contenu <span class="text-danger">*</span></label>
              <textarea name="contenu" rows="10"
                        class="form-control <?= isset($errors['contenu']) ? 'is-invalid' : '' ?>"
                        maxlength="5000" required><?= htmlspecialchars($values['contenu']) ?></textarea>
              <?php if (isset($errors['contenu'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['contenu']) ?></div>
              <?php endif; ?>
              <div class="form-text"><span id="contenuCount">0</span>/5000 caractères</div>
            </div>

            <!-- Image URL -->
            <div class="col-12">
              <label class="form-label fw-semibold">Image URL <small class="text-muted">(optionnel)</small></label>
              <input type="url" name="image_url"
                     class="form-control <?= isset($errors['image_url']) ? 'is-invalid' : '' ?>"
                     value="<?= htmlspecialchars($values['image_url'] ?? '') ?>" maxlength="500">
              <?php if (isset($errors['image_url'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['image_url']) ?></div>
              <?php endif; ?>
            </div>

            <div class="col-12 d-flex gap-2 pt-2">
              <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy me-1"></i> Enregistrer
              </button>
              <a href="post_list.php" class="btn btn-outline-secondary">Annuler</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function setupCounter(name,cId){
  const f=document.querySelector(`[name="${name}"]`),c=document.getElementById(cId);
  if(!f||!c)return;
  const u=()=>{c.textContent=f.value.length;};
  f.addEventListener('input',u);u();
}
setupCounter('titre','titreCount');
setupCounter('contenu','contenuCount');
document.getElementById('adminEditForm').addEventListener('submit',function(e){
  let v=true;
  const t=this.querySelector('[name="titre"]');
  if(t.value.trim().length<3){t.classList.add('is-invalid');v=false;}else t.classList.remove('is-invalid');
  const ct=this.querySelector('[name="contenu"]');
  if(ct.value.trim().length<10){ct.classList.add('is-invalid');v=false;}else ct.classList.remove('is-invalid');
  if(!v){e.preventDefault();window.scrollTo({top:0,behavior:'smooth'});}
});
function logout(){
  if(confirm('Se déconnecter ?'))
    fetch('../../controllers/UserController.php?action=logout')
      .then(r=>r.json()).then(d=>{ if(d.success) window.location.href='../index.php'; });
}
</script>
</body>
</html>
