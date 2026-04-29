<?php ob_start(); ?>
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="fs-3 mb-1">Modifier l'Exercice</h1>
      </div>
      <a href="index.php?c=exercice&action=index&activite_id=<?= $exercice['activite_id'] ?>" class="btn btn-secondary">
        <i class="ti ti-arrow-left me-2"></i>Retour
      </a>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="index.php?c=exercice&action=edit&id=<?= $exercice['id'] ?>" method="POST">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de l'exercice <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nom" id="nom" value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : htmlspecialchars($exercice['nom']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="difficulte" class="form-label">Difficulté <span class="text-danger">*</span></label>
                        <select name="difficulte" id="difficulte" class="form-select">
                            <?php $current_diff = isset($_POST['difficulte']) ? $_POST['difficulte'] : $exercice['difficulte']; ?>
                            <option value="">Sélectionnez...</option>
                            <option value="Débutant" <?= ($current_diff == 'Débutant') ? 'selected' : '' ?>>Débutant</option>
                            <option value="Intermédiaire" <?= ($current_diff == 'Intermédiaire') ? 'selected' : '' ?>>Intermédiaire</option>
                            <option value="Avancé" <?= ($current_diff == 'Avancé') ? 'selected' : '' ?>>Avancé</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description et Consignes <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="5" class="form-control"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : htmlspecialchars($exercice['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-warning">Mettre à jour l'exercice</button>
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
