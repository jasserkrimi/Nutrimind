<?php ob_start(); ?>

<!-- Statistiques Dashboard -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-12">
        <div class="card p-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-2 h-100">
            <div class="d-flex gap-3">
                <div class="icon-shape icon-md bg-primary text-white rounded-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="ti ti-activity fs-4"></i>
                </div>
                <div>
                    <h2 class="mb-1 fs-6 text-muted">Total Activités</h2>
                    <h3 class="fw-bold mb-0 text-primary"><?= htmlspecialchars($total_activites ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-12">
        <div class="card p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2 h-100">
            <div class="d-flex gap-3">
                <div class="icon-shape icon-md bg-success text-white rounded-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="ti ti-stretching fs-4"></i>
                </div>
                <div>
                    <h2 class="mb-1 fs-6 text-muted">Total Exercices</h2>
                    <h3 class="fw-bold mb-0 text-success"><?= htmlspecialchars($total_exercices ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 col-12">
        <div class="card p-3 h-100 border-0 shadow-sm">
            <h6 class="text-muted mb-3">Répartition par Catégorie</h6>
            <div class="d-flex flex-wrap gap-2">
                <?php 
                $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];
                $i = 0;
                if (!empty($categories_stats)) {
                    foreach ($categories_stats as $stat) { 
                        $color = $colors[$i % count($colors)];
                        $i++;
                ?>
                    <div class="d-flex flex-column align-items-center p-2 rounded <?= $color ?> bg-opacity-10 border border-opacity-25 px-3" style="min-width: 90px;">
                        <span class="fs-4 fw-bold text-dark"><?= htmlspecialchars($stat['count']) ?></span>
                        <span class="small text-muted text-center" style="font-size: 11px;"><?= htmlspecialchars($stat['categorie']) ?></span>
                    </div>
                <?php 
                    }
                } else {
                    echo "<span class='text-muted small'>Aucune donnée</span>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <div>
        <h1 class="fs-3 mb-1">Gestion des Activités</h1>
        <p class="mb-0 text-muted">Gérer le catalogue des activités sportives</p>
      </div>
      <div class="d-flex gap-2">
        <!-- Recherche et Tri -->
        <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par nom..." style="width: 200px;">
        <select id="categoryFilter" class="form-select" style="width: 200px;">
            <option value="Tous">Toutes les catégories</option>
            <?php 
            if (!empty($categories_stats)) {
                foreach ($categories_stats as $stat) { 
            ?>
                <option value="<?= htmlspecialchars($stat['categorie']) ?>"><?= htmlspecialchars($stat['categorie']) ?></option>
            <?php 
                }
            }
            ?>
        </select>
        <a href="index.php?c=activite&action=create" class="btn btn-primary d-flex align-items-center">
          <i class="ti ti-plus me-1"></i>Nouvelle Activité
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="activitiesTable">
            <thead class="table-light">
              <tr>
                <th class="ps-4 py-3 text-muted fw-semibold small text-uppercase">ID</th>
                <th class="py-3 text-muted fw-semibold small text-uppercase">Nom</th>
                <th class="py-3 text-muted fw-semibold small text-uppercase">Catégorie</th>
                <th class="py-3 text-muted fw-semibold small text-uppercase">Description</th>
                <th class="text-end pe-4 py-3 text-muted fw-semibold small text-uppercase">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($activites)): ?>
                <tr>
                  <td colspan="5" class="text-center py-5 text-muted">
                    <i class="ti ti-activity fs-1 d-block mb-2"></i>
                    Aucune activité trouvée
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($activites as $activite): ?>
                  <tr class="activity-row">
                    <td class="ps-4 text-muted fw-medium">#<?php echo htmlspecialchars($activite['id']); ?></td>
                    <td class="fw-semibold text-dark activity-name"><?php echo htmlspecialchars($activite['nom']); ?></td>
                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 activity-category"><?php echo htmlspecialchars($activite['categorie']); ?></span></td>
                    <td class="text-muted small"><?php echo htmlspecialchars(substr($activite['description'] ?? '', 0, 60)) . (strlen($activite['description'] ?? '') > 60 ? '...' : ''); ?></td>
                    <td class="text-end pe-4">
                      <a href="index.php?c=exercice&action=index&activite_id=<?= $activite['id'] ?>" class="btn btn-sm btn-light text-primary border" title="Gérer les exercices">
                        <i class="ti ti-stretching"></i> Exercices
                      </a>
                      <a href="index.php?c=activite&action=edit&id=<?php echo htmlspecialchars($activite['id']); ?>" class="btn btn-sm btn-light text-warning border" title="Modifier">
                        <i class="ti ti-edit"></i>
                      </a>
                      <a href="index.php?c=activite&action=delete&id=<?php echo htmlspecialchars($activite['id']); ?>" class="btn btn-sm btn-light text-danger border" onclick="return confirm('Supprimer définitivement cette activité ?');" title="Supprimer">
                        <i class="ti ti-trash"></i>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const rows = document.querySelectorAll('.activity-row');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryTerm = categoryFilter.value;

        rows.forEach(row => {
            const name = row.querySelector('.activity-name').textContent.toLowerCase();
            const category = row.querySelector('.activity-category').textContent;
            
            const matchesSearch = name.includes(searchTerm);
            const matchesCategory = (categoryTerm === 'Tous' || category === categoryTerm);

            if (matchesSearch && matchesCategory) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterTable);
    categoryFilter.addEventListener('change', filterTable);
});
</script>

<?php 
$content = ob_get_clean();
require 'views/layout_back.php'; 
?>
