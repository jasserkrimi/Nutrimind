<?php
session_start();
require_once '../controllers/PostController.php';
require_once '../controllers/CommentController.php';

$postController    = new PostController();
$commentController = new CommentController();

// ─── Get post ────────────────────────────────────────────────────────────────
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $postController->getById($id);

if (!$post || $post['statut'] !== 'publie') {
    $_SESSION['error_message'] = 'Post introuvable ou non publié.';
    header('Location: post_list.php');
    exit;
}

// ─── Handle new comment submission ───────────────────────────────────────────
$comment_errors = [];
$comment_values = ['contenu' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = 'Vous devez être connecté pour commenter.';
        header("Location: post_detail.php?id=$id");
        exit;
    }

    $result = $commentController->create($_POST, $id, $_SESSION['user_id']);

    if ($result['success']) {
        $_SESSION['success_message'] = 'Commentaire ajouté avec succès !';
        header("Location: post_detail.php?id=$id");
        exit;
    } else {
        $comment_errors  = $result['errors'];
        $comment_values  = $_POST;
    }
}

// ─── Handle comment delete ────────────────────────────────────────────────────
if (isset($_GET['delete_comment']) && isset($_SESSION['user_id'])) {
    $cid     = (int)$_GET['delete_comment'];
    $comment = $commentController->getById($cid);
    if ($comment && $comment['user_id'] == $_SESSION['user_id']) {
        $commentController->delete($cid);
        $_SESSION['success_message'] = 'Commentaire supprimé.';
    } else {
        $_SESSION['error_message'] = 'Action non autorisée.';
    }
    header("Location: post_detail.php?id=$id");
    exit;
}

$comments = $commentController->getAllByPost($id, true);
?>
<?php include 'header.php'; ?>

<!-- Page Banner -->
<div class="breadcrumb-section breadcrumb-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p><a href="post_list.php" style="color:#fff;">Posts</a> &rsaquo; Détail</p>
                    <h1><?= htmlspecialchars($post['titre']) ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="product-section mt-80 mb-80">
    <div class="container">

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- ── Post Content ── -->
            <div class="col-lg-8">
                <div class="post-detail-card p-4 mb-4">
                    <!-- Meta -->
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="badge badge-<?= getCatBadge($post['categorie']) ?>">
                            <?= htmlspecialchars($post['categorie']) ?>
                        </span>
                        <span class="text-muted small">
                            <i class="fas fa-user"></i>
                            <?= htmlspecialchars($post['auteur_nom'] ?? 'Anonyme') ?>
                        </span>
                        <span class="text-muted small">
                            <i class="fas fa-calendar"></i>
                            <?= date('d/m/Y à H:i', strtotime($post['date_creation'])) ?>
                        </span>
                        <span class="text-muted small">
                            <i class="fas fa-comments"></i>
                            <?= (int)$post['nb_comments'] ?> commentaire(s)
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="post-body mb-4" style="line-height:1.8; font-size:16px;">
                        <?= nl2br(htmlspecialchars($post['contenu'])) ?>
                    </div>

                    <!-- Owner actions -->
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                        <hr>
                        <div class="d-flex gap-2">
                            <a href="post_edit.php?id=<?= $post['id_post'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="#" class="btn btn-danger btn-sm" id="deletePostBtn">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3">
                        <a href="post_list.php" class="read-more-btn">
                            <i class="fas fa-arrow-left"></i> Retour aux posts
                        </a>
                    </div>
                </div>

                <!-- ── Comments Section ── -->
                <div class="post-detail-card p-4">
                    <h4 class="mb-4">
                        <i class="fas fa-comments orange-text"></i>
                        Commentaires (<?= count($comments) ?>)
                    </h4>

                    <?php if (empty($comments)): ?>
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-comment-slash fa-2x mb-2 d-block" style="opacity:.3;"></i>
                            Aucun commentaire pour le moment. Soyez le premier !
                        </p>
                    <?php else: ?>
                        <?php foreach ($comments as $c): ?>
                            <div class="comment-item mb-3 p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="orange-text">
                                            <i class="fas fa-user-circle"></i>
                                            <?= htmlspecialchars($c['auteur_nom'] ?? 'Anonyme') ?>
                                        </strong>
                                        <small class="text-muted ml-2">
                                            <?= date('d/m/Y à H:i', strtotime($c['date_creation'])) ?>
                                        </small>
                                    </div>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']): ?>
                                        <a href="post_detail.php?id=<?= $id ?>&delete_comment=<?= $c['id_comment'] ?>"
                                           class="btn btn-xs btn-outline-danger"
                                           onclick="return confirm('Supprimer ce commentaire ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <p class="mt-2 mb-0" style="white-space:pre-wrap;">
                                    <?= htmlspecialchars($c['contenu']) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- ── Add Comment Form ── -->
                    <hr>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <h5 class="mb-3">Laisser un commentaire</h5>
                        <form method="POST" action="post_detail.php?id=<?= $id ?>" id="commentForm" novalidate>
                            <div class="form-group">
                                <label for="contenu">Votre commentaire <span class="text-danger">*</span></label>
                                <textarea name="contenu" id="contenu" rows="4"
                                    class="form-control <?= isset($comment_errors['contenu']) ? 'is-invalid' : '' ?>"
                                    placeholder="Partagez votre avis... (2 à 1000 caractères)"
                                    maxlength="1000" required><?= htmlspecialchars($comment_values['contenu'] ?? '') ?></textarea>
                                <?php if (isset($comment_errors['contenu'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($comment_errors['contenu']) ?></div>
                                <?php endif; ?>
                                <small class="form-text text-muted">
                                    <span id="charCount">0</span>/1000 caractères
                                </small>
                            </div>
                            <?php if (isset($comment_errors['general'])): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($comment_errors['general']) ?></div>
                            <?php endif; ?>
                            <button type="submit" name="add_comment" class="boxed-btn mt-2">
                                <i class="fas fa-paper-plane"></i> Publier le commentaire
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <a href="auth.php">Connectez-vous</a> pour laisser un commentaire.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── Sidebar ── -->
            <div class="col-lg-4">
                <div class="post-detail-card p-4 mb-4">
                    <h5><i class="fas fa-info-circle orange-text"></i> À propos de ce post</h5>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><strong>Auteur :</strong> <?= htmlspecialchars($post['auteur_nom'] ?? 'Anonyme') ?></li>
                        <li class="mb-2"><strong>Catégorie :</strong>
                            <span class="badge badge-<?= getCatBadge($post['categorie']) ?>">
                                <?= htmlspecialchars($post['categorie']) ?>
                            </span>
                        </li>
                        <li class="mb-2"><strong>Publié le :</strong> <?= date('d/m/Y', strtotime($post['date_creation'])) ?></li>
                        <li class="mb-2"><strong>Modifié le :</strong> <?= date('d/m/Y', strtotime($post['date_mise_a_jour'])) ?></li>
                        <li class="mb-2"><strong>Commentaires :</strong> <?= (int)$post['nb_comments'] ?></li>
                    </ul>
                </div>
                <div class="post-detail-card p-4">
                    <h5><i class="fas fa-link orange-text"></i> Navigation</h5>
                    <ul class="list-unstyled mt-3">
                        <li><a href="post_list.php" class="read-more-btn"><i class="fas fa-list"></i> Tous les posts</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="mt-2"><a href="post_create.php" class="read-more-btn"><i class="fas fa-plus"></i> Créer un post</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Post Modal -->
<div class="modal fade" id="deletePostModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Supprimer ce post supprimera aussi tous ses commentaires. Continuer ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <a href="post_list.php?delete=<?= $post['id_post'] ?>" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<style>
.post-detail-card { background:#fff; border:1px solid #eee; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,.06); }
.comment-item { background:#f9f9f9; border-left:3px solid #f28123; border-radius:4px; }
.btn-xs { padding:2px 8px; font-size:11px; }
.gap-2 { gap:.5rem; }
.breadcrumb-bg { background:url('assets/img/breadcrumb-bg.jpg') center/cover no-repeat; }
.breadcrumb-text p { color:#fff; font-size:14px; text-transform:uppercase; letter-spacing:3px; margin-bottom:10px; }
.breadcrumb-text h1 { color:#fff; font-size:36px; font-weight:700; }

/* Yellow warning style */
.is-warning {
    border-color: #ffc107 !important;
    background-color: #fff3cd !important;
}
.is-warning:focus {
    border-color: #ffc107 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
}
</style>

<script>
// Character counter for comment
const textarea = document.getElementById('contenu');
const counter  = document.getElementById('charCount');
if (textarea && counter) {
    const update = () => {
        counter.textContent = textarea.value.length;
        counter.style.color = textarea.value.length > 900 ? 'red' : '';
    };
    textarea.addEventListener('input', update);
    update();

    // Real-time validation
    textarea.addEventListener('input', function() {
        const val = this.value.trim();
        if (val.length < 2) {
            this.classList.add('is-warning');
            this.classList.remove('is-invalid');
        } else {
            this.classList.remove('is-warning', 'is-invalid');
        }
    });
}

// Delete post button
const delBtn = document.getElementById('deletePostBtn');
if (delBtn) {
    delBtn.addEventListener('click', e => { e.preventDefault(); $('#deletePostModal').modal('show'); });
}

// Client-side comment validation
document.getElementById('commentForm')?.addEventListener('submit', function(e) {
    const val = textarea.value.trim();
    if (val.length < 2) {
        e.preventDefault();
        textarea.classList.add('is-warning');
        textarea.classList.remove('is-invalid');
    }
});
</script>

<?php
function getCatBadge($cat) {
    $map = ['Nutrition'=>'success','Recettes'=>'warning','Sport'=>'primary','Santé'=>'info','Autre'=>'secondary'];
    return $map[$cat] ?? 'secondary';
}
?>
<?php include 'footer.php'; ?>
