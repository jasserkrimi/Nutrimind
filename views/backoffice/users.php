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
            <td colspan="5" class="text-center py-4">
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
        
        return `
          <tr>
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
            <td>
              <small class="text-muted">${formattedDate}</small>
            </td>
            <td>
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
    });
  </script>

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

</body>
</html>
