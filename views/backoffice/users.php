<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || 
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect non-admin users to home page
    header('Location: ../index.php');
    exit;
}

require_once '../../config/Database.php';

// Get statistics for all users (not paginated)
$database = new Database();
$db = $database->connect();

$statsQuery = "SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_count,
    SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as user_count
    FROM user";
$statsStmt = $db->prepare($statsQuery);
$statsStmt->execute();
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Utilisateurs - Tableau de Bord Nutrimind</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">
  <link rel="manifest" href="assets/site.webmanifest">

  <script type="module" crossorigin src="assets/js/main.js"></script>
  <link rel="stylesheet" crossorigin href="assets/css/main.css">
  <style>
    .role-badge {
      display: inline-block;
      padding: 0.35rem 0.65rem;
      border-radius: 0.25rem;
      font-size: 0.8rem;
      font-weight: 600;
    }
    .role-admin {
      background-color: #fee;
      color: #c33;
    }
    .role-user {
      background-color: #efe;
      color: #3c3;
    }
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
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
    <div class="ms-auto">
      <!-- Navbar nav -->
      <ul class="list-unstyled d-flex align-items-center mb-0 gap-1">
        <!-- Dropdown -->
        <li class="ms-3 dropdown">
          <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="assets/images/avatar-1.jpg" alt="" class="avatar avatar-sm rounded-circle" />
          </a>
          <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 250px; position: absolute; right: 0; top: 100%; margin-top: 0.5rem;" data-popper-placement="bottom-end">
            <div>
              <div class="d-flex gap-3 align-items-center border-dashed border-bottom px-4 py-3">
                <img src="assets/images/avatar-1.jpg" alt="" class="avatar avatar-md rounded-circle" />
                <div>
                  <h5 class="mb-0 small fw-600"><?php echo htmlspecialchars($_SESSION['user_nom']); ?></h5>
                  <p class="mb-0 text-muted small"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                </div>
              </div>
              <div class="p-3 d-flex flex-column gap-2 small lh-lg">
                <a href="profile.php" class="text-decoration-none text-body d-flex align-items-center gap-2 px-2 py-2 rounded" style="transition: all 0.2s ease;">
                  <i class="ti ti-user text-success"></i>
                  <span>Déconnexion</span>
                </a>
                <a href="#" onclick="logout(); return false;" class="text-decoration-none text-body d-flex align-items-center gap-2 px-2 py-2 rounded" style="transition: all 0.2s ease;">
                  <i class="ti ti-logout text-danger"></i>
                  <span>Déconnexion</span>
                </a>
              </div>
            </div>
          </div>
                  <span>Logout</span>
                </a>
              </div>
            </div>
          </div>
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
      <li><a class="nav-link active" href="users.php"><i class="ti ti-users"></i><span
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
      <li><a class="nav-link" href="objectives.php"><i class="ti ti-target"></i><span
            class="nav-text">Objectifs</span></a></li>

      <li class="px-4 pt-4 pb-2"><small class="nav-text">Compte</small></li>
      <li><a class="nav-link" href="#" onclick="logout(); return false;"><i class="ti ti-logout"></i><span class="nav-text">Déconnexion</span></a>
      </li>
    </ul>

  </aside>

  <!-- MAIN CONTENT -->
  <main id="content" class="content py-10">
    <div class="container-fluid">
      <div class="row ">
        <div class="col-12">
          <div class="mb-6">
            <h1 class="fs-3 mb-1">Gestion des Utilisateurs</h1>
            <p>Afficher et gérer tous les utilisateurs enregistrés</p>
          </div>
        </div>
      </div>
      
      <!-- Users Table -->
      <div class="row g-3">
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Liste des Utilisateurs</h5>
              <div class="d-flex gap-2">
                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Rechercher par nom ou email..." style="width: 250px;">
                <button id="exportPdfBtn" class="btn btn-primary btn-sm">
                  <i class="ti ti-download"></i> Exporter PDF
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div id="loadingSpinner" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Chargement...</span>
                </div>
              </div>
              <div class="table-responsive">
                <table id="usersTable" class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Utilisateur</th>
                      <th>Courrier Électronique</th>
                      <th>Rôle</th>
                      <th>Statut</th>
                      <th>Date d'Inscription</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="usersTableBody">
                    <!-- Users will be loaded here via AJAX -->
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <span id="paginationInfo" class="text-muted small"></span>
                </div>
                <nav>
                  <ul id="paginationControls" class="pagination pagination-sm mb-0">
                    <!-- Pagination will be loaded here -->
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- AI Email Campaign Section -->
      <div class="row g-3 mt-4">
        <div class="col-12">
          <div class="card border-primary">
            <div class="card-header bg-gradient d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
              <h5 class="mb-0 text-white">🤖 AI Email Campaign System</h5>
              <button id="toggleCampaignBtn" class="btn btn-light btn-sm">
                <i class="ti ti-chevron-down"></i>
              </button>
            </div>
            <div id="campaignSection" class="card-body" style="display: none;">
              <div class="row g-3">
                <!-- Segment Selection -->
                <div class="col-md-6">
                  <label class="form-label fw-bold">1️⃣ Select User Segment</label>
                  <select id="segmentSelect" class="form-select">
                    <option value="">-- Select Segment --</option>
                    <option value="super_active">🔥 Super Active Users</option>
                    <option value="vip">⭐ VIP Users (Admins)</option>
                    <option value="new_users">🆕 New Users (< 7 days)</option>
                    <option value="dormant">💤 Dormant Users (14-30 days)</option>
                    <option value="at_risk">⚠️ At Risk (30+ days inactive)</option>
                    <option value="incomplete_profile">📝 Incomplete Profiles</option>
                    <option value="allergy_alert">🚨 Users with Allergies</option>
                    <option value="all">👥 All Users</option>
                  </select>
                  <div id="segmentInfo" class="mt-2 small text-muted"></div>
                </div>

                <!-- Template Selection -->
                <div class="col-md-6">
                  <label class="form-label fw-bold">2️⃣ Select Email Template</label>
                  <select id="templateSelect" class="form-select">
                    <option value="welcome">🎉 Welcome Email</option>
                    <option value="reengagement">🔥 Re-engagement</option>
                    <option value="profile_completion">📝 Profile Completion</option>
                    <option value="health_milestone">💪 Health Milestone</option>
                    <option value="allergy_alert">⚠️ Allergy Safety Alert</option>
                    <option value="birthday">🎂 Birthday Wishes</option>
                    <option value="progress_report">📊 Progress Report</option>
                    <option value="tips">💡 Health Tips</option>
                  </select>
                </div>

                <!-- Tone Selection -->
                <div class="col-md-6">
                  <label class="form-label fw-bold">3️⃣ Email Tone</label>
                  <select id="toneSelect" class="form-select">
                    <option value="friendly">😊 Friendly</option>
                    <option value="professional">👔 Professional</option>
                    <option value="motivational">💪 Motivational</option>
                    <option value="caring">💚 Caring</option>
                  </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-6 d-flex align-items-end">
                  <button id="generateAIEmailBtn" class="btn btn-primary me-2">
                    <i class="ti ti-users"></i> Show Users
                  </button>
                  <button id="viewHistoryBtn" class="btn btn-outline-secondary">
                    <i class="ti ti-history"></i> History
                  </button>
                </div>
              </div>

              <!-- User Selection Section -->
              <div id="userSelectionSection" class="mt-4" style="display: none;">
                <hr>
                <h6 class="fw-bold mb-3">👥 Select Users to Email</h6>
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div>
                    <button id="selectAllUsersBtn" class="btn btn-sm btn-outline-primary">
                      <i class="ti ti-checkbox"></i> Select All
                    </button>
                    <button id="deselectAllUsersBtn" class="btn btn-sm btn-outline-secondary">
                      <i class="ti ti-square"></i> Deselect All
                    </button>
                  </div>
                  <div>
                    <span id="selectedUserCount" class="badge bg-primary">0 selected</span>
                  </div>
                </div>
                <div id="userSelectionContainer" class="border rounded p-3" style="max-height: 400px; overflow-y: auto; background: #f8f9fa;">
                  <!-- Users will be displayed here -->
                </div>
                <div class="mt-3 text-center">
                  <button id="proceedToGenerateBtn" class="btn btn-primary btn-lg" disabled>
                    <i class="ti ti-sparkles"></i> Generate AI Emails for Selected Users
                  </button>
                </div>
              </div>

              <!-- Preview Section -->
              <div id="emailPreviewSection" class="mt-4" style="display: none;">
                <hr>
                <h6 class="fw-bold mb-3">📧 Email Preview & Send</h6>
                <div id="emailPreviewContainer"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div class="row g-3 mt-4">
        <div class="col-lg-4 col-12">
          <div class="card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="fw-bold h4"><?php echo $stats['total_users']; ?></h3>
                  <span class="text-muted">Utilisateurs Totaux</span>
                </div>
                <div>
                  <i class="ti ti-users fs-1 text-success"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-12">
          <div class="card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="fw-bold h4"><?php echo $stats['admin_count']; ?></h3>
                  <span class="text-muted">Utilisateurs Administrateurs</span>
                </div>
                <div>
                  <i class="ti ti-shield-check fs-1 text-danger"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-12">
          <div class="card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h3 class="fw-bold h4"><?php echo $stats['user_count']; ?></h3>
                  <span class="text-muted">Utilisateurs Normaux</span>
                </div>
                <div>
                  <i class="ti ti-user-check fs-1 text-success"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="assets/js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  
  <script>
    // Logout function
    function showLogoutModal() {
        const modal = document.createElement('div');
        modal.className = 'logout-modal-overlay';
        modal.innerHTML = `
            <div class="logout-modal">
                <div class="logout-modal-content">
                    <h3>Confirmation de Déconnexion</h3>
                    <p>Êtes-vous sûr de vouloir vous déconnecter?</p>
                    <div class="logout-modal-buttons">
                        <button class="logout-btn-cancel">Annuler</button>
                        <button class="logout-btn-confirm">Déconnexion</button>
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

    // Delete user functions
    function showDeleteModal(userId, userName) {
        const modal = document.createElement('div');
        modal.className = 'delete-modal-overlay';
        modal.innerHTML = `
            <div class="delete-modal">
                <div class="delete-modal-content">
                    <h3>Confirmation de Suppression</h3>
                    <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>${userName}</strong> ?</p>
                    <p class="text-danger small">Cette action est irréversible.</p>
                    <div class="delete-modal-buttons">
                        <button class="delete-btn-cancel">Annuler</button>
                        <button class="delete-btn-confirm" data-user-id="${userId}">Supprimer</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const cancelBtn = modal.querySelector('.delete-btn-cancel');
        const confirmBtn = modal.querySelector('.delete-btn-confirm');
        
        const closeModal = () => modal.remove();
        
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        
        confirmBtn.addEventListener('click', () => {
            const id = confirmBtn.getAttribute('data-user-id');
            deleteUser(id);
            closeModal();
        });
    }

    function deleteUser(userId) {
        fetch('../../controllers/UserController.php?action=delete_user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to refresh the user list
                location.reload();
            } else {
                alert('Erreur: ' + (data.errors ? data.errors.join(', ') : 'Une erreur est survenue'));
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            alert('Une erreur est survenue lors de la suppression');
        });
    }

    // Block user functions
    function blockUser(userId, userName) {
        const modal = document.createElement('div');
        modal.className = 'block-modal-overlay';
        modal.innerHTML = `
            <div class="block-modal">
                <div class="block-modal-content">
                    <h3>🔒 Bloquer l'utilisateur</h3>
                    <p>Êtes-vous sûr de vouloir bloquer <strong>${userName}</strong> ?</p>
                    <p class="text-warning small">L'utilisateur ne pourra plus se connecter à son compte.</p>
                    <div class="block-modal-buttons">
                        <button class="block-btn-cancel">Annuler</button>
                        <button class="block-btn-confirm" data-user-id="${userId}">Bloquer</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const cancelBtn = modal.querySelector('.block-btn-cancel');
        const confirmBtn = modal.querySelector('.block-btn-confirm');
        
        const closeModal = () => modal.remove();
        
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        
        confirmBtn.addEventListener('click', () => {
            const id = confirmBtn.getAttribute('data-user-id');
            performBlockUser(id);
            closeModal();
        });
    }

    function performBlockUser(userId) {
        fetch('../../controllers/UserController.php?action=block_user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + (data.errors ? data.errors.join(', ') : 'Une erreur est survenue'));
            }
        })
        .catch(error => {
            console.error('Block error:', error);
            alert('Une erreur est survenue lors du blocage');
        });
    }

    // Unblock user functions
    function unblockUser(userId, userName) {
        const modal = document.createElement('div');
        modal.className = 'block-modal-overlay';
        modal.innerHTML = `
            <div class="block-modal">
                <div class="block-modal-content">
                    <h3>🔓 Débloquer l'utilisateur</h3>
                    <p>Êtes-vous sûr de vouloir débloquer <strong>${userName}</strong> ?</p>
                    <p class="text-success small">L'utilisateur pourra à nouveau se connecter à son compte.</p>
                    <div class="block-modal-buttons">
                        <button class="block-btn-cancel">Annuler</button>
                        <button class="block-btn-confirm-success" data-user-id="${userId}">Débloquer</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const cancelBtn = modal.querySelector('.block-btn-cancel');
        const confirmBtn = modal.querySelector('.block-btn-confirm-success');
        
        const closeModal = () => modal.remove();
        
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        
        confirmBtn.addEventListener('click', () => {
            const id = confirmBtn.getAttribute('data-user-id');
            performUnblockUser(id);
            closeModal();
        });
    }

    function performUnblockUser(userId) {
        fetch('../../controllers/UserController.php?action=unblock_user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + (data.errors ? data.errors.join(', ') : 'Une erreur est survenue'));
            }
        })
        .catch(error => {
            console.error('Unblock error:', error);
            alert('Une erreur est survenue lors du déblocage');
        });
    }

    // ==================== USER PROFILE DASHBOARD ====================

    function viewUserProfile(userId) {
        // Show loading modal
        const loadingModal = document.createElement('div');
        loadingModal.className = 'profile-modal-overlay';
        loadingModal.id = 'profileModal';
        loadingModal.innerHTML = `
            <div class="profile-modal">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-3">Chargement du profil...</p>
                </div>
            </div>
        `;
        document.body.appendChild(loadingModal);

        // Fetch user profile data
        fetch(`../../controllers/UserController.php?action=get_user_profile_dashboard&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUserProfileDashboard(data.data);
                } else {
                    alert('Erreur: ' + (data.errors ? data.errors.join(', ') : 'Une erreur est survenue'));
                    loadingModal.remove();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue');
                loadingModal.remove();
            });
    }

    function displayUserProfileDashboard(data) {
        const modal = document.getElementById('profileModal');
        const user = data.user;
        
        // Calculate BMI
        let bmiHtml = '';
        if (user.poids && user.taille) {
            const bmi = (user.poids / ((user.taille / 100) ** 2)).toFixed(1);
            let bmiCategory = '';
            let bmiColor = '';
            if (bmi < 18.5) { bmiCategory = 'Insuffisant'; bmiColor = 'warning'; }
            else if (bmi < 25) { bmiCategory = 'Normal'; bmiColor = 'success'; }
            else if (bmi < 30) { bmiCategory = 'Surpoids'; bmiColor = 'warning'; }
            else { bmiCategory = 'Obésité'; bmiColor = 'danger'; }
            
            bmiHtml = `<span class="badge bg-${bmiColor}">${bmi} - ${bmiCategory}</span>`;
        } else {
            bmiHtml = '<span class="text-muted">Non calculé</span>';
        }

        // Profile completion
        const profileCompletion = calculateProfileCompletion(user);
        
        // Engagement score
        const engagementScore = data.engagement_score;
        let engagementColor = 'danger';
        if (engagementScore >= 70) engagementColor = 'success';
        else if (engagementScore >= 40) engagementColor = 'warning';

        modal.innerHTML = `
            <div class="profile-modal">
                <div class="profile-modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="user-avatar-large">${user.nom.charAt(0).toUpperCase()}</div>
                        <div>
                            <h3 class="mb-1">${escapeHtml(user.nom)}</h3>
                            <p class="text-muted mb-0">${escapeHtml(user.email)}</p>
                        </div>
                    </div>
                    <button class="btn-close-modal" onclick="closeProfileModal()">
                        <i class="ti ti-x"></i>
                    </button>
                </div>

                <div class="profile-modal-body">
                    <!-- Quick Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-label">Score d'Engagement</div>
                                <div class="stat-value">
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-${engagementColor}" style="width: ${engagementScore}%">
                                            ${engagementScore}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-label">Profil Complété</div>
                                <div class="stat-value">${profileCompletion}%</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-label">IMC</div>
                                <div class="stat-value">${bmiHtml}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-info">
                                <i class="ti ti-info-circle"></i> Informations
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-notes">
                                <i class="ti ti-notes"></i> Notes (${data.notes.length})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tags">
                                <i class="ti ti-tag"></i> Tags (${data.tags.length})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-activity">
                                <i class="ti ti-activity"></i> Activité
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-emails">
                                <i class="ti ti-mail"></i> Emails (${data.email_history.length})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ai-insights">
                                <i class="ti ti-sparkles"></i> 🤖 AI Insights
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Info Tab -->
                        <div class="tab-pane fade show active" id="tab-info">
                            ${renderInfoTab(user)}
                        </div>

                        <!-- Notes Tab -->
                        <div class="tab-pane fade" id="tab-notes">
                            ${renderNotesTab(user.id, data.notes)}
                        </div>

                        <!-- Tags Tab -->
                        <div class="tab-pane fade" id="tab-tags">
                            ${renderTagsTab(user.id, data.tags)}
                        </div>

                        <!-- Activity Tab -->
                        <div class="tab-pane fade" id="tab-activity">
                            ${renderActivityTab(data.activity, data.activity_stats)}
                        </div>

                        <!-- Emails Tab -->
                        <div class="tab-pane fade" id="tab-emails">
                            ${renderEmailsTab(data.email_history)}
                        </div>

                        <!-- AI Insights Tab -->
                        <div class="tab-pane fade" id="tab-ai-insights">
                            ${renderAIInsightsTab(user.id)}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function calculateProfileCompletion(user) {
        const fields = ['nom', 'email', 'age', 'poids', 'taille', 'allergique', 'role'];
        let filled = 0;
        fields.forEach(field => {
            if (user[field] !== null && user[field] !== '') filled++;
        });
        return Math.round((filled / fields.length) * 100);
    }

    function renderInfoTab(user) {
        return `
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Rôle</div>
                    <div class="info-value">
                        <span class="role-badge role-${user.role.toLowerCase()}">
                            ${user.role === 'admin' ? 'Administrateur' : 'Utilisateur'}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Statut</div>
                    <div class="info-value">
                        ${user.status === 'blocked' ? 
                            '<span class="badge bg-danger">🔒 Bloqué</span>' : 
                            '<span class="badge bg-success">✓ Actif</span>'}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Âge</div>
                    <div class="info-value">${user.age || '<span class="text-muted">Non renseigné</span>'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Poids</div>
                    <div class="info-value">${user.poids ? user.poids + ' kg' : '<span class="text-muted">Non renseigné</span>'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Taille</div>
                    <div class="info-value">${user.taille ? user.taille + ' cm' : '<span class="text-muted">Non renseigné</span>'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Allergies</div>
                    <div class="info-value">
                        ${user.allergique == 1 ? 
                            '<span class="badge bg-warning">⚠️ Oui</span>' : 
                            '<span class="badge bg-success">✓ Non</span>'}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date d'inscription</div>
                    <div class="info-value">${new Date(user.date_creation).toLocaleDateString('fr-FR')}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Dernière connexion</div>
                    <div class="info-value">
                        ${user.last_login ? new Date(user.last_login).toLocaleString('fr-FR') : '<span class="text-muted">Jamais</span>'}
                    </div>
                </div>
            </div>
        `;
    }

    function renderNotesTab(userId, notes) {
        let html = `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0 fw-bold">Ajouter une note</label>
                    <button class="btn btn-sm btn-outline-primary" onclick="getAINoteSuggestions(${userId})">
                        <i class="ti ti-sparkles"></i> 🤖 Suggestions AI
                    </button>
                </div>
                <textarea id="newNoteText" class="form-control" rows="3" placeholder="Ajouter une note privée..."></textarea>
                <div id="aiNoteSuggestions" class="mt-2" style="display: none;"></div>
                <button class="btn btn-primary btn-sm mt-2" onclick="addNote(${userId})">
                    <i class="ti ti-plus"></i> Ajouter Note
                </button>
            </div>
            <div class="notes-list">
        `;

        if (notes.length === 0) {
            html += '<p class="text-muted text-center py-4">Aucune note pour cet utilisateur</p>';
        } else {
            notes.forEach(note => {
                html += `
                    <div class="note-item">
                        <div class="note-header">
                            <strong>${escapeHtml(note.admin_name)}</strong>
                            <span class="text-muted small">${new Date(note.created_at).toLocaleString('fr-FR')}</span>
                        </div>
                        <div class="note-body">${escapeHtml(note.note)}</div>
                        <button class="btn btn-sm btn-danger mt-2" onclick="deleteNote(${note.id}, ${userId})">
                            <i class="ti ti-trash"></i> Supprimer
                        </button>
                    </div>
                `;
            });
        }

        html += '</div>';
        return html;
    }

    function renderTagsTab(userId, tags) {
        const tagColors = ['primary', 'success', 'danger', 'warning', 'info', 'secondary'];
        
        let html = `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0 fw-bold">Ajouter un tag</label>
                    <button class="btn btn-sm btn-outline-primary" onclick="getAITagRecommendations(${userId})">
                        <i class="ti ti-sparkles"></i> 🤖 Recommandations AI
                    </button>
                </div>
                <div id="aiTagRecommendations" class="mb-3" style="display: none;"></div>
                <div class="input-group">
                    <input type="text" id="newTagName" class="form-control" placeholder="Nom du tag...">
                    <select id="newTagColor" class="form-select" style="max-width: 150px;">
                        ${tagColors.map(color => `<option value="${color}">${color}</option>`).join('')}
                    </select>
                    <button class="btn btn-primary" onclick="addTag(${userId})">
                        <i class="ti ti-plus"></i> Ajouter
                    </button>
                </div>
            </div>
            <div class="tags-list">
        `;

        if (tags.length === 0) {
            html += '<p class="text-muted text-center py-4">Aucun tag pour cet utilisateur</p>';
        } else {
            tags.forEach(tag => {
                html += `
                    <span class="badge bg-${tag.tag_color} me-2 mb-2" style="font-size: 14px; padding: 8px 12px;">
                        ${escapeHtml(tag.tag_name)}
                        <i class="ti ti-x" style="cursor: pointer;" onclick="deleteTag(${tag.id}, ${userId})"></i>
                    </span>
                `;
            });
        }

        html += '</div>';
        return html;
    }

    function renderActivityTab(activities, stats) {
        let html = `
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card-small">
                        <div class="stat-label-small">Total Activités</div>
                        <div class="stat-value-small">${stats.total_activities}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card-small">
                        <div class="stat-label-small">7 Derniers Jours</div>
                        <div class="stat-value-small">${stats.recent_activities}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card-small">
                        <div class="stat-label-small">Plus Fréquent</div>
                        <div class="stat-value-small small">${stats.most_common_activity}</div>
                    </div>
                </div>
            </div>
            <div class="activity-timeline">
        `;

        if (activities.length === 0) {
            html += '<p class="text-muted text-center py-4">Aucune activité enregistrée</p>';
        } else {
            activities.forEach(activity => {
                const date = new Date(activity.created_at);
                const timeAgo = getTimeAgo(date);
                
                html += `
                    <div class="activity-item">
                        <div class="activity-icon">●</div>
                        <div class="activity-content">
                            <div class="activity-type">${escapeHtml(activity.activity_type)}</div>
                            ${activity.activity_description ? `<div class="activity-desc">${escapeHtml(activity.activity_description)}</div>` : ''}
                            <div class="activity-time">${timeAgo}</div>
                        </div>
                    </div>
                `;
            });
        }

        html += '</div>';
        return html;
    }

    function renderEmailsTab(emails) {
        let html = '<div class="emails-list">';

        if (emails.length === 0) {
            html += '<p class="text-muted text-center py-4">Aucun email envoyé</p>';
        } else {
            emails.forEach(email => {
                const statusBadge = email.status === 'sent' ? 
                    '<span class="badge bg-success">Envoyé</span>' : 
                    '<span class="badge bg-danger">Échec</span>';
                
                html += `
                    <div class="email-item">
                        <div class="email-header">
                            <strong>${escapeHtml(email.subject)}</strong>
                            ${statusBadge}
                            ${email.ai_generated == 1 ? '<span class="badge bg-primary ms-2">🤖 AI</span>' : ''}
                        </div>
                        <div class="email-meta">
                            <span class="text-muted small">${new Date(email.sent_at).toLocaleString('fr-FR')}</span>
                            <span class="text-muted small ms-3">Template: ${email.template_name}</span>
                        </div>
                    </div>
                `;
            });
        }

        html += '</div>';
        return html;
    }

    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        
        if (seconds < 60) return 'À l\'instant';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' min';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' h';
        if (seconds < 604800) return Math.floor(seconds / 86400) + ' j';
        return date.toLocaleDateString('fr-FR');
    }

    function closeProfileModal() {
        const modal = document.getElementById('profileModal');
        if (modal) modal.remove();
    }

    // Note functions
    function addNote(userId) {
        const noteText = document.getElementById('newNoteText').value.trim();
        if (!noteText) {
            alert('Veuillez entrer une note');
            return;
        }

        fetch('../../controllers/UserController.php?action=add_user_note', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `user_id=${userId}&note=${encodeURIComponent(noteText)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                viewUserProfile(userId); // Refresh
            } else {
                alert('Erreur: ' + (data.errors ? data.errors.join(', ') : 'Une erreur est survenue'));
            }
        });
    }

    function deleteNote(noteId, userId) {
        if (!confirm('Supprimer cette note?')) return;

        fetch('../../controllers/UserController.php?action=delete_user_note', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `note_id=${noteId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                viewUserProfile(userId); // Refresh
            } else {
                alert('Erreur: ' + (data.errors ? data.errors.join(', ') : 'Une erreur est survenue'));
            }
        });
    }

    // Tag functions
    function addTag(userId) {
        const tagName = document.getElementById('newTagName').value.trim();
        const tagColor = document.getElementById('newTagColor').value;
        
        if (!tagName) {
            alert('Veuillez entrer un nom de tag');
            return;
        }

        fetch('../../controllers/UserController.php?action=add_user_tag', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `user_id=${userId}&tag_name=${encodeURIComponent(tagName)}&tag_color=${tagColor}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                viewUserProfile(userId); // Refresh
            } else {
                alert('Erreur: ' + (data.errors ? data.errors.join(', ') : 'Une erreur est survenue'));
            }
        });
    }

    function deleteTag(tagId, userId) {
        fetch('../../controllers/UserController.php?action=delete_user_tag', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `tag_id=${tagId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                viewUserProfile(userId); // Refresh
            } else {
                alert('Erreur: ' + (data.errors ? data.errors.join(', ') : 'Une erreur est survenue'));
            }
        });
    }

    // ==================== AI-POWERED FUNCTIONS ====================

    // Render AI Insights Tab
    function renderAIInsightsTab(userId) {
        return `
            <div class="ai-insights-container">
                <div class="text-center mb-4">
                    <h5 class="mb-3">🤖 Intelligence Artificielle</h5>
                    <p class="text-muted">Utilisez l'IA pour obtenir des insights approfondis sur cet utilisateur</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <button class="btn btn-primary w-100" onclick="generateUserInsights(${userId})">
                            <i class="ti ti-brain"></i> Générer Insights Complets
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-info w-100" onclick="analyzeUserBehavior(${userId})">
                            <i class="ti ti-chart-line"></i> Analyser Comportement
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-warning w-100" onclick="generateProfileSummary(${userId})">
                            <i class="ti ti-file-text"></i> Résumé du Profil
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary w-100" onclick="getAINoteSuggestions(${userId})">
                            <i class="ti ti-notes"></i> Suggestions de Notes
                        </button>
                    </div>
                </div>

                <div id="aiInsightsResults" class="ai-results-container">
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-sparkles" style="font-size: 48px;"></i>
                        <p class="mt-3">Cliquez sur un bouton ci-dessus pour générer des insights AI</p>
                    </div>
                </div>
            </div>
        `;
    }

    // Generate User Insights
    function generateUserInsights(userId) {
        const resultsDiv = document.getElementById('aiInsightsResults');
        resultsDiv.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Génération des insights AI...</p></div>';

        fetch(`../../controllers/UserController.php?action=generate_user_insights&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                console.log('AI Insights Response:', data);
                
                if (data.success && data.data) {
                    const insights = data.data;
                    resultsDiv.innerHTML = `
                        <div class="ai-result-card">
                            <h5 class="mb-3"><i class="ti ti-brain"></i> Insights Utilisateur</h5>
                            
                            <div class="insight-section">
                                <h6 class="text-primary">📊 Résumé</h6>
                                <p>${insights.summary || 'Aucun résumé disponible'}</p>
                            </div>

                            ${insights.strengths && insights.strengths.length > 0 ? `
                                <div class="insight-section">
                                    <h6 class="text-success">💪 Points Forts</h6>
                                    <ul>
                                        ${insights.strengths.map(s => `<li>${s}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}

                            ${insights.engagement_analysis ? `
                                <div class="insight-section">
                                    <h6 class="text-info">📈 Analyse d'Engagement</h6>
                                    <p>${insights.engagement_analysis}</p>
                                </div>
                            ` : ''}

                            ${insights.recommendations && insights.recommendations.length > 0 ? `
                                <div class="insight-section">
                                    <h6 class="text-warning">💡 Recommandations</h6>
                                    <ul>
                                        ${insights.recommendations.map(r => `<li>${r}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}

                            ${insights.health_insights ? `
                                <div class="insight-section">
                                    <h6 class="text-danger">🏥 Insights Santé</h6>
                                    <p>${insights.health_insights}</p>
                                </div>
                            ` : ''}

                            ${insights.risk_factors && insights.risk_factors.length > 0 ? `
                                <div class="insight-section">
                                    <h6 class="text-danger">⚠️ Facteurs de Risque</h6>
                                    <ul>
                                        ${insights.risk_factors.map(r => `<li>${r}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}

                            ${insights.next_actions && insights.next_actions.length > 0 ? `
                                <div class="insight-section">
                                    <h6 class="text-secondary">🎯 Actions Suivantes</h6>
                                    <ul>
                                        ${insights.next_actions.map(a => `<li>${a}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}
                        </div>
                    `;
                } else {
                    console.error('AI Insights Error:', data);
                    resultsDiv.innerHTML = '<div class="alert alert-danger">Erreur: ' + (data.error || data.message || 'Impossible de générer les insights') + '</div>';
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                resultsDiv.innerHTML = '<div class="alert alert-danger">Erreur: ' + error.message + '</div>';
            });
    }

    // Get AI Note Suggestions
    function getAINoteSuggestions(userId) {
        const suggestionsDiv = document.getElementById('aiNoteSuggestions');
        suggestionsDiv.style.display = 'block';
        suggestionsDiv.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Génération de suggestions...</div>';

        fetch(`../../controllers/UserController.php?action=get_ai_note_suggestions&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const suggestions = data.data;
                    let html = '<div class="alert alert-info"><strong>🤖 Suggestions AI:</strong><ul class="mb-0 mt-2">';
                    suggestions.forEach(suggestion => {
                        html += `<li style="cursor: pointer;" onclick="document.getElementById('newNoteText').value = '${escapeHtml(suggestion).replace(/'/g, "\\'")}'">${suggestion}</li>`;
                    });
                    html += '</ul></div>';
                    suggestionsDiv.innerHTML = html;
                } else {
                    suggestionsDiv.innerHTML = `<div class="alert alert-warning">Aucune suggestion disponible</div>`;
                }
            })
            .catch(error => {
                suggestionsDiv.innerHTML = `<div class="alert alert-danger">Erreur: ${error.message}</div>`;
            });
    }

    // Get AI Tag Recommendations
    function getAITagRecommendations(userId) {
        const recommendationsDiv = document.getElementById('aiTagRecommendations');
        recommendationsDiv.style.display = 'block';
        recommendationsDiv.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Génération de recommandations...</div>';

        fetch(`../../controllers/UserController.php?action=get_ai_tag_recommendations&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const recommendations = data.data;
                    let html = '<div class="alert alert-info"><strong>🤖 Recommandations AI:</strong><div class="mt-2">';
                    recommendations.forEach(rec => {
                        html += `
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <span class="badge bg-${rec.color}">${rec.name}</span>
                                    <small class="text-muted ms-2">${rec.reason}</small>
                                </div>
                                <button class="btn btn-sm btn-primary" onclick="addAITag(${userId}, '${escapeHtml(rec.name).replace(/'/g, "\\'")}', '${rec.color}')">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                        `;
                    });
                    html += '</div></div>';
                    recommendationsDiv.innerHTML = html;
                } else {
                    recommendationsDiv.innerHTML = `<div class="alert alert-warning">Aucune recommandation disponible</div>`;
                }
            })
            .catch(error => {
                recommendationsDiv.innerHTML = `<div class="alert alert-danger">Erreur: ${error.message}</div>`;
            });
    }

    // Add AI-recommended tag
    function addAITag(userId, tagName, tagColor) {
        fetch('../../controllers/UserController.php?action=add_user_tag', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `user_id=${userId}&tag_name=${encodeURIComponent(tagName)}&tag_color=${tagColor}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                viewUserProfile(userId); // Refresh
            } else {
                alert('Erreur: ' + (data.errors ? data.errors.join(', ') : 'Une erreur est survenue'));
            }
        });
    }

    // Analyze User Behavior
    function analyzeUserBehavior(userId) {
        const resultsDiv = document.getElementById('aiInsightsResults');
        resultsDiv.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Analyse du comportement...</p></div>';

        fetch(`../../controllers/UserController.php?action=analyze_user_behavior&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const analysis = data.data;
                    resultsDiv.innerHTML = `
                        <div class="ai-result-card">
                            <h5 class="mb-3"><i class="ti ti-chart-line"></i> Analyse Comportementale</h5>
                            
                            ${analysis.patterns && analysis.patterns.length > 0 ? `
                                <div class="insight-section">
                                    <h6 class="text-primary">🔍 Patterns Détectés</h6>
                                    <ul>
                                        ${analysis.patterns.map(p => `<li>${p}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}

                            ${analysis.insights ? `
                                <div class="insight-section">
                                    <h6 class="text-info">💡 Insights Clés</h6>
                                    <p>${analysis.insights}</p>
                                </div>
                            ` : ''}

                            ${analysis.predictions ? `
                                <div class="insight-section">
                                    <h6 class="text-success">🔮 Prédictions</h6>
                                    <p>${analysis.predictions}</p>
                                </div>
                            ` : ''}

                            ${analysis.concerns ? `
                                <div class="insight-section">
                                    <h6 class="text-warning">⚠️ Points d'Attention</h6>
                                    <p>${analysis.concerns}</p>
                                </div>
                            ` : ''}
                        </div>
                    `;
                } else if (data.success && data.analysis) {
                    resultsDiv.innerHTML = `<div class="alert alert-info">${data.analysis}</div>`;
                } else {
                    resultsDiv.innerHTML = `<div class="alert alert-danger">Erreur: ${data.error || 'Impossible d\'analyser le comportement'}</div>`;
                }
            })
            .catch(error => {
                resultsDiv.innerHTML = `<div class="alert alert-danger">Erreur: ${error.message}</div>`;
            });
    }

    // Generate Health Recommendations
    function generateHealthRecommendations(userId) {
        const resultsDiv = document.getElementById('aiInsightsResults');
        resultsDiv.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Génération des recommandations santé...</p></div>';

        fetch(`../../controllers/UserController.php?action=generate_health_recommendations&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const health = data.data;
                    resultsDiv.innerHTML = `
                        <div class="ai-result-card">
                            <h5 class="mb-3"><i class="ti ti-heart"></i> Recommandations Santé</h5>
                            
                            <div class="insight-section">
                                <h6 class="text-primary">📊 Catégorie IMC</h6>
                                <p><strong>${health.bmi_category}</strong></p>
                            </div>

                            <div class="insight-section">
                                <h6 class="text-info">🏥 État de Santé</h6>
                                <p>${health.health_status}</p>
                            </div>

                            <div class="insight-section">
                                <h6 class="text-success">💡 Recommandations</h6>
                                <ul>
                                    ${health.recommendations.map(r => `<li>${r}</li>`).join('')}
                                </ul>
                            </div>

                            ${health.nutrition_tips && health.nutrition_tips.length > 0 ? `
                                <div class="insight-section">
                                    <h6 class="text-warning">🍎 Conseils Nutrition</h6>
                                    <ul>
                                        ${health.nutrition_tips.map(t => `<li>${t}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}

                            ${health.exercise_suggestions && health.exercise_suggestions.length > 0 ? `
                                <div class="insight-section">
                                    <h6 class="text-primary">🏃 Suggestions d'Exercice</h6>
                                    <ul>
                                        ${health.exercise_suggestions.map(s => `<li>${s}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}

                            ${health.warnings && health.warnings.length > 0 ? `
                                <div class="insight-section">
                                    <h6 class="text-danger">⚠️ Avertissements</h6>
                                    <ul>
                                        ${health.warnings.map(w => `<li>${w}</li>`).join('')}
                                    </ul>
                                </div>
                            ` : ''}
                        </div>
                    `;
                } else {
                    resultsDiv.innerHTML = `<div class="alert alert-danger">Erreur: ${data.error || 'Impossible de générer les recommandations'}</div>`;
                }
            })
            .catch(error => {
                resultsDiv.innerHTML = `<div class="alert alert-danger">Erreur: ${error.message}</div>`;
            });
    }

    // Generate Profile Summary
    function generateProfileSummary(userId) {
        const resultsDiv = document.getElementById('aiInsightsResults');
        resultsDiv.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Génération du résumé...</p></div>';

        fetch(`../../controllers/UserController.php?action=generate_profile_summary&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.summary) {
                    resultsDiv.innerHTML = `
                        <div class="ai-result-card">
                            <h5 class="mb-3"><i class="ti ti-file-text"></i> Résumé du Profil</h5>
                            <div class="alert alert-primary">
                                <p class="mb-0" style="font-size: 16px; line-height: 1.6;">${data.summary}</p>
                            </div>
                        </div>
                    `;
                } else {
                    resultsDiv.innerHTML = `<div class="alert alert-danger">Erreur: ${data.error || 'Impossible de générer le résumé'}</div>`;
                }
            })
            .catch(error => {
                resultsDiv.innerHTML = `<div class="alert alert-danger">Erreur: ${error.message}</div>`;
            });
    }

    // Initialize dropdown toggle
    document.addEventListener('DOMContentLoaded', function() {
        const profileDropdownToggle = document.querySelector('.dropdown button[data-bs-toggle="dropdown"], .dropdown a[data-bs-toggle="dropdown"]');
        if (profileDropdownToggle) {
            profileDropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdown = this.nextElementSibling;
                if (dropdown && dropdown.classList.contains('dropdown-menu')) {
                    dropdown.classList.toggle('show');
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdownMenus = document.querySelectorAll('.dropdown-menu.show');
            dropdownMenus.forEach(menu => {
                const dropdownContainer = menu.closest('.dropdown');
                if (dropdownContainer && !dropdownContainer.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });
        });
    });
  </script>

  <script>
    // Global variables
    let currentPage = 1;
    let currentSearch = '';
    const perPage = 10;

    // Load users with pagination
    function loadUsers(page = 1, search = '') {
      currentPage = page;
      currentSearch = search;
      
      const loadingSpinner = document.getElementById('loadingSpinner');
      const tableBody = document.getElementById('usersTableBody');
      
      loadingSpinner.style.display = 'block';
      tableBody.innerHTML = '';

      fetch(`../../controllers/UserController.php?action=get_users_paginated&page=${page}&per_page=${perPage}&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
          loadingSpinner.style.display = 'none';
          
          if (data.success) {
            renderUsers(data.users);
            renderPagination(data.pagination);
          } else {
            tableBody.innerHTML = `
              <tr>
                <td colspan="5" class="text-center py-4">
                  <p class="text-danger mb-0">Erreur lors du chargement des utilisateurs</p>
                </td>
              </tr>
            `;
          }
        })
        .catch(error => {
          loadingSpinner.style.display = 'none';
          console.error('Error loading users:', error);
          tableBody.innerHTML = `
            <tr>
              <td colspan="5" class="text-center py-4">
                <p class="text-danger mb-0">Erreur lors du chargement des utilisateurs</p>
              </td>
            </tr>
          `;
        });
    }

    // Render users in table
    function renderUsers(users) {
      const tableBody = document.getElementById('usersTableBody');
      
      if (users.length === 0) {
        tableBody.innerHTML = `
          <tr>
            <td colspan="6" class="text-center py-4">
              <p class="text-muted mb-0">Aucun utilisateur trouvé</p>
            </td>
          </tr>
        `;
        return;
      }

      tableBody.innerHTML = users.map(user => {
        const initial = user.nom.charAt(0).toUpperCase();
        const roleClass = user.role.toLowerCase();
        const roleText = user.role.toLowerCase() === 'admin' ? 'Administrateur' : 'Utilisateur';
        const date = new Date(user.date_creation);
        const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        // Status badge and button
        const isBlocked = user.status === 'blocked';
        const statusBadge = isBlocked ? 
          '<span class="badge bg-danger">🔒 Bloqué</span>' : 
          '<span class="badge bg-success">✓ Actif</span>';
        
        const actionButton = isBlocked ?
          `<button class="btn btn-sm btn-success me-2" onclick="unblockUser(${user.id}, '${escapeHtml(user.nom)}')">
            <i class="ti ti-lock-open"></i> Débloquer
          </button>` :
          `<button class="btn btn-sm btn-warning me-2" onclick="blockUser(${user.id}, '${escapeHtml(user.nom)}')">
            <i class="ti ti-lock"></i> Bloquer
          </button>`;
        
        return `
          <tr ${isBlocked ? 'style="opacity: 0.6; background-color: #fff3cd;"' : ''}>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="user-avatar">${initial}</div>
                <div>
                  <p class="mb-0 fw-semi-bold">${escapeHtml(user.nom)}</p>
                </div>
              </div>
            </td>
            <td>${escapeHtml(user.email)}</td>
            <td>
              <span class="role-badge role-${roleClass}">${roleText}</span>
            </td>
            <td>${statusBadge}</td>
            <td>
              <small class="text-muted">${formattedDate}</small>
            </td>
            <td>
              ${actionButton}
              <button class="btn btn-sm btn-info me-2" onclick="viewUserProfile(${user.id})">
                <i class="ti ti-eye"></i> Profil
              </button>
              <button class="btn btn-sm btn-danger" onclick="showDeleteModal(${user.id}, '${escapeHtml(user.nom)}')">
                <i class="ti ti-trash"></i> Supprimer
              </button>
            </td>
          </tr>
        `;
      }).join('');
    }

    // Render pagination controls
    function renderPagination(pagination) {
      const paginationInfo = document.getElementById('paginationInfo');
      const paginationControls = document.getElementById('paginationControls');
      
      const start = (pagination.current_page - 1) * pagination.per_page + 1;
      const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_users);
      
      paginationInfo.textContent = `Affichage ${start}-${end} sur ${pagination.total_users} utilisateurs`;
      
      let paginationHTML = '';
      
      // Previous button
      paginationHTML += `
        <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
          <a class="page-link" href="#" onclick="loadUsers(${pagination.current_page - 1}, currentSearch); return false;">
            Précédent
          </a>
        </li>
      `;
      
      // Page numbers
      const maxVisiblePages = 5;
      let startPage = Math.max(1, pagination.current_page - Math.floor(maxVisiblePages / 2));
      let endPage = Math.min(pagination.total_pages, startPage + maxVisiblePages - 1);
      
      if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
      }
      
      if (startPage > 1) {
        paginationHTML += `
          <li class="page-item">
            <a class="page-link" href="#" onclick="loadUsers(1, currentSearch); return false;">1</a>
          </li>
        `;
        if (startPage > 2) {
          paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
      }
      
      for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
          <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
            <a class="page-link" href="#" onclick="loadUsers(${i}, currentSearch); return false;">${i}</a>
          </li>
        `;
      }
      
      if (endPage < pagination.total_pages) {
        if (endPage < pagination.total_pages - 1) {
          paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        paginationHTML += `
          <li class="page-item">
            <a class="page-link" href="#" onclick="loadUsers(${pagination.total_pages}, currentSearch); return false;">${pagination.total_pages}</a>
          </li>
        `;
      }
      
      // Next button
      paginationHTML += `
        <li class="page-item ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}">
          <a class="page-link" href="#" onclick="loadUsers(${pagination.current_page + 1}, currentSearch); return false;">
            Suivant
          </a>
        </li>
      `;
      
      paginationControls.innerHTML = paginationHTML;
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

    // Search functionality with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function() {
      clearTimeout(searchTimeout);
      const searchTerm = this.value.trim();
      
      searchTimeout = setTimeout(() => {
        loadUsers(1, searchTerm);
      }, 500);
    });

    // PDF Export functionality - exports ALL users matching search
    document.getElementById('exportPdfBtn').addEventListener('click', function() {
      const searchTerm = currentSearch;
      
      // Show loading state
      this.disabled = true;
      this.innerHTML = '<i class="ti ti-loader"></i> Exportation...';
      
      fetch(`../../controllers/UserController.php?action=get_all_users_export&search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            generatePDF(data.users);
          } else {
            alert('Erreur lors de l\'exportation des utilisateurs');
          }
          
          // Reset button state
          this.disabled = false;
          this.innerHTML = '<i class="ti ti-download"></i> Exporter PDF';
        })
        .catch(error => {
          console.error('Export error:', error);
          alert('Une erreur est survenue lors de l\'exportation');
          
          // Reset button state
          this.disabled = false;
          this.innerHTML = '<i class="ti ti-download"></i> Exporter PDF';
        });
    });

    // Generate PDF from all users
    function generatePDF(users) {
      // Count admin and regular users
      const adminCount = users.filter(u => u.role.toLowerCase() === 'admin').length;
      const userCount = users.filter(u => u.role.toLowerCase() === 'user').length;
      
      // Get the logo as base64 to embed in PDF
      const logoImg = new Image();
      logoImg.crossOrigin = 'anonymous';
      logoImg.src = window.location.origin + '/nutrimind/views/assets/img/logooo.png';
      
      logoImg.onload = function() {
        // Convert image to base64
        const canvas = document.createElement('canvas');
        canvas.width = logoImg.width;
        canvas.height = logoImg.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(logoImg, 0, 0);
        const logoBase64 = canvas.toDataURL('image/png');
        
        generatePDFWithLogo(users, adminCount, userCount, logoBase64);
      };
      
      logoImg.onerror = function() {
        // If logo fails to load, generate PDF without it
        console.warn('Logo failed to load, generating PDF without logo');
        generatePDFWithLogo(users, adminCount, userCount, null);
      };
    }
    
    function generatePDFWithLogo(users, adminCount, userCount, logoBase64) {
      let tableHTML = `
        <table style="width: 100%; border-collapse: collapse; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
          <thead>
            <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
              <th style="padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; letter-spacing: 0.5px; border-right: 1px solid rgba(255,255,255,0.2); width: 25%;">UTILISATEUR</th>
              <th style="padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; letter-spacing: 0.5px; border-right: 1px solid rgba(255,255,255,0.2); width: 35%;">COURRIER ÉLECTRONIQUE</th>
              <th style="padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; letter-spacing: 0.5px; border-right: 1px solid rgba(255,255,255,0.2); width: 20%;">RÔLE</th>
              <th style="padding: 16px 12px; text-align: left; font-weight: 600; font-size: 13px; letter-spacing: 0.5px; width: 20%;">DATE D'INSCRIPTION</th>
            </tr>
          </thead>
          <tbody>
      `;

      users.forEach((user, index) => {
        const roleText = user.role.toLowerCase() === 'admin' ? 'Administrateur' : 'Utilisateur';
        const date = new Date(user.date_creation);
        const formattedDate = date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
        const rowBg = index % 2 === 0 ? '#ffffff' : '#f8f9fa';
        const roleColor = user.role.toLowerCase() === 'admin' ? '#dc3545' : '#28a745';
        const roleBg = user.role.toLowerCase() === 'admin' ? '#ffe6e6' : '#e6f7e6';
        
        tableHTML += `
          <tr style="background-color: ${rowBg};">
            <td style="padding: 14px 12px; border-bottom: 1px solid #e9ecef; font-weight: 500; color: #2c3e50; font-size: 12px;">
              <div style="display: flex; align-items: center;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 13px; margin-right: 10px;">
                  ${escapeHtml(user.nom.charAt(0).toUpperCase())}
                </div>
                <span>${escapeHtml(user.nom)}</span>
              </div>
            </td>
            <td style="padding: 14px 12px; border-bottom: 1px solid #e9ecef; color: #5a6c7d; font-size: 12px;">${escapeHtml(user.email)}</td>
            <td style="padding: 14px 12px; border-bottom: 1px solid #e9ecef;">
              <span style="display: inline-block; padding: 6px 12px; border-radius: 20px; background-color: ${roleBg}; color: ${roleColor}; font-weight: 600; font-size: 11px; letter-spacing: 0.3px;">
                ${roleText}
              </span>
            </td>
            <td style="padding: 14px 12px; border-bottom: 1px solid #e9ecef; color: #7f8c8d; font-size: 11px;">${formattedDate}</td>
          </tr>
        `;
      });

      tableHTML += `
          </tbody>
        </table>
      `;

      const logoHTML = logoBase64 ? `<img src="${logoBase64}" alt="Nutrimind Logo" style="max-height: 80px; width: auto; margin-bottom: 15px; filter: brightness(0) invert(1);" />` : '';

      const element = document.createElement('div');
      element.innerHTML = `
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px 40px; margin: -20px -20px 30px -20px; border-radius: 0 0 20px 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
          <div style="text-align: center;">
            ${logoHTML}
            <h1 style="color: white; font-size: 32px; margin: 10px 0 5px 0; font-weight: 700; letter-spacing: 1px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">NUTRIMIND</h1>
            <h2 style="color: rgba(255,255,255,0.95); font-size: 20px; margin: 5px 0 15px 0; font-weight: 400; letter-spacing: 0.5px;">Liste des Utilisateurs</h2>
            <div style="display: inline-block; background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 25px;">
              <p style="color: white; font-size: 11px; margin: 0; font-weight: 500;">
                📅 Généré le ${new Date().toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' })} à ${new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}
              </p>
            </div>
          </div>
        </div>
        
        <div style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 20px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
          <div style="display: flex; justify-content: space-around; text-align: center;">
            <div style="flex: 1; padding: 10px;">
              <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: 700; color: #667eea; margin-bottom: 5px;">${users.length}</div>
                <div style="font-size: 11px; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Utilisateurs</div>
              </div>
            </div>
            <div style="flex: 1; padding: 10px;">
              <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: 700; color: #dc3545; margin-bottom: 5px;">${adminCount}</div>
                <div style="font-size: 11px; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Administrateurs</div>
              </div>
            </div>
            <div style="flex: 1; padding: 10px;">
              <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: 700; color: #28a745; margin-bottom: 5px;">${userCount}</div>
                <div style="font-size: 11px; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Utilisateurs</div>
              </div>
            </div>
          </div>
        </div>
        
        <div style="margin: 25px 0; border-radius: 12px; overflow: hidden;">
          ${tableHTML}
        </div>
        
        <div style="text-align: center; margin-top: 40px; padding-top: 25px; border-top: 2px solid #e9ecef;">
          <div style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 30px; border-radius: 30px; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
            <p style="margin: 0; color: white; font-size: 11px; font-weight: 600; letter-spacing: 0.5px;">
              © 2024 NUTRIMIND - Tous droits réservés | Système de Gestion Nutritionnelle
            </p>
          </div>
          <p style="margin: 15px 0 0 0; color: #95a5a6; font-size: 10px; font-style: italic;">
            Document confidentiel - Usage interne uniquement
          </p>
        </div>
      `;

      const opt = {
        margin: [0.3, 0.3, 0.3, 0.3],
        filename: 'Nutrimind_Utilisateurs_' + new Date().toLocaleDateString('fr-FR').replace(/\//g, '-') + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
          scale: 2, 
          useCORS: true, 
          logging: false,
          letterRendering: true,
          allowTaint: true
        },
        jsPDF: { 
          unit: 'in', 
          format: 'a4', 
          orientation: 'landscape',
          compress: true
        }
      };

      html2pdf().set(opt).from(element).save();
    }

    // Load initial users on page load
    document.addEventListener('DOMContentLoaded', function() {
      loadUsers(1, '');
      loadSegmentCounts();
    });

    // ============================================
    // AI EMAIL CAMPAIGN SYSTEM
    // ============================================

    let userSegments = {};
    let selectedSegmentUsers = [];
    let selectedUserIds = [];
    let generatedEmails = [];

    // Toggle campaign section
    document.getElementById('toggleCampaignBtn').addEventListener('click', function() {
      const section = document.getElementById('campaignSection');
      const icon = this.querySelector('i');
      
      if (section.style.display === 'none') {
        section.style.display = 'block';
        icon.classList.remove('ti-chevron-down');
        icon.classList.add('ti-chevron-up');
      } else {
        section.style.display = 'none';
        icon.classList.remove('ti-chevron-up');
        icon.classList.add('ti-chevron-down');
      }
    });

    // Load segment counts
    function loadSegmentCounts() {
      fetch('../../controllers/UserController.php?action=get_user_segments')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            userSegments = data.segments;
            updateSegmentInfo();
          }
        })
        .catch(error => console.error('Error loading segments:', error));
    }

    // Update segment info when selection changes
    document.getElementById('segmentSelect').addEventListener('change', function() {
      updateSegmentInfo();
    });

    function updateSegmentInfo() {
      const segmentType = document.getElementById('segmentSelect').value;
      const infoDiv = document.getElementById('segmentInfo');
      
      if (!segmentType) {
        infoDiv.innerHTML = '';
        return;
      }
      
      if (segmentType === 'all') {
        infoDiv.innerHTML = `<span class="badge bg-primary">All users will be shown</span>`;
        return;
      }
      
      const count = userSegments[segmentType] ? userSegments[segmentType].length : 0;
      infoDiv.innerHTML = `<span class="badge bg-info">${count} users in this segment</span>`;
    }

    // Generate AI Emails - Now shows user selection first
    document.getElementById('generateAIEmailBtn').addEventListener('click', function() {
      const segmentType = document.getElementById('segmentSelect').value;
      
      if (!segmentType) {
        alert('Please select a user segment');
        return;
      }
      
      // Get users from selected segment
      if (segmentType === 'all') {
        // Get all users
        fetch(`../../controllers/UserController.php?action=get_all_users_export`)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              selectedSegmentUsers = data.users;
              displayUserSelection(selectedSegmentUsers);
            }
          });
      } else {
        selectedSegmentUsers = userSegments[segmentType] || [];
        if (selectedSegmentUsers.length === 0) {
          alert('No users in this segment');
          return;
        }
        displayUserSelection(selectedSegmentUsers);
      }
    });

    // Display user selection interface
    function displayUserSelection(users) {
      const container = document.getElementById('userSelectionContainer');
      const section = document.getElementById('userSelectionSection');
      
      selectedUserIds = []; // Reset selection
      updateSelectedCount();
      
      if (users.length === 0) {
        container.innerHTML = '<p class="text-muted text-center mb-0">No users found in this segment</p>';
        section.style.display = 'block';
        return;
      }
      
      let html = '<div class="row g-2">';
      
      users.forEach(user => {
        const initial = user.nom.charAt(0).toUpperCase();
        const roleClass = user.role.toLowerCase();
        const roleText = user.role.toLowerCase() === 'admin' ? 'Admin' : 'User';
        const date = new Date(user.date_creation);
        const formattedDate = date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
        
        html += `
          <div class="col-12">
            <div class="user-select-card" data-user-id="${user.id}">
              <input type="checkbox" class="user-checkbox" id="user_${user.id}" value="${user.id}">
              <label for="user_${user.id}" class="user-select-label">
                <div class="d-flex align-items-center gap-3 w-100">
                  <div class="user-avatar-small">${initial}</div>
                  <div class="flex-grow-1">
                    <div class="fw-bold">${escapeHtml(user.nom)}</div>
                    <div class="small text-muted">${escapeHtml(user.email)}</div>
                  </div>
                  <div class="text-end">
                    <span class="role-badge role-${roleClass} small">${roleText}</span>
                    <div class="small text-muted">${formattedDate}</div>
                  </div>
                </div>
              </label>
            </div>
          </div>
        `;
      });
      
      html += '</div>';
      
      container.innerHTML = html;
      section.style.display = 'block';
      
      // Add event listeners to checkboxes
      document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
          const userId = parseInt(this.value);
          if (this.checked) {
            if (!selectedUserIds.includes(userId)) {
              selectedUserIds.push(userId);
            }
          } else {
            selectedUserIds = selectedUserIds.filter(id => id !== userId);
          }
          updateSelectedCount();
        });
      });
      
      // Scroll to user selection
      section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Select all users
    document.getElementById('selectAllUsersBtn').addEventListener('click', function() {
      document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = true;
        const userId = parseInt(checkbox.value);
        if (!selectedUserIds.includes(userId)) {
          selectedUserIds.push(userId);
        }
      });
      updateSelectedCount();
    });

    // Deselect all users
    document.getElementById('deselectAllUsersBtn').addEventListener('click', function() {
      document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = false;
      });
      selectedUserIds = [];
      updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
      const countBadge = document.getElementById('selectedUserCount');
      const proceedBtn = document.getElementById('proceedToGenerateBtn');
      
      countBadge.textContent = `${selectedUserIds.length} selected`;
      proceedBtn.disabled = selectedUserIds.length === 0;
    }

    // Proceed to generate AI emails for selected users
    document.getElementById('proceedToGenerateBtn').addEventListener('click', function() {
      const templateType = document.getElementById('templateSelect').value;
      const tone = document.getElementById('toneSelect').value;
      
      if (selectedUserIds.length === 0) {
        alert('Please select at least one user');
        return;
      }
      
      // Get selected users data
      const selectedUsers = selectedSegmentUsers.filter(u => selectedUserIds.includes(u.id));
      generateAIEmailsForUsers(selectedUsers, templateType, tone);
    });

    function generateAIEmailsForUsers(users, templateType, tone) {
      const btn = document.getElementById('proceedToGenerateBtn');
      btn.disabled = true;
      btn.innerHTML = '<i class="ti ti-loader"></i> Generating AI Emails...';
      
      const userIds = users.map(u => u.id);
      
      // Create FormData for better handling
      const formData = new FormData();
      formData.append('user_ids', JSON.stringify(userIds));
      formData.append('template_type', templateType);
      formData.append('tone', tone);
      
      fetch('../../controllers/UserController.php?action=generate_bulk_ai_emails', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-sparkles"></i> Generate AI Emails for Selected Users';
        
        if (data.success) {
          generatedEmails = data.emails;
          displayEmailPreviews(generatedEmails);
        } else {
          alert('Error generating emails: ' + (data.errors ? data.errors.join(', ') : 'Unknown error'));
        }
      })
      .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-sparkles"></i> Generate AI Emails for Selected Users';
        console.error('Error:', error);
        alert('An error occurred while generating emails');
      });
    }

    function displayEmailPreviews(emails) {
      const container = document.getElementById('emailPreviewContainer');
      const section = document.getElementById('emailPreviewSection');
      
      let html = '';
      
      emails.forEach((emailData, index) => {
        const email = emailData.email_data;
        const success = email.success;
        
        html += `
          <div class="email-preview-card mb-4">
            <div class="email-preview-header">
              <div class="d-flex align-items-center gap-2">
                <div class="user-avatar-small">${emailData.user_name.charAt(0).toUpperCase()}</div>
                <div>
                  <strong>${escapeHtml(emailData.user_name)}</strong>
                  <div class="small text-muted">${escapeHtml(emailData.user_email)}</div>
                </div>
              </div>
              ${success ? `
                <span class="badge bg-success">✓ Generated</span>
              ` : `
                <span class="badge bg-danger">✗ Failed</span>
              `}
            </div>
            
            ${success ? `
              <div class="email-preview-body">
                <div class="mb-3">
                  <label class="form-label fw-bold small text-muted">SUBJECT</label>
                  <div class="email-subject-display">${escapeHtml(email.subject)}</div>
                </div>
                
                <div class="mb-3">
                  <label class="form-label fw-bold small text-muted">EMAIL CONTENT</label>
                  <div class="email-body-display">
                    ${email.body}
                  </div>
                </div>
              </div>
              
              <div class="email-preview-footer">
                <button class="btn btn-outline-secondary btn-sm" onclick="regenerateEmail(${index})">
                  <i class="ti ti-refresh"></i> Regenerate
                </button>
                <button class="btn btn-success btn-sm" onclick="sendSingleEmail(${index})">
                  <i class="ti ti-send"></i> Send Email
                </button>
              </div>
            ` : `
              <div class="email-preview-body">
                <div class="alert alert-danger mb-3">
                  <strong>❌ Generation Failed</strong><br>
                  <span class="small">${email.error || 'Unknown error'}</span>
                  ${email.http_code ? `<br><span class="small text-muted">HTTP Code: ${email.http_code}</span>` : ''}
                </div>
                ${email.response ? `
                  <details class="small">
                    <summary class="text-muted" style="cursor: pointer;">View API Response</summary>
                    <pre class="mt-2 p-2 bg-light border rounded" style="max-height: 200px; overflow-y: auto;">${escapeHtml(typeof email.response === 'string' ? email.response : JSON.stringify(email.response, null, 2))}</pre>
                  </details>
                ` : ''}
                ${email.raw_content ? `
                  <details class="small mt-2">
                    <summary class="text-muted" style="cursor: pointer;">View Raw Content</summary>
                    <pre class="mt-2 p-2 bg-light border rounded" style="max-height: 200px; overflow-y: auto;">${escapeHtml(email.raw_content)}</pre>
                  </details>
                ` : ''}
              </div>
              
              <div class="email-preview-footer">
                <button class="btn btn-primary btn-sm" onclick="regenerateEmail(${index})">
                  <i class="ti ti-refresh"></i> Try Again
                </button>
              </div>
            `}
          </div>
        `;
      });
      
      // Add bulk send button at the bottom
      const successCount = emails.filter(e => e.email_data.success).length;
      if (successCount > 0) {
        html += `
          <div class="text-center mt-4 pt-3 border-top">
            <button class="btn btn-primary btn-lg" onclick="sendAllEmails()">
              <i class="ti ti-send"></i> Send All Emails (${successCount})
            </button>
          </div>
        `;
      }
      
      container.innerHTML = html;
      section.style.display = 'block';
      
      // Scroll to preview
      section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Regenerate email for a specific user
    function regenerateEmail(index) {
      const emailData = generatedEmails[index];
      const templateType = document.getElementById('templateSelect').value;
      const tone = document.getElementById('toneSelect').value;
      
      const btn = event.target.closest('button');
      const originalHTML = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="ti ti-loader"></i> Regenerating...';
      
      // Create FormData
      const formData = new FormData();
      formData.append('user_ids', JSON.stringify([emailData.user_id]));
      formData.append('template_type', templateType);
      formData.append('tone', tone);
      
      fetch('../../controllers/UserController.php?action=generate_bulk_ai_emails', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success && data.emails.length > 0) {
          // Update the email in the array
          generatedEmails[index] = data.emails[0];
          // Refresh the display
          displayEmailPreviews(generatedEmails);
        } else {
          btn.disabled = false;
          btn.innerHTML = originalHTML;
          alert('Error regenerating email: ' + (data.errors ? data.errors.join(', ') : 'Unknown error'));
        }
      })
      .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        console.error('Error:', error);
        alert('An error occurred while regenerating email');
      });
    }

    function sendSingleEmail(index) {
      const emailData = generatedEmails[index];
      const subject = emailData.email_data.subject;
      const body = emailData.email_data.body;
      
      if (!confirm(`Envoyer l'email à ${emailData.user_name} (${emailData.user_email})?`)) {
        return;
      }
      
      const btn = event.target.closest('button');
      const originalHTML = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="ti ti-loader"></i> Envoi en cours...';
      
      fetch('../../controllers/UserController.php?action=send_email_campaign', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user_id=${emailData.user_id}&subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}&template_name=ai_generated&ai_generated=1`
      })
      .then(response => response.json())
      .then(data => {
        console.log('Server response:', data); // Debug log
        
        if (data.success) {
          // Success notification
          btn.innerHTML = '<i class="ti ti-check"></i> Envoyé!';
          btn.classList.remove('btn-success');
          btn.classList.add('btn-secondary');
          btn.disabled = true;
          
          // Show success notification
          showNotification('✅ Email envoyé avec succès!', `L'email a été envoyé à ${emailData.user_name} (${emailData.user_email})`, 'success');
        } else {
          btn.disabled = false;
          btn.innerHTML = originalHTML;
          console.error('Error response:', data); // Debug log
          showNotification('❌ Erreur', data.message || (data.errors ? data.errors.join(', ') : 'Erreur lors de l\'envoi'), 'error');
        }
      })
      .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        console.error('Fetch error:', error);
        showNotification('❌ Erreur', 'Une erreur s\'est produite lors de l\'envoi de l\'email', 'error');
      });
    }

    // Notification function
    function showNotification(title, message, type = 'success') {
      // Create notification element
      const notification = document.createElement('div');
      notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        max-width: 400px;
        background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
        color: ${type === 'success' ? '#155724' : '#721c24'};
        border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
      `;
      
      notification.innerHTML = `
        <div style="display: flex; align-items: start; gap: 10px;">
          <div style="flex: 1;">
            <strong style="display: block; margin-bottom: 5px; font-size: 14px;">${title}</strong>
            <p style="margin: 0; font-size: 13px; line-height: 1.4;">${message}</p>
          </div>
          <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: inherit; padding: 0; line-height: 1;">×</button>
        </div>
      `;
      
      // Add animation
      const style = document.createElement('style');
      style.textContent = `
        @keyframes slideIn {
          from {
            transform: translateX(400px);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }
        @keyframes slideOut {
          from {
            transform: translateX(0);
            opacity: 1;
          }
          to {
            transform: translateX(400px);
            opacity: 0;
          }
        }
      `;
      document.head.appendChild(style);
      
      document.body.appendChild(notification);
      
      // Auto remove after 5 seconds
      setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
      }, 5000);
    }

    function sendAllEmails() {
      const successEmails = generatedEmails.filter(e => e.email_data.success);
      
      if (!confirm(`Envoyer ${successEmails.length} emails personnalisés?`)) {
        return;
      }
      
      const btn = event.target;
      btn.disabled = true;
      btn.innerHTML = '<i class="ti ti-loader"></i> Envoi en cours...';
      
      fetch('../../controllers/UserController.php?action=send_personalized_bulk_campaign', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email_data=${encodeURIComponent(JSON.stringify(successEmails))}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const successCount = data.results.filter(r => r.success).length;
          const failedCount = data.results.length - successCount;
          
          btn.innerHTML = '<i class="ti ti-check"></i> Tous envoyés!';
          btn.classList.add('btn-secondary');
          
          // Show success notification
          showNotification(
            '✅ Emails envoyés!', 
            `${successCount} email(s) envoyé(s) avec succès${failedCount > 0 ? `, ${failedCount} échec(s)` : ''}`, 
            'success'
          );
          
          // Reload page after 3 seconds
          setTimeout(() => {
            location.reload();
          }, 3000);
        } else {
          btn.disabled = false;
          btn.innerHTML = '<i class="ti ti-send"></i> Envoyer tous les emails';
          showNotification('❌ Erreur', data.message || (data.errors ? data.errors.join(', ') : 'Erreur lors de l\'envoi'), 'error');
        }
      })
      .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-send"></i> Envoyer tous les emails';
        console.error('Error:', error);
        showNotification('❌ Erreur', 'Une erreur s\'est produite lors de l\'envoi des emails', 'error');
      });
    }

    // View campaign history
    document.getElementById('viewHistoryBtn').addEventListener('click', function() {
      fetch('../../controllers/UserController.php?action=get_campaign_history&limit=20')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            displayCampaignHistory(data.history);
          }
        })
        .catch(error => console.error('Error loading history:', error));
    });

    function displayCampaignHistory(history) {
      const modal = document.createElement('div');
      modal.className = 'campaign-history-modal-overlay';
      
      let historyHTML = '';
      if (history.length === 0) {
        historyHTML = '<p class="text-muted text-center">No campaign history yet</p>';
      } else {
        historyHTML = '<div class="table-responsive"><table class="table table-sm table-hover">';
        historyHTML += '<thead><tr><th>Date</th><th>User</th><th>Subject</th><th>Status</th><th>AI</th></tr></thead><tbody>';
        
        history.forEach(item => {
          const date = new Date(item.sent_at);
          const formattedDate = date.toLocaleString('fr-FR');
          const statusBadge = item.status === 'sent' ? 
            '<span class="badge bg-success">Sent</span>' : 
            '<span class="badge bg-danger">Failed</span>';
          const aiBadge = item.ai_generated == 1 ? 
            '<span class="badge bg-primary">🤖 AI</span>' : 
            '<span class="badge bg-secondary">Manual</span>';
          
          historyHTML += `
            <tr>
              <td><small>${formattedDate}</small></td>
              <td><small>${escapeHtml(item.nom)}</small></td>
              <td><small>${escapeHtml(item.subject)}</small></td>
              <td>${statusBadge}</td>
              <td>${aiBadge}</td>
            </tr>
          `;
        });
        
        historyHTML += '</tbody></table></div>';
      }
      
      modal.innerHTML = `
        <div class="campaign-history-modal">
          <div class="campaign-history-modal-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4>📊 Campaign History</h4>
              <button class="btn btn-sm btn-light" onclick="this.closest('.campaign-history-modal-overlay').remove()">
                <i class="ti ti-x"></i>
              </button>
            </div>
            ${historyHTML}
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
    }
  </script>

  <style>
    .campaign-history-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .campaign-history-modal {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        max-width: 800px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .campaign-history-modal-content {
        padding: 30px;
    }

    .bg-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    /* User Selection Cards */
    .user-select-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .user-select-card:hover {
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
    }

    .user-select-card input[type="checkbox"] {
        display: none;
    }

    .user-select-card input[type="checkbox"]:checked + .user-select-label {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    }

    .user-select-card input[type="checkbox"]:checked ~ .user-select-label::before {
        content: '✓';
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: #667eea;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }

    .user-select-label {
        cursor: pointer;
        margin: 0;
        padding: 8px;
        border-radius: 6px;
        transition: all 0.2s ease;
        display: block;
        position: relative;
    }

    .user-select-label:hover {
        background: rgba(102, 126, 234, 0.05);
    }

    .user-avatar-small {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
        flex-shrink: 0;
    }

    /* Email Preview Cards */
    .email-preview-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .email-preview-card:hover {
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
        border-color: #667eea;
    }

    .email-preview-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #dee2e6;
    }

    .email-preview-body {
        padding: 25px;
    }

    .email-subject-display {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        padding: 12px 16px;
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        border-radius: 6px;
    }

    .email-body-display {
        padding: 20px;
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        line-height: 1.8;
        color: #495057;
        max-height: 400px;
        overflow-y: auto;
    }

    .email-body-display p {
        margin-bottom: 12px;
    }

    .email-body-display p:last-child {
        margin-bottom: 0;
    }

    .email-preview-footer {
        padding: 20px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .email-preview-footer .btn {
        min-width: 120px;
    }
  </style>

  <style>
    .logout-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .logout-modal {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease-out;
    }
    
    .logout-modal-content {
        padding: 30px;
        min-width: 400px;
        text-align: center;
    }
    
    .logout-modal-content h3 {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 22px;
    }
    
    .logout-modal-content p {
        margin: 0 0 30px 0;
        color: #666;
        font-size: 16px;
    }
    
    .logout-modal-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    
    .logout-btn-cancel, .logout-btn-confirm {
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .logout-btn-cancel {
        background-color: #e0e0e0;
        color: #333;
    }
    
    .logout-btn-cancel:hover {
        background-color: #d0d0d0;
    }
    
    .logout-btn-confirm {
        background-color: #dc3545;
        color: white;
    }
    
    .logout-btn-confirm:hover {
        background-color: #c82333;
    }

    /* Dropdown menu positioning to keep it on screen */
    .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        margin-top: 0.5rem !important;
        z-index: 1050 !important;
        max-height: 90vh;
        overflow-y: auto;
    }

    .dropdown-menu.show {
        display: block !important;
    }

    .dropdown {
        position: relative;
    }

    /* Block/Unblock Modal */
    .block-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .block-modal {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease-out;
    }
    
    .block-modal-content {
        padding: 30px;
        min-width: 400px;
        text-align: center;
    }
    
    .block-modal-content h3 {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 22px;
    }
    
    .block-modal-content p {
        margin: 0 0 10px 0;
        color: #666;
        font-size: 16px;
    }
    
    .block-modal-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 30px;
    }
    
    .block-btn-cancel, .block-btn-confirm, .block-btn-confirm-success {
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .block-btn-cancel {
        background-color: #e0e0e0;
        color: #333;
    }
    
    .block-btn-cancel:hover {
        background-color: #d0d0d0;
    }
    
    .block-btn-confirm {
        background-color: #ffc107;
        color: #333;
    }
    
    .block-btn-confirm:hover {
        background-color: #ffb300;
    }

    .block-btn-confirm-success {
        background-color: #28a745;
        color: white;
    }
    
    .block-btn-confirm-success:hover {
        background-color: #218838;
    }

    /* User Profile Dashboard Modal */
    .profile-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10001;
        overflow-y: auto;
        padding: 20px;
    }

    .profile-modal {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 50px rgba(0, 0, 0, 0.3);
        max-width: 1000px;
        width: 100%;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .profile-modal-header {
        padding: 25px 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .profile-modal-header h3 {
        margin: 0;
        font-size: 24px;
    }

    .btn-close-modal {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .btn-close-modal:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .user-avatar-large {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 32px;
    }

    .profile-modal-body {
        padding: 30px;
        overflow-y: auto;
        flex: 1;
    }

    .stat-card {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 20px;
        border-radius: 10px;
        text-align: center;
    }

    .stat-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }

    .stat-card-small {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }

    .stat-label-small {
        font-size: 11px;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .stat-value-small {
        font-size: 20px;
        font-weight: bold;
        color: #667eea;
    }

    .nav-tabs .nav-link {
        color: #666;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 20px;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: #667eea;
    }

    .nav-tabs .nav-link.active {
        color: #667eea;
        border-bottom-color: #667eea;
        background: none;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .info-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .info-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .info-value {
        font-size: 16px;
        color: #333;
        font-weight: 500;
    }

    .notes-list, .emails-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .note-item, .email-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #667eea;
    }

    .note-header, .email-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .note-body {
        color: #555;
        line-height: 1.6;
    }

    .email-meta {
        margin-top: 8px;
    }

    .tags-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .activity-timeline {
        max-height: 400px;
        overflow-y: auto;
    }

    .activity-item {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        color: #667eea;
        font-size: 20px;
        font-weight: bold;
    }

    .activity-content {
        flex: 1;
    }

    .activity-type {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .activity-desc {
        color: #666;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .activity-time {
        color: #999;
        font-size: 12px;
    }
  </style>

  <style>
    .delete-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .delete-modal {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease-out;
    }
    
    .delete-modal-content {
        padding: 30px;
        min-width: 400px;
        text-align: center;
    }
    
    .delete-modal-content h3 {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 22px;
    }
    
    .delete-modal-content p {
        margin: 0 0 10px 0;
        color: #666;
        font-size: 16px;
    }
    
    .delete-modal-content p.text-danger {
        margin-bottom: 30px;
    }
    
    .delete-modal-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    
    .delete-btn-cancel, .delete-btn-confirm {
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .delete-btn-cancel {
        background-color: #e0e0e0;
        color: #333;
    }
    
    .delete-btn-cancel:hover {
        background-color: #d0d0d0;
    }
    
    .delete-btn-confirm {
        background-color: #dc3545;
        color: white;
    }
    
    .delete-btn-confirm:hover {
        background-color: #c82333;
    }

    @keyframes slideIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
  </style>

  <style>
    /* AI Insights Styles */
    .ai-insights-container {
        padding: 20px;
    }

    .ai-results-container {
        min-height: 300px;
    }

    .ai-result-card {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .insight-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #667eea;
    }

    .insight-section h6 {
        margin-bottom: 15px;
        font-weight: 600;
    }

    .insight-section ul {
        margin-bottom: 0;
        padding-left: 20px;
    }

    .insight-section li {
        margin-bottom: 8px;
        line-height: 1.6;
    }

    .insight-section p {
        margin-bottom: 0;
        line-height: 1.6;
        color: #495057;
    }

    /* AI Button Styles */
    .btn-primary:hover, .btn-success:hover, .btn-info:hover, .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }

    /* AI Suggestions/Recommendations */
    #aiNoteSuggestions li:hover,
    #aiTagRecommendations .border:hover {
        background: rgba(102, 126, 234, 0.05);
        cursor: pointer;
    }

    /* Sparkle animation for AI tab */
    @keyframes sparkle {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .nav-link:has(.ti-sparkles):hover .ti-sparkles {
        animation: sparkle 1s ease-in-out infinite;
    }

    /* Loading spinner in AI results */
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    /* AI Badge */
    .badge.bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
  </style>

</body>
</html>
