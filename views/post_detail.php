<?php
session_start();
require_once '../controllers/PostController.php';
require_once '../controllers/CommentController.php';

$postController    = new PostController();
$commentController = new CommentController();

require_once '../models/PostReaction.php';
$reactionModel = new PostReaction();

// ─── Get post ────────────────────────────────────────────────────────────────
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $postController->getById($id);

if (!$post || $post['statut'] !== 'publie') {
    $_SESSION['error_message'] = 'Post introuvable ou non publié.';
    header('Location: post_list.php');
    exit;
}

$reactionCounts = $reactionModel->getCounts($id);
$userReaction = isset($_SESSION['user_id']) ? $reactionModel->getUserReaction($id, $_SESSION['user_id']) : null;

require_once '../models/Bookmark.php';
$bookmarkModel = new Bookmark();
$isBookmarked = isset($_SESSION['user_id']) ? $bookmarkModel->isBookmarked($id, $_SESSION['user_id']) : false;

require_once '../models/User.php';

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

$commentsTree = [];
$replies = [];
foreach ($comments as $c) {
    if ($c['parent_id']) {
        $replies[$c['parent_id']][] = $c;
    } else {
        $commentsTree[] = $c;
    }
}

require_once '../models/CommentReaction.php';
$commentReactionModel = new CommentReaction();

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
            <?php unset($_SESSION['success_message']); ?>
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
                            <?php 
                            if (isset($post['auteur_points'])) {
                                $badge = User::getBadge($post['auteur_points']);
                                echo "<span class='badge badge-{$badge['couleur']}' title='{$badge['nom']}'>{$badge['icone']} {$badge['nom']}</span>";
                            }
                            ?>
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
                        <?php if (!empty($post['image_url'])): ?>
                            <div class="mb-4 text-center">
                                <img src="<?= htmlspecialchars($post['image_url']) ?>" alt="Image du post" class="img-fluid rounded shadow-sm" style="max-height: 500px; width: auto; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <?= nl2br(htmlspecialchars($post['contenu'])) ?>
                    </div>

                    <!-- ── Reactions ── -->
                    <div class="reactions-container d-flex align-items-center mb-4 gap-2">
                        <button class="btn <?= $userReaction === 'like' ? 'btn-success' : 'btn-outline-success' ?> react-btn" data-type="like" data-post="<?= $id ?>">
                            <i class="fas fa-thumbs-up"></i> <span class="like-count"><?= $reactionCounts['likes'] ?></span>
                        </button>
                        <button class="btn <?= $userReaction === 'dislike' ? 'btn-danger' : 'btn-outline-danger' ?> react-btn" data-type="dislike" data-post="<?= $id ?>">
                            <i class="fas fa-thumbs-down"></i> <span class="dislike-count"><?= $reactionCounts['dislikes'] ?></span>
                        </button>
                        
                        <!-- Bookmark -->
                        <button class="btn <?= $isBookmarked ? 'btn-warning' : 'btn-outline-warning' ?> ms-auto" id="bookmarkBtn" data-post="<?= $id ?>">
                            <i class="<?= $isBookmarked ? 'fas' : 'far' ?> fa-bookmark"></i> <span id="bookmarkText"><?= $isBookmarked ? 'Sauvegardé' : 'Sauvegarder' ?></span>
                        </button>
                        
                        <!-- Report Post -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button class="btn btn-outline-secondary report-btn" data-type="post" data-id="<?= $id ?>" title="Signaler ce post">
                                <i class="fas fa-flag"></i>
                            </button>
                        <?php endif; ?>
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
                        <?php
                        $renderComment = function($c, $isReply = false) use ($id, $commentReactionModel, $comment_errors, $comment_values) {
                            $userCReaction = isset($_SESSION['user_id']) ? $commentReactionModel->getUserReaction($c['id_comment'], $_SESSION['user_id']) : null;
                            $marginLeft = $isReply ? 'margin-left: 40px; border-left: 3px solid #ccc;' : 'border-left: 3px solid #f28123;';
                        ?>
                            <div class="comment-item mb-3 p-3" style="<?= $marginLeft ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="orange-text">
                                            <i class="fas fa-user-circle"></i>
                                            <?= htmlspecialchars($c['auteur_nom'] ?? 'Anonyme') ?>
                                        </strong>
                                        <?php 
                                        if (isset($c['auteur_points'])) {
                                            $cBadge = User::getBadge($c['auteur_points']);
                                            echo "<span class='badge badge-{$cBadge['couleur']} ms-1' title='{$cBadge['nom']}' style='font-size:0.7em;'>{$cBadge['icone']} {$cBadge['nom']}</span>";
                                        }
                                        ?>
                                        <small class="text-muted ml-2">
                                            <?= date('d/m/Y à H:i', strtotime($c['date_creation'])) ?>
                                        </small>
                                    </div>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']): ?>
                                        <button class="btn btn-xs btn-outline-danger delete-comment-btn"
                                            data-comment="<?= $c['id_comment'] ?>"
                                            data-post="<?= $id ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <p class="mt-2 mb-2" id="comment-text-<?= $c['id_comment'] ?>" style="white-space:pre-wrap;"><?= htmlspecialchars($c['contenu']) ?></p>
                                
                                <!-- Comment Reactions & Reply Button -->
                                <div class="d-flex align-items-center gap-3">
                                    <div class="comment-reactions d-flex gap-2">
                                        <button class="btn btn-sm <?= $userCReaction === 'like' ? 'btn-success' : 'btn-outline-success' ?> react-comment-btn" data-type="like" data-comment="<?= $c['id_comment'] ?>" style="padding: 0.1rem 0.4rem; font-size: 12px;">
                                            <i class="fas fa-thumbs-up"></i> <span class="like-c-count"><?= (int)$c['nb_likes'] ?></span>
                                        </button>
                                        <button class="btn btn-sm <?= $userCReaction === 'dislike' ? 'btn-danger' : 'btn-outline-danger' ?> react-comment-btn" data-type="dislike" data-comment="<?= $c['id_comment'] ?>" style="padding: 0.1rem 0.4rem; font-size: 12px;">
                                            <i class="fas fa-thumbs-down"></i> <span class="dislike-c-count"><?= (int)$c['nb_dislikes'] ?></span>
                                        </button>
                                    </div>
                                    <?php if (!$isReply && isset($_SESSION['user_id'])): ?>
                                        <button class="btn btn-sm btn-link text-muted reply-btn" data-comment="<?= $c['id_comment'] ?>" style="font-size: 13px; text-decoration: none;">
                                            <i class="fas fa-reply"></i> Répondre
                                        </button>
                                    <?php endif; ?>
                                    
                                    <!-- Translate Button -->
                                    <button class="btn btn-sm btn-link text-muted translate-btn" data-comment="<?= $c['id_comment'] ?>" style="font-size: 13px; text-decoration: none;">
                                        <i class="fas fa-language"></i> Traduire
                                    </button>

                                    <!-- Report Comment -->
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $c['user_id']): ?>
                                        <button class="btn btn-sm text-muted report-btn ms-auto" data-type="comment" data-id="<?= $c['id_comment'] ?>" title="Signaler" style="padding: 0; font-size: 13px;">
                                            <i class="fas fa-flag"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Reply Form (Hidden by default) -->
                                <?php if (!$isReply && isset($_SESSION['user_id'])): ?>
                                    <div class="reply-form-container mt-2" id="reply-form-<?= $c['id_comment'] ?>" style="display: none;">
                                        <form method="POST" action="post_detail.php?id=<?= $id ?>" novalidate>
                                            <input type="hidden" name="parent_id" value="<?= $c['id_comment'] ?>">
                                            <div class="d-flex gap-2">
                                                <input type="text" name="contenu" id="reply-input-<?= $c['id_comment'] ?>" class="form-control form-control-sm" placeholder="Votre réponse..." required minlength="2" maxlength="1000">
                                                <button type="button" class="btn btn-sm btn-outline-secondary ai-suggest-btn" data-comment="<?= $c['id_comment'] ?>" data-text="<?= htmlspecialchars($c['contenu'], ENT_QUOTES) ?>" title="Suggestions IA">
                                                    <i class="fas fa-magic"></i>
                                                </button>
                                                <button type="submit" name="add_comment" class="btn btn-sm btn-primary">Envoyer</button>
                                            </div>
                                            <!-- AI Suggestions Box -->
                                            <div id="ai-suggestions-<?= $c['id_comment'] ?>" class="ai-suggestions-box" style="display:none;">
                                                <div class="ai-suggestions-header">
                                                    <i class="fas fa-magic"></i> Suggestions IA
                                                    <span class="ai-suggestions-close" data-comment="<?= $c['id_comment'] ?>">&times;</span>
                                                </div>
                                                <div class="ai-suggestions-loading" id="ai-suggest-loading-<?= $c['id_comment'] ?>" style="display:none;">
                                                    <i class="fas fa-spinner fa-spin"></i> Génération en cours...
                                                </div>
                                                <div class="ai-suggestions-list" id="ai-suggest-list-<?= $c['id_comment'] ?>"></div>
                                            </div>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php
                        };

                        foreach ($commentsTree as $parentComment): 
                            $renderComment($parentComment, false);
                            if (!empty($replies[$parentComment['id_comment']])):
                                echo '<div class="replies-container">';
                                foreach ($replies[$parentComment['id_comment']] as $reply):
                                    $renderComment($reply, true);
                                endforeach;
                                echo '</div>';
                            endif;
                        endforeach; 
                        ?>
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

                                <!-- AI Comment Improver -->
                                <div class="mt-2">
                                    <button type="button" id="improveCommentBtn" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-magic"></i> Améliorer avec l'IA
                                    </button>
                                </div>
                                <div id="improveBox" style="display:none;" class="improve-box mt-2">
                                    <div class="improve-box-header">
                                        <i class="fas fa-magic"></i> Version améliorée par l'IA
                                        <span id="improveClose" style="cursor:pointer; float:right; font-size:18px; line-height:1;">&times;</span>
                                    </div>
                                    <div id="improveLoading" style="display:none; padding:10px 12px; color:#888; font-size:13px;">
                                        <i class="fas fa-spinner fa-spin"></i> Amélioration en cours...
                                    </div>
                                    <div id="improveResult" style="display:none;">
                                        <div id="improveText" class="improve-preview"></div>
                                        <div class="improve-actions">
                                            <button type="button" id="improveAccept" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Utiliser ce texte
                                            </button>
                                            <button type="button" id="improveReject" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-times"></i> Garder l'original
                                            </button>
                                        </div>
                                    </div>
                                    <div id="improveError" style="display:none; padding:8px 12px; color:#dc3545; font-size:13px;"></div>
                                </div>
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

<!-- Delete Comment Modal -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supprimer le commentaire</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Voulez-vous vraiment supprimer ce commentaire ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <a href="#" id="confirmDeleteCommentBtn" class="btn btn-danger">Supprimer</a>
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

/* AI Smart Reply Suggestions */
.ai-suggestions-box {
    margin-top: 8px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #fafafa;
    overflow: hidden;
    font-size: 13px;
}
.ai-suggestions-header {
    background: linear-gradient(90deg, #f28123, #f5a623);
    color: #fff;
    padding: 6px 12px;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.ai-suggestions-close {
    cursor: pointer;
    font-size: 18px;
    line-height: 1;
    opacity: 0.85;
}
.ai-suggestions-close:hover { opacity: 1; }
.ai-suggestions-loading {
    padding: 10px 12px;
    color: #888;
}
.ai-suggestions-list {
    padding: 6px 8px;
}
.ai-suggestion-chip {
    display: inline-block;
    background: #fff;
    border: 1px solid #f28123;
    color: #333;
    border-radius: 20px;
    padding: 4px 12px;
    margin: 4px;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    font-size: 12.5px;
}
.ai-suggestion-chip:hover {
    background: #f28123;
    color: #fff;
}

/* AI Comment Improver */
.improve-box {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #fafafa;
    overflow: hidden;
    font-size: 13px;
}
.improve-box-header {
    background: linear-gradient(90deg, #f28123, #f5a623);
    color: #fff;
    padding: 6px 12px;
    font-weight: 600;
}
.improve-preview {
    padding: 10px 12px;
    background: #fff;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    line-height: 1.6;
    white-space: pre-wrap;
    color: #333;
}
.improve-actions {
    padding: 8px 12px;
    display: flex;
    gap: 8px;
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

// ── AI Comment Improver ───────────────────────────────────────────────────────
(function() {
    const improveBtn    = document.getElementById('improveCommentBtn');
    const improveBox    = document.getElementById('improveBox');
    const improveClose  = document.getElementById('improveClose');
    const improveLoad   = document.getElementById('improveLoading');
    const improveResult = document.getElementById('improveResult');
    const improveText   = document.getElementById('improveText');
    const improveError  = document.getElementById('improveError');
    const improveAccept = document.getElementById('improveAccept');
    const improveReject = document.getElementById('improveReject');
    const ta            = document.getElementById('contenu');

    if (!improveBtn || !ta) return;

    function resetBox() {
        improveLoad.style.display   = 'none';
        improveResult.style.display = 'none';
        improveError.style.display  = 'none';
    }

    improveBtn.addEventListener('click', function() {
        const draft = ta.value.trim();

        if (draft.length < 2) {
            ta.focus();
            ta.classList.add('is-warning');
            return;
        }

        // Toggle off if already open
        if (improveBox.style.display === 'block') {
            improveBox.style.display = 'none';
            return;
        }

        improveBox.style.display = 'block';
        resetBox();
        improveLoad.style.display = 'block';

        fetch('improve_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ draft: draft })
        })
        .then(r => r.json())
        .then(data => {
            improveLoad.style.display = 'none';
            if (!data.success) {
                improveError.textContent    = data.error || 'Erreur IA.';
                improveError.style.display  = 'block';
                return;
            }
            improveText.textContent         = data.improved;
            improveResult.style.display     = 'block';
        })
        .catch(() => {
            improveLoad.style.display  = 'none';
            improveError.textContent   = 'Service IA indisponible.';
            improveError.style.display = 'block';
        });
    });

    // Accept: replace textarea content with improved version
    improveAccept.addEventListener('click', function() {
        ta.value = improveText.textContent;
        ta.dispatchEvent(new Event('input')); // update char counter
        improveBox.style.display = 'none';
    });

    // Reject: just close the box
    improveReject.addEventListener('click', function() {
        improveBox.style.display = 'none';
    });

    // Close button
    improveClose.addEventListener('click', function() {
        improveBox.style.display = 'none';
    });
})();

// Delete comment buttons
document.querySelectorAll('.delete-comment-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const commentId = this.getAttribute('data-comment');
        const postId    = this.getAttribute('data-post');
        document.getElementById('confirmDeleteCommentBtn').href =
            'post_detail.php?id=' + postId + '&delete_comment=' + commentId;
        $('#deleteCommentModal').modal('show');
    });
});

// Client-side comment validation
document.getElementById('commentForm')?.addEventListener('submit', function(e) {
    const val = textarea.value.trim();
    if (val.length < 2) {
        e.preventDefault();
        textarea.classList.add('is-warning');
        textarea.classList.remove('is-invalid');
    }
});

// Reactions AJAX
document.querySelectorAll('.react-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const type = this.getAttribute('data-type');
        const postId = this.getAttribute('data-post');

        fetch('post_reaction.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId, type: type })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update counts
                document.querySelector('.like-count').textContent = data.counts.likes;
                document.querySelector('.dislike-count').textContent = data.counts.dislikes;

                // Reset buttons
                const btnLike = document.querySelector('.react-btn[data-type="like"]');
                const btnDislike = document.querySelector('.react-btn[data-type="dislike"]');

                btnLike.className = 'btn react-btn ' + (data.user_reaction === 'like' ? 'btn-success' : 'btn-outline-success');
                btnDislike.className = 'btn react-btn ' + (data.user_reaction === 'dislike' ? 'btn-danger' : 'btn-outline-danger');
            } else {
                if (data.error === 'Vous devez être connecté.') {
                    window.location.href = 'auth.php';
                } else {
                    alert(data.error);
                }
            }
        })
        .catch(err => console.error('Error:', err));
    });
});

// Toggle Reply Form
document.querySelectorAll('.reply-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const commentId = this.getAttribute('data-comment');
        const form = document.getElementById('reply-form-' + commentId);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });
});

// ── AI Smart Reply Suggestions ────────────────────────────────────────────────
document.querySelectorAll('.ai-suggest-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const commentId   = this.getAttribute('data-comment');
        const commentText = this.getAttribute('data-text');
        const box         = document.getElementById('ai-suggestions-' + commentId);
        const loading     = document.getElementById('ai-suggest-loading-' + commentId);
        const list        = document.getElementById('ai-suggest-list-' + commentId);

        // Toggle: if already open, close it
        if (box.style.display === 'block') {
            box.style.display = 'none';
            return;
        }

        box.style.display  = 'block';
        loading.style.display = 'block';
        list.innerHTML     = '';

        fetch('reply_suggestions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ comment: commentText })
        })
        .then(r => r.json())
        .then(data => {
            loading.style.display = 'none';
            if (!data.success) {
                list.innerHTML = '<span class="text-muted" style="padding:8px;display:block;">' + (data.error || 'Erreur IA.') + '</span>';
                return;
            }
            data.suggestions.forEach(suggestion => {
                const chip = document.createElement('span');
                chip.className   = 'ai-suggestion-chip';
                chip.textContent = suggestion;
                chip.addEventListener('click', function() {
                    const input = document.getElementById('reply-input-' + commentId);
                    input.value = suggestion;
                    input.focus();
                    box.style.display = 'none';
                });
                list.appendChild(chip);
            });
        })
        .catch(() => {
            loading.style.display = 'none';
            list.innerHTML = '<span class="text-muted" style="padding:8px;display:block;">Service IA indisponible.</span>';
        });
    });
});

// Close AI suggestions box
document.querySelectorAll('.ai-suggestions-close').forEach(btn => {
    btn.addEventListener('click', function() {
        const commentId = this.getAttribute('data-comment');
        document.getElementById('ai-suggestions-' + commentId).style.display = 'none';
    });
});

// Comments Reactions AJAX
document.querySelectorAll('.react-comment-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const type = this.getAttribute('data-type');
        const commentId = this.getAttribute('data-comment');
        const parentDiv = this.closest('.comment-reactions');

        fetch('comment_reaction.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ comment_id: commentId, type: type })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                parentDiv.querySelector('.like-c-count').textContent = data.counts.likes;
                parentDiv.querySelector('.dislike-c-count').textContent = data.counts.dislikes;

                const btnLike = parentDiv.querySelector('.react-comment-btn[data-type="like"]');
                const btnDislike = parentDiv.querySelector('.react-comment-btn[data-type="dislike"]');

                btnLike.className = 'btn btn-sm react-comment-btn ' + (data.user_reaction === 'like' ? 'btn-success' : 'btn-outline-success');
                btnDislike.className = 'btn btn-sm react-comment-btn ' + (data.user_reaction === 'dislike' ? 'btn-danger' : 'btn-outline-danger');
            } else {
                if (data.error === 'Vous devez être connecté.') {
                    window.location.href = 'auth.php';
                } else {
                    alert(data.error);
                }
            }
        })
        .catch(err => console.error('Error:', err));
    });
});

// Bookmark AJAX
document.getElementById('bookmarkBtn')?.addEventListener('click', function() {
    const postId = this.getAttribute('data-post');
    const btn = this;
    const icon = btn.querySelector('i');
    const text = btn.querySelector('span');

    fetch('bookmark_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ post_id: postId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (data.status === 'added') {
                btn.classList.remove('btn-outline-warning');
                btn.classList.add('btn-warning');
                icon.classList.remove('far');
                icon.classList.add('fas');
                text.textContent = 'Sauvegardé';
            } else {
                btn.classList.remove('btn-warning');
                btn.classList.add('btn-outline-warning');
                icon.classList.remove('fas');
                icon.classList.add('far');
                text.textContent = 'Sauvegarder';
            }
        } else {
            if (data.error === 'Vous devez être connecté pour sauvegarder un article.') {
                window.location.href = 'auth.php';
            } else {
                alert(data.error);
            }
        }
    })
    .catch(err => console.error('Error:', err));
});

// Report Modal Handling
document.querySelectorAll('.report-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const type = this.getAttribute('data-type');
        const id = this.getAttribute('data-id');
        
        document.getElementById('reportItemType').value = type;
        document.getElementById('reportItemId').value = id;
        document.getElementById('reportMotif').value = '';
        document.getElementById('reportError').classList.add('d-none');
        document.getElementById('reportSuccess').classList.add('d-none');
        
        // Show modal (assuming bootstrap 4 is included)
        $('#reportModal').modal('show');
    });
});

document.getElementById('submitReportBtn')?.addEventListener('click', function() {
    const type = document.getElementById('reportItemType').value;
    const id = document.getElementById('reportItemId').value;
    const motif = document.getElementById('reportMotif').value;
    const errorDiv = document.getElementById('reportError');
    const successDiv = document.getElementById('reportSuccess');

    if (!motif) {
        errorDiv.textContent = 'Veuillez sélectionner un motif.';
        errorDiv.classList.remove('d-none');
        return;
    }

    fetch('report_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ item_type: type, item_id: id, motif: motif })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            errorDiv.classList.add('d-none');
            successDiv.textContent = data.message;
            successDiv.classList.remove('d-none');
            setTimeout(() => {
                $('#reportModal').modal('hide');
            }, 2000);
        } else {
            successDiv.classList.add('d-none');
            errorDiv.textContent = data.error;
            errorDiv.classList.remove('d-none');
        }
    })
    .catch(err => console.error('Error:', err));
});

// IA Translation using Google Translate unofficial API
document.querySelectorAll('.translate-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const commentId = this.getAttribute('data-comment');
        const textElement = document.getElementById('comment-text-' + commentId);
        
        // Prevent multiple translations
        if (this.classList.contains('translated')) return;
        
        const originalText = textElement.textContent;
        const targetLang = navigator.language.split('-')[0] || 'fr'; // auto detect user browser lang
        
        // Show loading state
        const originalIcon = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';
        
        const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=${targetLang}&dt=t&q=${encodeURIComponent(originalText)}`;
        
        fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data && data[0]) {
                let translatedText = '';
                data[0].forEach(t => { translatedText += t[0]; });
                
                textElement.innerHTML = `<span class="translated-text">${translatedText}</span><br><small class="text-muted"><i class="fas fa-magic"></i> Traduit automatiquement (Original: ${data[2]})</small>`;
                this.classList.add('translated');
                this.innerHTML = '<i class="fas fa-check text-success"></i> Traduit';
            } else {
                this.innerHTML = originalIcon;
                alert("Erreur lors de la traduction.");
            }
        })
        .catch(err => {
            console.error(err);
            this.innerHTML = originalIcon;
            alert("Erreur lors de la traduction.");
        });
    });
});
</script>

<?php
function getCatBadge($cat) {
    $map = ['Nutrition'=>'success','Recettes'=>'warning','Sport'=>'primary','Santé'=>'info','Autre'=>'secondary'];
    return $map[$cat] ?? 'secondary';
}
?>
<!-- ── Report Modal ── -->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reportModalLabel"><i class="fas fa-flag text-danger"></i> Signaler un contenu</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="reportForm">
            <input type="hidden" id="reportItemType" name="item_type" value="">
            <input type="hidden" id="reportItemId" name="item_id" value="">
            
            <div class="form-group">
                <label>Pourquoi signalez-vous ce contenu ?</label>
                <select class="form-control" id="reportMotif" required>
                    <option value="">Sélectionnez un motif...</option>
                    <option value="Spam ou publicité">Spam ou publicité</option>
                    <option value="Contenu offensant ou haineux">Contenu offensant ou haineux</option>
                    <option value="Désinformation médicale/nutritionnelle">Désinformation médicale/nutritionnelle</option>
                    <option value="Harcèlement">Harcèlement</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>
            <div class="alert alert-danger d-none" id="reportError"></div>
            <div class="alert alert-success d-none" id="reportSuccess"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-danger" id="submitReportBtn">Envoyer le signalement</button>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
