<?php
session_start();
require_once '../controllers/PostController.php';

if (!isset($_SESSION['user_id'])) { header('Location: auth.php'); exit; }

$postController = new PostController();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $postController->getById($id);

if (!$post) { $_SESSION['error_message'] = 'Post introuvable.'; header('Location: post_list.php'); exit; }
// Only owner or admin can edit
if ($post['user_id'] != $_SESSION['user_id'] && ($_SESSION['user_role'] ?? '') !== 'admin') {
    $_SESSION['error_message'] = 'Action non autorisée.'; header('Location: post_list.php'); exit;
}

$categories = \Post::getCategories();
$statuts    = \Post::getStatuts();
$errors     = [];
$values     = $post; // prefill with existing

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $postController->update($id, $_POST, $_FILES['image_file'] ?? null);
    if ($result['success']) {
        $_SESSION['success_message'] = 'Post mis à jour avec succès !';
        header("Location: post_detail.php?id=$id"); exit;
    } else {
        $errors = $result['errors'];
        $values = array_merge($values, $_POST);
    }
}
?>
<?php include 'header.php'; ?>
<div class="breadcrumb-section breadcrumb-bg">
    <div class="container"><div class="row"><div class="col-lg-8 offset-lg-2 text-center">
        <div class="breadcrumb-text">
            <p><a href="post_list.php" style="color:#fff;">Posts</a> &rsaquo; Modifier</p>
            <h1>Modifier le <span class="orange-text">Post</span></h1>
        </div>
    </div></div></div>
</div>

<div class="product-section mt-80 mb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>

                <div class="post-form-card p-4">
                    <h4 class="mb-4"><i class="fas fa-edit orange-text"></i> Modifier le post</h4>
                    <form method="POST" action="post_edit.php?id=<?= $id ?>" id="postEditForm" enctype="multipart/form-data" novalidate>

                        <!-- Titre -->
                        <div class="form-group">
                            <label for="titre">Titre <span class="text-danger">*</span></label>
                            <input type="text" name="titre" id="titre"
                                   class="form-control <?= isset($errors['titre']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($values['titre']) ?>"
                                   maxlength="200" required>
                            <?php if (isset($errors['titre'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['titre']) ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted"><span id="titreCount">0</span>/200 caractères</small>
                            <div class="mt-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary ai-improve-btn" data-target="titre">
                                    <i class="fas fa-magic"></i> Améliorer avec l'IA
                                </button>
                            </div>
                            <div class="improve-box mt-2" id="improve-box-titre" style="display:none;">
                                <div class="improve-box-header">
                                    <i class="fas fa-magic"></i> Version améliorée par l'IA
                                    <span class="improve-close" data-target="titre" style="cursor:pointer;float:right;font-size:18px;line-height:1;">&times;</span>
                                </div>
                                <div class="improve-loading" id="improve-loading-titre" style="display:none;padding:10px 12px;color:#888;font-size:13px;">
                                    <i class="fas fa-spinner fa-spin"></i> Amélioration en cours...
                                </div>
                                <div class="improve-result" id="improve-result-titre" style="display:none;">
                                    <div class="improve-preview" id="improve-text-titre"></div>
                                    <div class="improve-actions">
                                        <button type="button" class="btn btn-sm btn-success improve-accept" data-target="titre"><i class="fas fa-check"></i> Utiliser ce texte</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary improve-reject" data-target="titre"><i class="fas fa-times"></i> Garder l'original</button>
                                    </div>
                                </div>
                                <div class="improve-error" id="improve-error-titre" style="display:none;padding:8px 12px;color:#dc3545;font-size:13px;"></div>
                            </div>
                        </div>

                        <!-- Catégorie -->
                        <div class="form-group">
                            <label for="categorie">Catégorie <span class="text-danger">*</span></label>
                            <select name="categorie" id="categorie"
                                    class="form-control <?= isset($errors['categorie']) ? 'is-invalid' : '' ?>" required>
                                <option value="">-- Choisir --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>"
                                        <?= $values['categorie'] === $cat ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['categorie'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['categorie']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Contenu -->
                        <div class="form-group">
                            <label for="contenu">Contenu <span class="text-danger">*</span></label>
                            <textarea name="contenu" id="contenu" rows="8"
                                      class="form-control <?= isset($errors['contenu']) ? 'is-invalid' : '' ?>"
                                      maxlength="5000" required><?= htmlspecialchars($values['contenu']) ?></textarea>
                            <?php if (isset($errors['contenu'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['contenu']) ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted"><span id="contenuCount">0</span>/5000 caractères</small>
                            <div class="mt-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary ai-improve-btn" data-target="contenu">
                                    <i class="fas fa-magic"></i> Améliorer avec l'IA
                                </button>
                            </div>
                            <div class="improve-box mt-2" id="improve-box-contenu" style="display:none;">
                                <div class="improve-box-header">
                                    <i class="fas fa-magic"></i> Version améliorée par l'IA
                                    <span class="improve-close" data-target="contenu" style="cursor:pointer;float:right;font-size:18px;line-height:1;">&times;</span>
                                </div>
                                <div class="improve-loading" id="improve-loading-contenu" style="display:none;padding:10px 12px;color:#888;font-size:13px;">
                                    <i class="fas fa-spinner fa-spin"></i> Amélioration en cours...
                                </div>
                                <div class="improve-result" id="improve-result-contenu" style="display:none;">
                                    <div class="improve-preview" id="improve-text-contenu"></div>
                                    <div class="improve-actions">
                                        <button type="button" class="btn btn-sm btn-success improve-accept" data-target="contenu"><i class="fas fa-check"></i> Utiliser ce texte</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary improve-reject" data-target="contenu"><i class="fas fa-times"></i> Garder l'original</button>
                                    </div>
                                </div>
                                <div class="improve-error" id="improve-error-contenu" style="display:none;padding:8px 12px;color:#dc3545;font-size:13px;"></div>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="form-group">
                            <label for="image_file">Image <small class="text-muted">(optionnel)</small></label>
                            <?php if (!empty($values['image_url'])): ?>
                                <div class="mb-2">
                                    <img src="<?= htmlspecialchars($values['image_url']) ?>" alt="Image actuelle" style="max-width: 200px; border-radius: 8px;">
                                    <small class="d-block text-muted mt-1">Image actuelle (laissez vide pour la conserver)</small>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image_file" id="image_file"
                                   class="form-control <?= isset($errors['image_url']) ? 'is-invalid' : '' ?>"
                                   accept="image/jpeg, image/png, image/gif">
                            <?php if (isset($errors['image_url'])): ?>
                                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['image_url']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Statut -->
                        <div class="form-group">
                            <label for="statut">Statut <span class="text-danger">*</span></label>
                            <select name="statut" id="statut"
                                    class="form-control <?= isset($errors['statut']) ? 'is-invalid' : '' ?>">
                                <?php foreach ($statuts as $s): ?>
                                    <option value="<?= $s ?>" <?= $values['statut'] === $s ? 'selected' : '' ?>>
                                        <?= ucfirst($s) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['statut'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['statut']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="boxed-btn"><i class="fas fa-save"></i> Enregistrer</button>
                            <a href="post_detail.php?id=<?= $id ?>" class="bordered-btn"><i class="fas fa-times"></i> Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.post-form-card{background:#fff;border:1px solid #eee;border-radius:10px;box-shadow:0 2px 12px rgba(0,0,0,.06)}
.gap-2{gap:.5rem}
.breadcrumb-bg{background:url('assets/img/breadcrumb-bg.jpg') center/cover no-repeat}
.breadcrumb-text p{color:#fff;font-size:14px;text-transform:uppercase;letter-spacing:3px;margin-bottom:10px}
.breadcrumb-text h1{color:#fff;font-size:42px;font-weight:700}

/* AI Post Improver */
.improve-box{border:1px solid #e0e0e0;border-radius:8px;background:#fafafa;overflow:hidden;font-size:13px;}
.improve-box-header{background:linear-gradient(90deg,#f28123,#f5a623);color:#fff;padding:6px 12px;font-weight:600;}
.improve-preview{padding:10px 12px;background:#fff;border-bottom:1px solid #eee;font-size:14px;line-height:1.6;white-space:pre-wrap;color:#333;max-height:200px;overflow-y:auto;}
.improve-actions{padding:8px 12px;display:flex;gap:8px;}
</style>
<script>
function setupCounter(fId,cId){
    const f=document.getElementById(fId),c=document.getElementById(cId);
    if(!f||!c)return;
    const u=()=>{c.textContent=f.value.length;c.style.color=f.value.length>f.getAttribute('maxlength')*.9?'orange':'';};
    f.addEventListener('input',u);u();
}
setupCounter('titre','titreCount');
setupCounter('contenu','contenuCount');

// ── AI Post Improver ──────────────────────────────────────────────────────────
document.querySelectorAll('.ai-improve-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const target  = this.getAttribute('data-target');
        const field   = document.getElementById(target);
        const box     = document.getElementById('improve-box-'     + target);
        const loading = document.getElementById('improve-loading-' + target);
        const result  = document.getElementById('improve-result-'  + target);
        const text    = document.getElementById('improve-text-'    + target);
        const error   = document.getElementById('improve-error-'   + target);
        const draft   = field.value.trim();

        if (draft.length < 2) { field.focus(); return; }

        if (box.style.display === 'block') { box.style.display = 'none'; return; }

        box.style.display     = 'block';
        loading.style.display = 'block';
        result.style.display  = 'none';
        error.style.display   = 'none';

        fetch('improve_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ draft: draft })
        })
        .then(r => r.json())
        .then(data => {
            loading.style.display = 'none';
            if (!data.success) {
                error.textContent   = data.error || 'Erreur IA.';
                error.style.display = 'block';
                return;
            }
            text.textContent     = data.improved;
            result.style.display = 'block';
        })
        .catch(() => {
            loading.style.display = 'none';
            error.textContent     = 'Service IA indisponible.';
            error.style.display   = 'block';
        });
    });
});

document.querySelectorAll('.improve-accept').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const target = this.getAttribute('data-target');
        const field  = document.getElementById(target);
        field.value  = document.getElementById('improve-text-' + target).textContent;
        field.dispatchEvent(new Event('input'));
        document.getElementById('improve-box-' + target).style.display = 'none';
    });
});

document.querySelectorAll('.improve-reject, .improve-close').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('improve-box-' + this.getAttribute('data-target')).style.display = 'none';
    });
});

document.getElementById('postEditForm').addEventListener('submit',function(e){
    let v=true;
    const t=document.getElementById('titre');
    if(t.value.trim().length<3){t.classList.add('is-invalid');v=false;}else t.classList.remove('is-invalid');
    const ct=document.getElementById('contenu');
    if(ct.value.trim().length<10){ct.classList.add('is-invalid');v=false;}else ct.classList.remove('is-invalid');
    const cat=document.getElementById('categorie');
    if(!cat.value){cat.classList.add('is-invalid');v=false;}else cat.classList.remove('is-invalid');
    if(!v){e.preventDefault();window.scrollTo({top:0,behavior:'smooth'});}
});
</script>
<?php include 'footer.php'; ?>
