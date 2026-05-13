<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../../controllers/PlanningController.php';
require_once '../../controllers/ObjectiveController.php';
require_once '../../controllers/UserController.php';

$planningController = new PlanningController();
$objectiveController = new ObjectiveController();
$userController = new UserController();

$plannings = $planningController->getAllWithDetails();

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = htmlspecialchars($_GET['delete']);
    if ($planningController->delete($deleteId)) {
        $_SESSION['success_message'] = "Plan supprimÃ© avec succÃ¨s!";
        header('Location: planning_list.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du plan!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Planning Management - NutriMind Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/logooo.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logooo.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/logooo.png">
  <link rel="manifest" href="assets/site.webmanifest">

  <script type="module" crossorigin src="assets/js/main.js"></script>
  <link rel="stylesheet" crossorigin href="assets/css/main.css">
  <style>
    #sidebar { width: 250px !important; }
    #sidebar .nav-text { display: inline !important; opacity: 1 !important; }
    #sidebar .logo-area img { display: block !important; }
    #content { margin-left: 250px !important; }
  </style>
</head>

<body>
  <div id="overlay" class="overlay"></div>
  <!-- TOPBAR -->
  <nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
    <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm ">
      <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>

    <!-- MOBILE -->
    <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
      <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>
    <div>
      <h4 class="mb-0">Planning Management</h4>
    </div>
  </nav>

  <!-- SIDEBAR -->
  <aside id="sidebar" class="sidebar">
    <div class="logo-area">
     <a href="index.php" class="d-inline-flex"><img src="assets/images/logooo.png" alt="Nutrimind" style="max-height: 50px; width: auto;"></a>
    </div>
    <ul class="nav flex-column">
      <li class="px-4 py-2"><small class="nav-text">Principal</small></li>
      <li><a class="nav-link" href="index.php"><i class="ti ti-home"></i><span
            class="nav-text">Tableau de bord</span></a></li>
      <li><a class="nav-link" href="users.php"><i class="ti ti-users"></i><span
            class="nav-text">Utilisateurs</span></a></li>
      <li class="px-4 py-2"><small class="nav-text">Nutrition</small></li>
      <li><a class="nav-link" href="index.php#meals-section"><i class="ti ti-tools-kitchen-2"></i><span
            class="nav-text">Repas</span></a></li>
      <li><a class="nav-link" href="index.php#ingredients-section"><i class="ti ti-leaf"></i><span
            class="nav-text">IngrÃ©dients</span></a></li>
      <li class="px-4 py-2"><small class="nav-text">Planning</small></li>
      <li><a class="nav-link active" href="planning_list.php"><i class="ti ti-calendar-event"></i><span
            class="nav-text">GÃ©rer les plans</span></a></li>
      <li><a class="nav-link" href="planning_create.php"><i class="ti ti-plus"></i><span
            class="nav-text">CrÃ©er un plan</span></a></li>
      <li><a class="nav-link" href="objectives.php"><i class="ti ti-target"></i><span
            class="nav-text">Objectifs</span></a></li>

      <li class="px-4 py-2"><small class="nav-text">CommunautÃ©</small></li>
      <li><a class="nav-link" href="post_list.php"><i class="ti ti-article"></i><span
            class="nav-text">Posts</span></a></li>
      <li><a class="nav-link" href="comment_list.php"><i class="ti ti-message"></i><span
            class="nav-text">Commentaires</span></a></li>

            <li class="px-4 py-2"><small class="nav-text">Sport</small></li>
      <li><a class="nav-link" href="../../index.php?c=activite"><i class="ti ti-activity"></i><span class="nav-text">Activités Sportives</span></a></li>
      <li><a class="nav-link" href="../../index.php?c=exercice"><i class="ti ti-stretching"></i><span class="nav-text">Exercices</span></a></li>
      <li><a class="nav-link" href="../../index.php?c=seance"><i class="ti ti-calendar"></i><span class="nav-text">Emploi du Temps</span></a></li>
      <li class="px-4 py-2"><small class="nav-text">Boutique</small></li>
      <li><a class="nav-link" href="../../index.php?c=produit"><i class="ti ti-shopping-cart"></i><span class="nav-text">Produits Sport</span></a></li>
    <li class="px-4 pt-4 pb-2"><small class="nav-text">Compte</small></li>
      <li><a class="nav-link" href="#" onclick="logout(); return false;"><i class="ti ti-logout"></i><span class="nav-text">DÃ©connexion</span></a>
      </li>
    </ul>
  </aside>

  <!-- MAIN CONTENT -->
  <main id="content" class="content py-10">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
              <h1 class="fs-3 mb-1">Gestion des Plans</h1>
              <p>GÃ©rer les plans nutritionnels des utilisateurs</p>
            </div>
            <a href="planning_create.php" class="btn btn-primary">
              <i class="ti ti-plus me-2"></i>CrÃ©er un Plan
            </a>
          </div>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <?php if (isset($_SESSION['success_message'])): ?>
        <div class="row mb-4">
          <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['error_message'])): ?>
        <div class="row mb-4">
          <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Search Bar -->
      <div class="row mb-4">
        <div class="col-12">
          <input type="text" id="planningSearchInput" placeholder="Rechercher dans les plans..." 
                 class="form-control" style="max-width: 500px;">
        </div>
      </div>

      <!-- Plans Table -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped table-hover" id="planningTable">
                  <thead class="table-dark">
                    <tr>
                      <th>ID</th>
                      <th>Utilisateur</th>
                      <th>Objectif</th>
                      <th>Titre</th>
                      <th>Calories/Jour</th>
                      <th>Date DÃ©but</th>
                      <th>Date Fin</th>
                      <th>Statut</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="planningTableBody">
                    <?php if (empty($plannings)): ?>
                      <tr>
                        <td colspan="9" class="text-center">Aucun plan trouvÃ©</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($plannings as $planning): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($planning['id_planning']); ?></td>
                          <td><?php echo htmlspecialchars($planning['user_name'] ?? 'Utilisateur inconnu'); ?></td>
                          <td><?php echo htmlspecialchars($planning['objectif_type'] ?? 'Objectif inconnu'); ?></td>
                          <td><?php echo htmlspecialchars($planning['titre'] ?? 'Sans titre'); ?></td>
                          <td><?php echo htmlspecialchars($planning['calories_par_jour'] ?? '-'); ?> kcal</td>
                          <td><?php echo htmlspecialchars($planning['date_debut'] ?? '-'); ?></td>
                          <td><?php echo htmlspecialchars($planning['date_fin'] ?? '-'); ?></td>
                          <td>
                            <span class="badge bg-<?php
                              switch($planning['statut']) {
                                case 'actif': echo 'success'; break;
                                case 'inactif': echo 'secondary'; break;
                                case 'termine': echo 'info'; break;
                                default: echo 'light';
                              }
                            ?>">
                              <?php echo htmlspecialchars($planning['statut'] ?? 'inconnu'); ?>
                            </span>
                          </td>
                          <td>
                            <a href="planning_edit.php?id=<?php echo htmlspecialchars($planning['id_planning']); ?>" class="btn btn-sm btn-warning">
                              <i class="ti ti-edit"></i> Modifier
                            </a>
                            <a href="#" class="btn btn-sm btn-danger delete-planning" data-id="<?php echo htmlspecialchars($planning['id_planning']); ?>">
                              <i class="ti ti-trash"></i> Supprimer
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <!-- Pagination -->
              <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div id="planPaginationInfo" class="text-muted small"></div>
                <nav><ul class="pagination pagination-sm mb-0" id="planPagination"></ul></nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          ÃŠtes-vous sÃ»r de vouloir supprimer ce plan ? Cette action est irrÃ©versible.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
          <a href="#" id="confirmDelete" class="btn btn-danger">Supprimer</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    // â”€â”€ Pagination + Search for planning â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const searchInput = document.getElementById('planningSearchInput');
    const tableBody   = document.getElementById('planningTableBody');
    const pagination  = document.getElementById('planPagination');
    const pageInfo    = document.getElementById('planPaginationInfo');
    const PER_PAGE    = 10;
    let currentPage   = 1;

    const allRows = Array.from(tableBody.querySelectorAll('tr'));

    function getFilteredRows() {
      const q = searchInput.value.toLowerCase().trim();
      if (!q) return allRows;
      return allRows.filter(row =>
        Array.from(row.cells).slice(0, 8).some(cell => cell.textContent.toLowerCase().includes(q))
      );
    }

    function render() {
      const filtered   = getFilteredRows();
      const totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
      if (currentPage > totalPages) currentPage = totalPages;

      const start = (currentPage - 1) * PER_PAGE;
      const end   = start + PER_PAGE;

      allRows.forEach(row => row.style.display = 'none');
      filtered.slice(start, end).forEach(row => row.style.display = '');

      if (filtered.length === 0) {
        pageInfo.textContent = 'Aucun rÃ©sultat trouvÃ©';
      } else {
        pageInfo.textContent = `Affichage ${start + 1}â€“${Math.min(end, filtered.length)} sur ${filtered.length}`;
      }

      pagination.innerHTML = '';

      const prev = document.createElement('li');
      prev.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
      prev.innerHTML = `<a class="page-link" href="#">&laquo;</a>`;
      prev.addEventListener('click', e => { e.preventDefault(); if (currentPage > 1) { currentPage--; render(); } });
      pagination.appendChild(prev);

      for (let p = 1; p <= totalPages; p++) {
        const li = document.createElement('li');
        li.className = `page-item ${p === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#">${p}</a>`;
        li.addEventListener('click', e => { e.preventDefault(); currentPage = p; render(); });
        pagination.appendChild(li);
      }

      const next = document.createElement('li');
      next.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
      next.innerHTML = `<a class="page-link" href="#">&raquo;</a>`;
      next.addEventListener('click', e => { e.preventDefault(); if (currentPage < totalPages) { currentPage++; render(); } });
      pagination.appendChild(next);
    }

    searchInput.addEventListener('input', () => { currentPage = 1; render(); });
    document.addEventListener('DOMContentLoaded', render);

    // Delete confirmation
    document.querySelectorAll('.delete-planning').forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        const planningId = this.getAttribute('data-id');
        document.getElementById('confirmDelete').href = 'planning_list.php?delete=' + planningId;
        $('#deleteModal').modal('show');
      });
    });

    // Logout function
    function showLogoutModal() {
        const modal = document.createElement('div');
        modal.className = 'logout-modal-overlay';
        modal.innerHTML = `
            <div class="logout-modal">
                <div class="logout-modal-content">
                    <h3>Confirmation de DÃ©connexion</h3>
                    <p>ÃŠtes-vous sÃ»r de vouloir vous dÃ©connecter?</p>
                    <div class="logout-modal-buttons">
                        <button class="logout-btn-cancel">Annuler</button>
                        <button class="logout-btn-confirm">DÃ©connexion</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const cancelBtn = modal.querySelector('.logout-btn-cancel');
        const confirmBtn = modal.querySelector('.logout-btn-confirm');
        
        const closeModal = () => modal.remove();
        
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        
        confirmBtn.addEventListener('click', () => {
            fetch('../../controllers/UserController.php?action=logout')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '../index.php';
                    } else {
                        alert('Error during logout');
                    }
                })
                .catch(error => {
                    console.error('Logout error:', error);
                    alert('An error occurred during logout');
                });
        });
    }
    
    function logout() {
        showLogoutModal();
    }
  </script>
</body>
</html>
