<?php
session_start();
require_once '../controllers/PostController.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$postController = new PostController();
$errors         = [];
$values         = ['titre' => '', 'contenu' => '', 'categorie' => '', 'image_url' => '', 'statut' => 'publie'];
$categories     = \Post::getCategories();
$statuts        = \Post::getStatuts();

// ─── Handle form submission ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $postController->create($_POST, $_SESSION['user_id'], $_FILES['image_file'] ?? null);

    if ($result['success']) {
        $_SESSION['success_message'] = 'Post créé avec succès !';
        header('Location: post_list.php');
        exit;
    } else {
        $errors = $result['errors'];
        $values = array_merge($values, $_POST);
    }
}
?>
<?php include 'header.php'; ?>

<!-- Page Banner -->
<div class="breadcrumb-section breadcrumb-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p><a href="post_list.php" style="color:#fff;">Posts</a> &rsaquo; Créer</p>
                    <h1>Créer un <span class="orange-text">Post</span></h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="product-section mt-80 mb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>

                <div class="post-form-card p-4">
                    <h4 class="mb-4">
                        <i class="fas fa-edit orange-text"></i> Nouveau post
                    </h4>

                    <form method="POST" action="post_create.php" id="postForm" enctype="multipart/form-data" novalidate>

                        <!-- Titre -->
                        <div class="form-group">
                            <label for="titre">Titre <span class="text-danger">*</span></label>
                            <input type="text" name="titre" id="titre"
                                   class="form-control <?= isset($errors['titre']) ? 'is-invalid' : '' ?>"
                                   placeholder="Titre de votre post (3 à 200 caractères)"
                                   value="<?= htmlspecialchars($values['titre']) ?>"
                                   maxlength="200" required>
                            <?php if (isset($errors['titre'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['titre']) ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">
                                <span id="titreCount">0</span>/200 caractères
                            </small>
                            <div class="mt-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary ai-improve-btn" data-target="titre" data-type="title">
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
                                        <button type="button" class="btn btn-sm btn-success improve-accept" data-target="titre">
                                            <i class="fas fa-check"></i> Utiliser ce texte
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary improve-reject" data-target="titre">
                                            <i class="fas fa-times"></i> Garder l'original
                                        </button>
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
                                <option value="">-- Choisir une catégorie --</option>
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
                                      placeholder="Rédigez votre article... (10 à 5000 caractères)"
                                      maxlength="5000" required><?= htmlspecialchars($values['contenu']) ?></textarea>
                            <?php if (isset($errors['contenu'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['contenu']) ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">
                                <span id="contenuCount">0</span>/5000 caractères
                            </small>
                            <div class="mt-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary ai-improve-btn" data-target="contenu" data-type="content">
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
                                        <button type="button" class="btn btn-sm btn-success improve-accept" data-target="contenu">
                                            <i class="fas fa-check"></i> Utiliser ce texte
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary improve-reject" data-target="contenu">
                                            <i class="fas fa-times"></i> Garder l'original
                                        </button>
                                    </div>
                                </div>
                                <div class="improve-error" id="improve-error-contenu" style="display:none;padding:8px 12px;color:#dc3545;font-size:13px;"></div>
                            </div>
                        </div>

                        <!-- Image Upload (optional) -->
                        <div class="form-group">
                            <label for="image_file">Image <small class="text-muted">(optionnel)</small></label>
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
                                    <option value="<?= $s ?>"
                                        <?= $values['statut'] === $s ? 'selected' : '' ?>>
                                        <?= ucfirst($s) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['statut'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['statut']) ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">
                                Choisissez "publie" pour que votre post soit visible publiquement.
                            </small>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="boxed-btn">
                                <i class="fas fa-save"></i> Publier le Post
                            </button>
                            <a href="post_list.php" class="bordered-btn">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.post-form-card { background:#fff; border:1px solid #eee; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,.06); }
.gap-2 { gap:.5rem; }
.breadcrumb-bg { background:url('assets/img/breadcrumb-bg.jpg') center/cover no-repeat; }
.breadcrumb-text p { color:#fff; font-size:14px; text-transform:uppercase; letter-spacing:3px; margin-bottom:10px; }
.breadcrumb-text h1 { color:#fff; font-size:42px; font-weight:700; }

/* Yellow warning style */
.is-warning {
    border-color: #ffc107 !important;
    background-color: #fff3cd !important;
}
.is-warning:focus {
    border-color: #ffc107 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
}

/* AI Post Improver */
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
    max-height: 200px;
    overflow-y: auto;
}
.improve-actions {
    padding: 8px 12px;
    display: flex;
    gap: 8px;
}
</style>

<script>
// Character counters
function setupCounter(fieldId, counterId) {
    const field   = document.getElementById(fieldId);
    const counter = document.getElementById(counterId);
    if (!field || !counter) return;
    const update = () => {
        counter.textContent = field.value.length;
        counter.style.color = field.value.length > parseInt(field.getAttribute('maxlength')) * 0.9 ? 'orange' : '';
    };
    field.addEventListener('input', update);
    update();
}
setupCounter('titre',   'titreCount');
setupCounter('contenu', 'contenuCount');

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

        // Toggle off
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
        const text   = document.getElementById('improve-text-' + target);
        field.value  = text.textContent;
        field.dispatchEvent(new Event('input'));
        document.getElementById('improve-box-' + target).style.display = 'none';
    });
});

document.querySelectorAll('.improve-reject, .improve-close').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const target = this.getAttribute('data-target');
        document.getElementById('improve-box-' + target).style.display = 'none';
    });
});

// Real-time validation
function validateField(field, minLength, errorMsg) {
    const value = field.value.trim();
    if (value.length < minLength) {
        field.classList.add('is-warning');
        field.classList.remove('is-invalid');
        field.setCustomValidity(errorMsg);
    } else {
        field.classList.remove('is-warning', 'is-invalid');
        field.setCustomValidity('');
    }
}

document.getElementById('titre').addEventListener('input', function() {
    validateField(this, 3, 'Le titre doit contenir au moins 3 caractères.');
});

document.getElementById('contenu').addEventListener('input', function() {
    validateField(this, 10, 'Le contenu doit contenir au moins 10 caractères.');
});

document.getElementById('categorie').addEventListener('change', function() {
    if (!this.value) {
        this.classList.add('is-warning');
        this.classList.remove('is-invalid');
    } else {
        this.classList.remove('is-warning', 'is-invalid');
    }
});

document.getElementById('image_file').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            this.classList.add('is-warning');
            this.classList.remove('is-invalid');
            this.setCustomValidity('Veuillez sélectionner une image valide (JPG, PNG, GIF).');
        } else if (file.size > 5000000) {
            this.classList.add('is-warning');
            this.classList.remove('is-invalid');
            this.setCustomValidity('L\'image ne doit pas dépasser 5 Mo.');
        } else {
            this.classList.remove('is-warning', 'is-invalid');
            this.setCustomValidity('');
        }
    } else {
        this.classList.remove('is-warning', 'is-invalid');
        this.setCustomValidity('');
    }
});

// Client-side validation on submit
document.getElementById('postForm').addEventListener('submit', function(e) {
    let valid = true;

    const titre = document.getElementById('titre');
    if (titre.value.trim().length < 3) {
        titre.classList.add('is-warning');
        titre.classList.remove('is-invalid');
        valid = false;
    }

    const categorie = document.getElementById('categorie');
    if (!categorie.value) {
        categorie.classList.add('is-warning');
        categorie.classList.remove('is-invalid');
        valid = false;
    }

    const contenu = document.getElementById('contenu');
    if (contenu.value.trim().length < 10) {
        contenu.classList.add('is-warning');
        contenu.classList.remove('is-invalid');
        valid = false;
    }

    const imageFile = document.getElementById('image_file');
    if (imageFile.files.length > 0) {
        const file = imageFile.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type) || file.size > 5000000) {
            imageFile.classList.add('is-warning');
            imageFile.classList.remove('is-invalid');
            valid = false;
        }
    }

    if (!valid) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>

<?php include 'footer.php'; ?>
