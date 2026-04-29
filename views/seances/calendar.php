<?php ob_start(); ?>
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="fs-3 mb-1">Emploi du Temps Hebdomadaire</h1>
        <p class="text-muted">Gérez vos séances sportives de la semaine</p>
      </div>
      <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addSeanceModal">
        <i class="ti ti-plus me-1"></i>Planifier une Séance
      </button>
    </div>
  </div>
</div>

<div class="row">
    <?php foreach ($calendrier as $jour => $seancesJour): ?>
        <div class="col-12 col-xl-4 col-lg-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 text-primary fw-bold"><i class="ti ti-calendar me-2"></i><?= htmlspecialchars($jour) ?></h5>
                </div>
                <div class="card-body bg-light bg-opacity-50 pt-0">
                    <?php if (empty($seancesJour)): ?>
                        <div class="text-center p-3 text-muted border border-dashed rounded">
                            <small>Aucune séance prévue</small>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-2 mt-2">
                            <?php foreach ($seancesJour as $seance): ?>
                                <div class="bg-white p-3 rounded shadow-sm border border-light position-relative">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                            <i class="ti ti-clock me-1"></i><?= date('H:i', strtotime($seance['heure_debut'])) ?>
                                        </span>
                                        <a href="index.php?c=seance&action=delete&id=<?= $seance['id'] ?>" class="text-danger" onclick="return confirm('Annuler cette séance ?');" title="Supprimer">
                                            <i class="ti ti-x"></i>
                                        </a>
                                    </div>
                                    <h6 class="mb-1 mt-2 fw-semibold"><?= htmlspecialchars($seance['activite_nom']) ?></h6>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 small"><?= htmlspecialchars($seance['statut']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Ajouter Séance -->
<div class="modal fade" id="addSeanceModal" tabindex="-1" aria-labelledby="addSeanceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSeanceModalLabel">Planifier une activité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="index.php?c=seance&action=create" method="POST">
          <div class="modal-body">
            <div class="mb-3">
                <label for="jour_semaine" class="form-label">Jour de la semaine <span class="text-danger">*</span></label>
                <select name="jour_semaine" id="jour_semaine" class="form-select" required>
                    <option value="Lundi">Lundi</option>
                    <option value="Mardi">Mardi</option>
                    <option value="Mercredi">Mercredi</option>
                    <option value="Jeudi">Jeudi</option>
                    <option value="Vendredi">Vendredi</option>
                    <option value="Samedi">Samedi</option>
                    <option value="Dimanche">Dimanche</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="heure_debut" class="form-label">Heure <span class="text-danger">*</span></label>
                <input type="time" class="form-control" name="heure_debut" id="heure_debut" required>
            </div>
            <div class="mb-3">
                <label for="activite_id" class="form-label">Activité <span class="text-danger">*</span></label>
                <select name="activite_id" id="activite_id" class="form-select" required>
                    <option value="">Sélectionnez...</option>
                    <?php foreach ($activites as $act): ?>
                        <option value="<?= $act['id'] ?>"><?= htmlspecialchars($act['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Ajouter au calendrier</button>
          </div>
      </form>
    </div>
  </div>
</div>

<?php 
$content = ob_get_clean();
require 'views/layout_back.php'; 
?>
