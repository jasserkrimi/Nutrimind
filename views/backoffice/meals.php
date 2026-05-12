<?php
session_start();

// Admin check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../../controllers/MealController.php';

$mealController = new MealController();

// Handle delete
if (isset($_GET['delete_meal'])) {
    $deleteId    = htmlspecialchars($_GET['delete_meal']);
    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($mealController->delete($deleteId)) {
        $_SESSION['success_message'] = "Repas supprimé avec succès!";
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du repas!";
    }
    header('Location: meals.php?page=' . $currentPage);
    exit;
}

// Pagination
$perPage     = 10;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$paginationData = $mealController->getPaginated($currentPage, $perPage);
$meals       = $paginationData['meals'];
$totalItems  = $paginationData['totalItems'];
$totalPages  = $paginationData['totalPages'];
$currentPage = $paginationData['currentPage'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Repas — NutriMind Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logooo.png">
  <script type="module" crossorigin src="assets/js/main.js"></script>
  <link rel="stylesheet" crossorigin href="assets/css/main.css">
</head>
<body>

<div id="overlay" class="overlay"></div>

<!-- TOPBAR -->
<nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
  <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm">
    <i class="ti ti-layout-sidebar-left-expand"></i>
  </button>
  <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
    <i class="ti ti-layout-sidebar-left-expand"></i>
  </button>
  <div>
    <ul class="list-unstyled d-flex align-items-center mb-0 gap-1">
      <li class="ms-3 dropdown">
        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="assets/images/avatar-1.jpg" alt="" class="avatar avatar-sm rounded-circle" />
        </a>
        <div class="dropdown-menu dropdown-menu-end p-0" style="min-width:250px;">
          <div class="d-flex gap-3 align-items-center border-dashed border-bottom px-4 py-3">
            <img src="assets/images/avatar-1.jpg" alt="" class="avatar avatar-md rounded-circle" />
            <div>
              <h5 class="mb-0 small fw-600"><?php echo htmlspecialchars($_SESSION['user_nom'] ?? ''); ?></h5>
              <p class="mb-0 text-muted small"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
            </div>
          </div>
          <div class="p-3 d-flex flex-column gap-2 small lh-lg">
            <a href="profile.php" class="text-decoration-none text-body d-flex align-items-center gap-2 px-2 py-2 rounded">
              <i class="ti ti-user text-success"></i><span>Profil</span>
            </a>
            <a href="#" onclick="logout(); return false;" class="text-decoration-none text-body d-flex align-items-center gap-2 px-2 py-2 rounded">
              <i class="ti ti-logout text-danger"></i><span>Déconnexion</span>
            </a>
          </div>
        </div>
      </li>
    </ul>
  </div>
</nav>

<!-- SIDEBAR -->
<aside id="sidebar" class="sidebar">
  <div class="logo-area">
    <a href="index.php" class="d-inline-flex"><img src="assets/images/logooo.png" alt="Nutrimind" style="max-height:50px;width:auto;"></a>
  </div>
  <ul class="nav flex-column">
    <li class="px-4 py-2"><small class="nav-text">Principal</small></li>
    <li><a class="nav-link" href="index.php"><i class="ti ti-home"></i><span class="nav-text">Tableau de bord</span></a></li>
    <li><a class="nav-link" href="users.php"><i class="ti ti-users"></i><span class="nav-text">Utilisateurs</span></a></li>
    <li class="px-4 py-2"><small class="nav-text">Nutrition</small></li>
    <li><a class="nav-link active" href="meals.php"><i class="ti ti-tools-kitchen-2"></i><span class="nav-text">Repas</span></a></li>
    <li><a class="nav-link" href="ingredients.php"><i class="ti ti-leaf"></i><span class="nav-text">Ingrédients</span></a></li>
    <li class="px-4 py-2"><small class="nav-text">Planning</small></li>
    <li><a class="nav-link" href="planning_list.php"><i class="ti ti-calendar-event"></i><span class="nav-text">Gérer les plans</span></a></li>
    <li><a class="nav-link" href="planning_create.php"><i class="ti ti-plus"></i><span class="nav-text">Créer un plan</span></a></li>
    <li><a class="nav-link" href="objectives.php"><i class="ti ti-target"></i><span class="nav-text">Objectifs</span></a></li>
    <li class="px-4 pt-4 pb-2"><small class="nav-text">Compte</small></li>
    <li><a class="nav-link" href="#" onclick="logout(); return false;"><i class="ti ti-logout"></i><span class="nav-text">Déconnexion</span></a></li>
  </ul>
</aside>

<!-- MAIN CONTENT -->
<main id="content" class="content py-10">
  <div class="container-fluid">

    <!-- Page header -->
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="fs-3 mb-1"><i class="ti ti-tools-kitchen-2 me-2 text-success"></i>Gestion des Repas</h1>
        <p class="text-muted mb-0"><?php echo $totalItems; ?> repas enregistrés</p>
      </div>
    </div>

    <!-- Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Meals card -->
    <div class="row g-3">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-white px-4 py-3">
            <!-- Search and Sort -->
            <div class="row g-2">
              <div class="col-md-6">
                <input type="text" id="mealsSearchInput" class="form-control" placeholder="Rechercher les repas par nom...">
              </div>
              <div class="col-md-6">
                <select id="mealsSortSelect" class="form-select">
                  <option value="name-asc">Trier par: Nom (A-Z)</option>
                  <option value="name-desc">Trier par: Nom (Z-A)</option>
                  <option value="date-newest">Trier par: Date (Plus récent)</option>
                  <option value="date-oldest">Trier par: Date (Plus ancien)</option>
                </select>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0" id="mealsTable">
                <thead class="table-dark">
                  <tr>
                    <th>Nom</th>
                    <th>Date</th>
                    <th>Notes</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="mealsTableBody">
                  <?php if (empty($meals)): ?>
                    <tr><td colspan="4" class="text-center py-4"><em>Aucun repas trouvé</em></td></tr>
                  <?php else: ?>
                    <?php foreach ($meals as $meal): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($meal['name']); ?></td>
                        <td><?php echo htmlspecialchars($meal['date']); ?></td>
                        <td><?php echo htmlspecialchars(substr($meal['notes'], 0, 50)) . (strlen($meal['notes']) > 50 ? '...' : ''); ?></td>
                        <td>
                          <a href="#" class="btn btn-sm btn-danger delete-meal" data-id="<?php echo htmlspecialchars($meal['id']); ?>">
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

          <!-- Pagination -->
          <?php if ($totalPages > 0): ?>
          <div class="card-footer bg-white d-flex align-items-center justify-content-between px-4 py-3">
            <small class="text-muted">
              <?php
                $from = ($currentPage - 1) * $perPage + 1;
                $to   = min($currentPage * $perPage, $totalItems);
                echo "Affichage de $from à $to sur $totalItems repas";
              ?>
            </small>
            <nav>
              <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                  <a class="page-link" href="meals.php?page=<?php echo $currentPage - 1; ?>">&laquo;</a>
                </li>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                  <li class="page-item <?php echo $p === $currentPage ? 'active' : ''; ?>">
                    <a class="page-link" href="meals.php?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                  </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                  <a class="page-link" href="meals.php?page=<?php echo $currentPage + 1; ?>">&raquo;</a>
                </li>
              </ul>
            </nav>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <footer class="text-center py-4 mt-4 text-secondary">
      <p class="mb-0">Copyright © 2026 NutriMind Admin</p>
    </footer>

  </div>
</main>

<script>
  // Delete confirmation
  document.querySelectorAll('.delete-meal').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      if (confirm('Êtes-vous sûr de vouloir supprimer ce repas ?')) {
        window.location.href = 'meals.php?delete_meal=' + this.getAttribute('data-id');
      }
    });
  });

  // Search & Sort
  const mealsSearchInput = document.getElementById('mealsSearchInput');
  const mealsSortSelect  = document.getElementById('mealsSortSelect');
  const mealsTableBody   = document.getElementById('mealsTableBody');
  let mealsData = [];

  function initMeals() {
    mealsData = [];
    mealsTableBody.querySelectorAll('tr').forEach(function (row) {
      var cells = row.querySelectorAll('td');
      if (cells.length >= 4) {
        mealsData.push({
          name: cells[0].textContent.trim(),
          date: cells[1].textContent.trim(),
          notes: cells[2].textContent.trim(),
          actions: cells[3].innerHTML
        });
      }
    });
  }

  function filterMeals() {
    var term = mealsSearchInput.value.toLowerCase();
    var sort = mealsSortSelect.value;
    var filtered = mealsData.filter(function (m) { return m.name.toLowerCase().includes(term); });
    if (sort === 'name-asc')      filtered.sort(function (a,b) { return a.name.localeCompare(b.name); });
    if (sort === 'name-desc')     filtered.sort(function (a,b) { return b.name.localeCompare(a.name); });
    if (sort === 'date-newest')   filtered.sort(function (a,b) { return new Date(b.date) - new Date(a.date); });
    if (sort === 'date-oldest')   filtered.sort(function (a,b) { return new Date(a.date) - new Date(b.date); });
    if (filtered.length === 0) {
      mealsTableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4"><em>Aucun repas trouvé</em></td></tr>';
    } else {
      mealsTableBody.innerHTML = filtered.map(function (m) {
        return '<tr><td>' + m.name + '</td><td>' + m.date + '</td><td>' + m.notes + '</td><td>' + m.actions + '</td></tr>';
      }).join('');
      document.querySelectorAll('.delete-meal').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          if (confirm('Êtes-vous sûr de vouloir supprimer ce repas ?')) {
            window.location.href = 'meals.php?delete_meal=' + this.getAttribute('data-id');
          }
        });
      });
    }
  }

  mealsSearchInput.addEventListener('input', filterMeals);
  mealsSortSelect.addEventListener('change', filterMeals);
  initMeals();

  function logout() {
    if (confirm('Voulez-vous vous déconnecter ?')) {
      window.location.href = '../auth.php?logout=1';
    }
  }
</script>

</body>
</html>
