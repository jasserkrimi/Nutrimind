<?php
session_start();
require_once '../controllers/PostController.php';

$postController = new PostController();

// ─── Handle delete (MUST be before any HTML output) ───────────────────────────
if (isset($_GET['delete'])) {
    $postToDelete = $postController->getById((int)$_GET['delete']);
    if ($postToDelete && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $postToDelete['user_id']) {
        $postController->delete((int)$_GET['delete']);
        $_SESSION['success_message'] = 'Post supprimé avec succès.';
    } else {
        $_SESSION['error_message'] = 'Action non autorisée.';
    }
    header('Location: post_list.php');
    exit;
}


$search    = isset($_GET['search'])    ? trim($_GET['search'])    : '';
$categorie = isset($_GET['categorie']) ? trim($_GET['categorie']) : '';

$posts      = $postController->getAllPublished($search, $categorie);
$categories = \Post::getCategories();
?>
<?php include 'header.php'; ?>

<!-- Page Banner -->
<div class="breadcrumb-section breadcrumb-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p>Communauté &amp; Nutrition</p>
                    <h1>Nos <span class="orange-text">Posts</span></h1>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Posts Section -->
<div class="product-section mt-80 mb-80">
    <div class="container">

        <!-- Filters + Add Post button -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <form method="GET" action="post_list.php" id="filterForm">
                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                        <div class="d-flex flex-wrap gap-2">
                            <!-- Search -->
                            <div class="input-group" style="max-width:280px;">
                                <input type="text" name="search" id="searchInput"
                                       class="form-control"
                                       placeholder="Rechercher un post..."
                                       value="<?= htmlspecialchars($search) ?>"
                                       maxlength="100">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <!-- Category filter -->
                            <select name="categorie" class="form-control" style="max-width:180px;"
                                    onchange="this.form.submit()">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>"
                                        <?= $categorie === $cat ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($search || $categorie): ?>
                                <a href="post_list.php" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-times"></i> Réinitialiser
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="post_create.php" class="boxed-btn">
                                <i class="fas fa-plus"></i> Créer un Post
                            </a>
                        <?php else: ?>
                            <a href="auth.php" class="boxed-btn">
                                <i class="fas fa-sign-in-alt"></i> Connectez-vous pour poster
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <!-- Results count -->
        <?php if ($search || $categorie): ?>
            <div class="row mb-3">
                <div class="col-12">
                    <p class="text-muted">
                        <i class="fas fa-filter"></i>
                        <?= count($posts) ?> résultat(s)
                        <?= $search ? ' pour "<strong>' . htmlspecialchars($search) . '</strong>"' : '' ?>
                        <?= $categorie ? ' dans <strong>' . htmlspecialchars($categorie) . '</strong>' : '' ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Posts Grid -->
        <?php if (empty($posts)): ?>
            <div class="row">
                <div class="col-12 text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3" style="opacity:0.4;"></i>
                    <h4 class="text-muted">Aucun post trouvé</h4>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <p>Soyez le premier à partager un article !</p>
                        <a href="post_create.php" class="boxed-btn mt-2">
                            <i class="fas fa-plus"></i> Créer un Post
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($posts as $post): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="single-latest-news h-100 post-card">
                            <!-- Category badge -->
                            <div class="news-text-box p-4">
                                <span class="badge badge-<?= getCatBadge($post['categorie']) ?> mb-2">
                                    <?= htmlspecialchars($post['categorie']) ?>
                                </span>
                                <h3>
                                    <a href="post_detail.php?id=<?= $post['id_post'] ?>">
                                        <?= htmlspecialchars(mb_strimwidth($post['titre'], 0, 70, '…')) ?>
                                    </a>
                                </h3>
                                <p class="blog-meta">
                                    <span class="author">
                                        <i class="fas fa-user"></i>
                                        <?= htmlspecialchars($post['auteur_nom'] ?? 'Anonyme') ?>
                                    </span>
                                    <span class="date">
                                        <i class="fas fa-calendar"></i>
                                        <?= date('d/m/Y', strtotime($post['date_creation'])) ?>
                                    </span>
                                </p>
                                <p class="excerpt">
                                    <?= htmlspecialchars(mb_strimwidth(strip_tags($post['contenu']), 0, 120, '…')) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="post_detail.php?id=<?= $post['id_post'] ?>" class="read-more-btn">
                                        Lire la suite <i class="fas fa-angle-right"></i>
                                    </a>
                                    <span class="text-muted small">
                                        <i class="fas fa-comments"></i>
                                        <?= (int)$post['nb_comments'] ?> commentaire(s)
                                    </span>
                                </div>

                                <!-- Edit/Delete for owner -->
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                                    <div class="mt-2 d-flex gap-2">
                                        <a href="post_edit.php?id=<?= $post['id_post'] ?>"
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger delete-post"
                                           data-id="<?= $post['id_post'] ?>">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce post ? Cette action supprimera aussi tous les commentaires.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<style>
.post-card { border:1px solid #eee; border-radius:8px; transition:box-shadow .2s; }
.post-card:hover { box-shadow:0 4px 20px rgba(0,0,0,.1); }
.gap-2 { gap:.5rem; }
.breadcrumb-bg { background:url('assets/img/breadcrumb-bg.jpg') center/cover no-repeat; }
.breadcrumb-text p { color:#fff; font-size:14px; text-transform:uppercase; letter-spacing:3px; margin-bottom:10px; }
.breadcrumb-text h1 { color:#fff; font-size:48px; font-weight:700; }
</style>

<script>
document.querySelectorAll('.delete-post').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        const id = btn.getAttribute('data-id');
        document.getElementById('confirmDelete').href = 'post_list.php?delete=' + id;
        $('#deleteModal').modal('show');
    });
});
</script>


<?php
function getCatBadge($cat) {
    $map = ['Nutrition'=>'success','Recettes'=>'warning','Sport'=>'primary','Santé'=>'info','Autre'=>'secondary'];
    return $map[$cat] ?? 'secondary';
}
?>

<?php include 'footer.php'; ?>

