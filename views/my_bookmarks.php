<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

require_once '../models/Bookmark.php';
$bookmarkModel = new Bookmark();
$posts = $bookmarkModel->getUserBookmarks($_SESSION['user_id']);

function getCatBadge($cat) {
    switch (strtolower($cat)) {
        case 'nutrition': return 'success';
        case 'sport':     return 'danger';
        case 'santé':     return 'info';
        case 'recettes':  return 'warning';
        default:          return 'secondary';
    }
}
?>
<?php include 'header.php'; ?>

<!-- breadcrumb-section -->
<div class="breadcrumb-section breadcrumb-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p>Votre collection personnelle</p>
                    <h1>Mes Favoris</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="latest-news mt-80 mb-150">
    <div class="container">
        <div class="row mb-3">
            <div class="col-12">
                <a href="post_list.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Retour aux articles</a>
            </div>
        </div>

        <?php if (empty($posts)): ?>
            <div class="row">
                <div class="col-12 text-center py-5">
                    <i class="far fa-bookmark fa-3x text-muted mb-3" style="opacity:0.4;"></i>
                    <h4 class="text-muted">Vous n'avez aucun favori</h4>
                    <p>Sauvegardez des articles pour les retrouver facilement ici.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($posts as $post): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="single-latest-news h-100 post-card">
                            <?php if (!empty($post['image_url'])): ?>
                                <a href="post_detail.php?id=<?= $post['id_post'] ?>">
                                    <div class="latest-news-bg" style="background-image: url('<?= htmlspecialchars($post['image_url']) ?>'); height: 200px; background-size: cover; background-position: center; border-radius: 8px 8px 0 0;"></div>
                                </a>
                            <?php endif; ?>
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
                                        <i class="fas fa-bookmark text-warning"></i> Sauvegardé le 
                                        <?= date('d/m/Y', strtotime($post['bookmarked_at'])) ?>
                                    </span>
                                </p>
                                <p class="excerpt">
                                    <?= htmlspecialchars(mb_strimwidth(strip_tags($post['contenu']), 0, 120, '…')) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="post_detail.php?id=<?= $post['id_post'] ?>" class="read-more-btn">
                                        Lire plus <i class="fas fa-angle-right"></i>
                                    </a>
                                    <span class="text-muted" style="font-size: 14px;">
                                        <i class="fas fa-thumbs-up" style="color: #28a745;"></i> <?= (int)$post['nb_likes'] ?>
                                        &nbsp;&nbsp;
                                        <i class="fas fa-comments" style="color: #f28123;"></i> <?= (int)$post['nb_comments'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
