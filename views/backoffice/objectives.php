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

    .objectives-table th,
    .objectives-table td {
      vertical-align: middle;
    }

    @media (max-width: 1200px) {
      .objectives-table td.description-cell {
        max-width: 180px;
      }
    }

    @media (max-width: 992px) {
      .objectives-table th:nth-child(2),
      .objectives-table td:nth-child(2),
      .objectives-table th:nth-child(8),
      .objectives-table td:nth-child(8) {
        display: none;
      }
    }

    @media (max-width: 768px) {
      .objectives-table th:nth-child(1),
      .objectives-table td:nth-child(1),
      .objectives-table th:nth-child(4),
      .objectives-table td:nth-child(4),
      .objectives-table th:nth-child(6),
      .objectives-table td:nth-child(6),
      .objectives-table th:nth-child(10),
      .objectives-table td:nth-child(10) {
        display: none;
      }
      .objectives-table td.description-cell {
        max-width: 140px;
      }
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
  <aside id="sidebar" class="sidebar bg-white border-end">
    <div class="d-flex flex-column h-100">
      <!-- Logo -->
      <div class="p-3 border-bottom">
        <a href="index.php" class="d-flex align-items-center text-decoration-none">
          <img src="assets/images/logooo.png" alt="Logo" width="32" height="32" class="me-2">
          <span class="fw-bold text-dark">NutriMind</span>
        </a>
      </div>

      <!-- Navigation -->
      <nav class="flex-grow-1 p-3">
        <ul class="nav nav-pills flex-column gap-2">
          <li class="nav-item">
            <a href="index.php" class="nav-link">
              <i class="ti ti-dashboard me-2"></i>Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a href="inventory.html" class="nav-link">
              <i class="ti ti-package me-2"></i>Inventory
            </a>
          </li>
          <li class="nav-item">
            <a href="users.php" class="nav-link">
              <i class="ti ti-users me-2"></i>Users
            </a>
          </li>
          <li class="nav-item">
            <a href="planning_list.php" class="nav-link">
              <i class="ti ti-calendar me-2"></i>Planning
            </a>
          </li>
          <li class="nav-item">
            <a href="objectives.php" class="nav-link active">
              <i class="ti ti-target me-2"></i>Objectives
            </a>
          </li>
          <li class="nav-item">
            <a href="reports.html" class="nav-link">
              <i class="ti ti-chart-bar me-2"></i>Reports
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main id="main" class="main bg-light">
    <div class="container-fluid py-4">
      <!-- Page Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h3 mb-0">Objectives Management</h1>
          <p class="text-muted">Manage user objectives</p>
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

      <!-- Objectives Table -->
      <div class="card objectives-card">
        <div class="card-header">
          <h5 class="mb-0">All Objectives</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover objectives-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Type</th>
                  <th>Target Value</th>
                  <th>Initial Weight</th>
                  <th>Deadline</th>
                  <th>Status</th>
                  <th>Priority</th>
                  <th>Description</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($objectives)): ?>
                  <?php foreach ($objectives as $obj): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($obj['id_objectif']); ?></td>
                      <td><?php echo htmlspecialchars($obj['user_nom'] ?? 'Unknown'); ?></td>
                      <td><?php echo htmlspecialchars($obj['type_objectif']); ?></td>
                      <td><?php echo htmlspecialchars($obj['valeur_cible']); ?></td>
                      <td><?php echo htmlspecialchars($obj['poids_initial']); ?></td>
                      <td><?php echo htmlspecialchars($obj['date_limite']); ?></td>
                      <td>
                        <span class="badge bg-<?php echo $obj['statut'] === 'active' ? 'success' : 'secondary'; ?>">
                          <?php echo htmlspecialchars($obj['statut']); ?>
                        </span>
                      </td>
                      <td><?php echo htmlspecialchars($obj['niveau_priorite']); ?></td>
                      <td class="description-cell"><?php echo htmlspecialchars($obj['description']); ?></td>
                      <td><?php echo htmlspecialchars($obj['date_creation']); ?></td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="?delete=<?php echo $obj['id_objectif']; ?>" class="btn btn-sm btn-outline-danger"
                             onclick="return confirm('Are you sure you want to delete this objective?')">
                            <i class="ti ti-trash"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="11" class="text-center">No objectives found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
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
  </script>
</body>
</html>