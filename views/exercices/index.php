<?php ob_start(); ?>
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="fs-3 mb-1">Exercices : <?= htmlspecialchars($activite['nom']) ?></h1>
        <p>Gérer les exercices de cette activité</p>
      </div>
      <div>
          <a href="index.php?c=exercice&action=create&activite_id=<?= $activite_id ?>" class="btn btn-primary me-2">
            <i class="ti ti-plus me-2"></i>Nouvel Exercice
          </a>
          <a href="index.php?c=activite&action=index" class="btn btn-secondary">
            <i class="ti ti-arrow-left me-2"></i>Retour
          </a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Difficulté</th>
                <th>Description</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($exercices)): ?>
                <tr>
                  <td colspan="5" class="text-center">Aucun exercice trouvé pour cette activité</td>
                </tr>
              <?php else: ?>
                <?php foreach ($exercices as $exercice): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($exercice['id']); ?></td>
                    <td><?php echo htmlspecialchars($exercice['nom']); ?></td>
                    <td><span class="badge bg-info"><?php echo htmlspecialchars($exercice['difficulte'] ?? 'N/A'); ?></span></td>
                    <td><?php echo htmlspecialchars(substr($exercice['description'] ?? '', 0, 50)) . '...'; ?></td>
                    <td>
                      <a href="index.php?c=exercice&action=edit&id=<?php echo htmlspecialchars($exercice['id']); ?>" class="btn btn-sm btn-warning">
                        <i class="ti ti-edit"></i> Modifier
                      </a>
                      <a href="index.php?c=exercice&action=delete&id=<?php echo htmlspecialchars($exercice['id']); ?>&activite_id=<?= $activite_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?');">
                        <i class="ti ti-trash"></i> Supprimer
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php 
$content = ob_get_clean();
require 'views/layout_back.php'; 
?>
