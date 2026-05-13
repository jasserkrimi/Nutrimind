<?php ob_start(); ?>
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="fs-3 mb-1">Modifier l'Activité Sportive</h1>
        <p>Mettre à jour l'activité existante</p>
      </div>
      <a href="index.php?c=activite&action=index" class="btn btn-secondary">
        <i class="ti ti-arrow-left me-2"></i>Retour
      </a>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="index.php?c=activite&action=edit&id=<?= $activite['id'] ?>" method="POST">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de l'activité <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nom" id="nom" value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : htmlspecialchars($activite['nom']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="categorie" class="form-label">Catégorie du Programme <span class="text-danger">*</span></label>
                        <select name="categorie" id="categorie" class="form-select">
                            <?php $current_cat = isset($_POST['categorie']) ? $_POST['categorie'] : $activite['categorie']; ?>
                            <option value="">Sélectionnez une catégorie...</option>
                            <option value="Être musclé" <?= ($current_cat == 'Être musclé') ? 'selected' : '' ?>>Être musclé (Bodybuilding)</option>
                            <option value="Perte de poids" <?= ($current_cat == 'Perte de poids') ? 'selected' : '' ?>>Perte de poids (HIIT)</option>
                            <option value="Cardio" <?= ($current_cat == 'Cardio') ? 'selected' : '' ?>>Cardio (Course, Vélo)</option>
                            <option value="Force" <?= ($current_cat == 'Force') ? 'selected' : '' ?>>Force (Powerlifting)</option>
                            <option value="Amateur" <?= ($current_cat == 'Amateur') ? 'selected' : '' ?>>Amateur</option>
                            <option value="Autre" <?= ($current_cat == 'Autre') ? 'selected' : '' ?>>Autre...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optionnel)</label>
                        <textarea name="description" id="description" rows="4" class="form-control"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : htmlspecialchars($activite['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-warning">Mettre à jour</button>
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
