<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../../controllers/ObjectiveController.php';

$objectiveController = new ObjectiveController();
$objectives = $objectiveController->getAllObjectives();

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = htmlspecialchars($_GET['delete']);
    if ($objectiveController->delete($deleteId)) {
        $_SESSION['success_message'] = "Objectif supprimé avec succès!";
        header('Location: objectives.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression de l'objectif!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Objectives Management - NutriMind Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/logooo.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logooo.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/logooo.png">
  <link rel="manifest" href="assets/site.webmanifest">

  <script type="module" crossorigin src="assets/js/main.js"></script>
  <link rel="stylesheet" crossorigin href="assets/css/main.css">
  <style>
    #sidebar {
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: 250px;
      z-index: 1030;
      overflow-y: auto;
    }

    #main {
      margin-left: 250px;
      min-height: 100vh;
      padding-top: 80px;
    }

    .objectives-card {
      overflow-x: visible;
      width: 100%;
    }

    .table-responsive {
      display: block;
      width: 100%;
      overflow-x: auto !important;
      -webkit-overflow-scrolling: touch;
    }

    .objectives-table {
      width: 100%;
      min-width: 1200px;
      max-width: none;
      border-collapse: collapse;
    }

    .objectives-table th,
    .objectives-table td {
      vertical-align: middle;
      white-space: nowrap;
    }

    .objectives-table td.description-cell {
      max-width: 260px;
      white-space: normal;
      word-break: break-word;
    }

    @media (max-width: 1200px) {
      .objectives-table td.description-cell { max-width: 180px; }
    }
    @media (max-width: 992px) {
      .objectives-table th:nth-child(2), .objectives-table td:nth-child(2),
      .objectives-table th:nth-child(8), .objectives-table td:nth-child(8) { display: none; }
    }
    @media (max-width: 768px) {
      .objectives-table th:nth-child(1), .objectives-table td:nth-child(1),
      .objectives-table th:nth-child(4), .objectives-table td:nth-child(4),
      .objectives-table th:nth-child(6), .objectives-table td:nth-child(6),
      .objectives-table th:nth-child(10), .objectives-table td:nth-child(10) { display: none; }
      .objectives-table td.description-cell { max-width: 140px; }
    }

    /* Stat cards */
    .stat-card { border: none; border-radius: 12px; transition: transform .15s; }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-card .stat-icon {
      width: 48px; height: 48px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center; font-size: 1.4rem;
    }

    /* Card headers */
    .card-header-colored {
      border-radius: 10px 10px 0 0 !important;
      border-bottom: none;
      padding: 1rem 1.25rem;
    }
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
      <!-- Navbar nav -->
      <ul class="list-unstyled d-flex align-items-center mb-0 gap-1">
        <!-- Pages link -->

        <!-- Bell icon -->
        <li>
          <a class="position-relative btn-icon btn-sm btn-light btn rounded-circle" data-bs-toggle="dropdown"
            href="#" role="button" aria-expanded="false">
            <i class="ti ti-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
              style="font-size: 0.6rem;">3</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">New user registered</a></li>
            <li><a class="dropdown-item" href="#">Order placed</a></li>
            <li><a class="dropdown-item" href="#">Payment received</a></li>
          </ul>
        </li>

        <!-- User dropdown -->
        <li>
          <a class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="dropdown" href="#" role="button"
            aria-expanded="false">
            <i class="ti ti-user"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
            <li><a class="dropdown-item" href="settings.php">Settings</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="../auth.php?logout=1">Logout</a></li>
          </ul>
        </li>
      </ul>
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
            class="nav-text">Ingrédients</span></a></li>
      <li class="px-4 py-2"><small class="nav-text">Planning</small></li>
      <li><a class="nav-link" href="planning_list.php"><i class="ti ti-calendar-event"></i><span
            class="nav-text">Gérer les plans</span></a></li>
      <li><a class="nav-link" href="planning_create.php"><i class="ti ti-plus"></i><span
            class="nav-text">Créer un plan</span></a></li>
      <li><a class="nav-link active" href="objectives.php"><i class="ti ti-target"></i><span
            class="nav-text">Objectifs</span></a></li>

            <li class="px-4 py-2"><small class="nav-text">Sport</small></li>
      <li><a class="nav-link" href="../../index.php?c=activite"><i class="ti ti-activity"></i><span
            class="nav-text">Activit�s Sportives</span></a></li>

      <li class="px-4 pt-4 pb-2"><small class="nav-text">Compte</small></li>
      <li><a class="nav-link" href="#" onclick="logout(); return false;"><i class="ti ti-logout"></i><span class="nav-text">Déconnexion</span></a>
      </li>
    </ul>
  </aside>

  <!-- MAIN CONTENT -->
  <main id="main" class="main bg-light">
    <div class="container-fluid py-4">
      <!-- Page Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h3 mb-0">Gestion des Objectifs</h1>
          <p class="text-muted">Gérer les objectifs des utilisateurs</p>
        </div>
      </div>

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

      <!-- Stat Cards -->
      <?php
        $total    = count($objectives);
        $active   = count(array_filter($objectives, fn($o) => strtolower($o['statut']) === 'active'));
        $inactive = count(array_filter($objectives, fn($o) => strtolower($o['statut']) === 'inactive'));
        $pending  = count(array_filter($objectives, fn($o) => strtolower($o['statut']) === 'pending'));
      ?>
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <div class="card stat-card p-3" style="background: linear-gradient(135deg,#6366f1,#818cf8);">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon bg-white bg-opacity-25 text-white"><i class="ti ti-list-check"></i></div>
              <div class="text-white">
                <div class="fs-4 fw-bold"><?php echo $total; ?></div>
                <div class="small opacity-75">Total</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card stat-card p-3" style="background: linear-gradient(135deg,#10b981,#34d399);">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon bg-white bg-opacity-25 text-white"><i class="ti ti-circle-check"></i></div>
              <div class="text-white">
                <div class="fs-4 fw-bold"><?php echo $active; ?></div>
                <div class="small opacity-75">Actifs</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card stat-card p-3" style="background: linear-gradient(135deg,#f59e0b,#fbbf24);">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon bg-white bg-opacity-25 text-white"><i class="ti ti-clock"></i></div>
              <div class="text-white">
                <div class="fs-4 fw-bold"><?php echo $pending; ?></div>
                <div class="small opacity-75">En attente</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card stat-card p-3" style="background: linear-gradient(135deg,#ef4444,#f87171);">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon bg-white bg-opacity-25 text-white"><i class="ti ti-circle-x"></i></div>
              <div class="text-white">
                <div class="fs-4 fw-bold"><?php echo $inactive; ?></div>
                <div class="small opacity-75">Inactifs</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search Section -->
      <div class="row mb-4">
        <div class="col-12">
          <input type="text" id="objectivesSearchInput" placeholder="Rechercher des objectifs..."
                 class="form-control" style="max-width: 300px;">
        </div>
      </div>

      <!-- Objectives Table -->
      <div class="card objectives-card">
        <div class="card-header card-header-colored" style="background: linear-gradient(135deg,#6366f1,#818cf8);">
          <h5 class="mb-0 text-white"><i class="ti ti-target me-2"></i>Tous les objectifs</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover objectives-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Utilisateur</th>
                  <th>Type</th>
                  <th>Valeur cible</th>
                  <th>Poids initial</th>
                  <th>Date limite</th>
                  <th>Statut</th>
                  <th>Priorité</th>
                  <th>Description</th>
                  <th>Créé le</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="objectivesTableBody">
                <?php if (!empty($objectives)): ?>
                  <?php foreach ($objectives as $obj): ?>
                    <?php
                      $statut = strtolower($obj['statut']);
                      $badgeColor = match($statut) {
                        'active'   => 'success',
                        'pending'  => 'warning',
                        'inactive' => 'danger',
                        default    => 'secondary'
                      };
                    ?>
                    <tr>
                      <td><?php echo htmlspecialchars($obj['id_objectif']); ?></td>
                      <td><?php echo htmlspecialchars($obj['user_nom'] ?? 'Inconnu'); ?></td>
                      <td><?php echo htmlspecialchars($obj['type_objectif']); ?></td>
                      <td><?php echo htmlspecialchars($obj['valeur_cible']); ?></td>
                      <td><?php echo htmlspecialchars($obj['poids_initial']); ?></td>
                      <td><?php echo htmlspecialchars($obj['date_limite']); ?></td>
                      <td>
                        <span class="badge bg-<?php echo $badgeColor; ?>">
                          <?php echo htmlspecialchars($obj['statut']); ?>
                        </span>
                      </td>
                      <td><?php echo htmlspecialchars($obj['niveau_priorite']); ?></td>
                      <td class="description-cell"><?php echo htmlspecialchars($obj['description']); ?></td>
                      <td><?php echo htmlspecialchars($obj['date_creation']); ?></td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="?delete=<?php echo $obj['id_objectif']; ?>" class="btn btn-sm btn-outline-danger"
                             onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet objectif ?')">
                            <i class="ti ti-trash"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="11" class="text-center">Aucun objectif trouvé.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Statistics Section -->
      <div class="card mt-4">
        <div class="card-header card-header-colored" style="background: linear-gradient(135deg,#10b981,#34d399);">
          <h5 class="mb-0 text-white"><i class="ti ti-chart-pie me-2"></i>Statistiques des objectifs</h5>
        </div>
        <div class="card-body d-flex justify-content-center">
          <div style="max-width: 380px; width: 100%;">
            <canvas id="objectiveStatsPie"></canvas>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
  <script>
    // Sidebar toggle
    const toggleBtn = document.getElementById('toggleBtn');
    const mobileBtn = document.getElementById('mobileBtn');
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const overlay = document.getElementById('overlay');

    function toggleSidebar() {
      sidebar.classList.toggle('show');
      overlay.classList.toggle('show');
    }

    toggleBtn.addEventListener('click', toggleSidebar);
    mobileBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);

    // Responsive behavior
    window.addEventListener('resize', function() {
      if (window.innerWidth >= 992) {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
      }
    });

    // Dynamic search functionality
    const searchInput = document.getElementById('objectivesSearchInput');
    const tableBody = document.getElementById('objectivesTableBody');
    const tableRows = tableBody.getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function() {
      const searchTerm = this.value.toLowerCase();
      for (let i = 0; i < tableRows.length; i++) {
        const row = tableRows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < cells.length; j++) {
          if (cells[j].textContent.toLowerCase().includes(searchTerm)) {
            found = true;
            break;
          }
        }
        row.style.display = found ? '' : 'none';
      }
    });

    // Initialize pie chart for objectives statistics
    function initializeObjectiveStatsChart() {
      const rows = document.querySelectorAll('#objectivesTableBody tr:not([style*="display: none"])');
      const statusCounts = {};
      
      rows.forEach(row => {
        const statusCell = row.cells[6];
        if (statusCell) {
          const status = statusCell.textContent.trim().toLowerCase();
          statusCounts[status] = (statusCounts[status] || 0) + 1;
        }
      });

      const labels = Object.keys(statusCounts).map(s => s.charAt(0).toUpperCase() + s.slice(1));
      const data = Object.values(statusCounts);
      const palette = {
        'active':'#10b981','actif':'#10b981',
        'inactive':'#ef4444','inactif':'#ef4444',
        'pending':'#f59e0b','en_attente':'#f59e0b','en attente':'#f59e0b',
        'en_cours':'#6366f1','en cours':'#6366f1',
        'termine':'#10b981','terminé':'#10b981',
        'annule':'#ef4444','annulé':'#ef4444',
        'cancelled':'#ef4444'
      };
      const backgroundColors = Object.keys(statusCounts).map(s => palette[s] || '#94a3b8');

      const ctx = document.getElementById('objectiveStatsPie');
      if (ctx && ctx.chart) { ctx.chart.destroy(); }

      ctx.chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: backgroundColors,
            borderColor: '#fff',
            borderWidth: 3,
            hoverOffset: 8
          }]
        },
        options: {
          responsive: true,
          cutout: '60%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: { padding: 16, font: { size: 13 }, usePointStyle: true, pointStyleWidth: 10 }
            },
            title: {
              display: true,
              text: 'Objectifs par statut',
              font: { size: 15, weight: 'bold' },
              padding: { bottom: 16 }
            }
          }
        }
      });
    }

    // Initialize chart on page load
    document.addEventListener('DOMContentLoaded', function() {
      initializeObjectiveStatsChart();
      
      // Re-initialize chart when search changes
      searchInput.addEventListener('keyup', function() {
        setTimeout(initializeObjectiveStatsChart, 100);
      });
    });
  </script>
</body>
</html>
