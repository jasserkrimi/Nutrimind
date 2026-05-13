<?php ob_start(); ?>
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="fs-3 mb-1">Créer un Nouvel Exercice</h1>
      </div>
      <a href="index.php?c=exercice&action=index&activite_id=<?= $activite_id ?>" class="btn btn-secondary">
        <i class="ti ti-arrow-left me-2"></i>Retour
      </a>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="index.php?c=exercice&action=create&activite_id=<?= $activite_id ?>" method="POST">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de l'exercice <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nom" id="nom" value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label for="activite_id" class="form-label">Activité Sportive <span class="text-danger">*</span></label>
                        <select name="activite_id" id="activite_id" class="form-select">
                            <option value="">Sélectionnez une activité</option>
                            <?php foreach ($activites as $act): ?>
                                <option value="<?= $act['id'] ?>" <?= (isset($_POST['activite_id']) && $_POST['activite_id'] == $act['id']) ? 'selected' : '' ?>><?= htmlspecialchars($act['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="difficulte" class="form-label">Difficulté <span class="text-danger">*</span></label>
                        <select name="difficulte" id="difficulte" class="form-select">
                            <option value="">Sélectionnez...</option>
                            <option value="Débutant" <?= (isset($_POST['difficulte']) && $_POST['difficulte'] == 'Débutant') ? 'selected' : '' ?>>Débutant</option>
                            <option value="Intermédiaire" <?= (isset($_POST['difficulte']) && $_POST['difficulte'] == 'Intermédiaire') ? 'selected' : '' ?>>Intermédiaire</option>
                            <option value="Avancé" <?= (isset($_POST['difficulte']) && $_POST['difficulte'] == 'Avancé') ? 'selected' : '' ?>>Avancé</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description et Consignes <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="5" class="form-control"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="url_video" class="form-label">Lien Vidéo</label>
                        <input type="url" class="form-control" name="url_video" id="url_video" placeholder="https://..." value="<?= isset($_POST['url_video']) ? htmlspecialchars($_POST['url_video']) : '' ?>">
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Enregistrer l'exercice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'views/layout_back.php';
?>
