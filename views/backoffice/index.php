<?php

session_start();



// Check if user is admin

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || 

    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {

    // Redirect non-admin users to home page

    header('Location: ../index.php');

    exit;

}



// Include controllers

require_once '../../controllers/MealController.php';

require_once '../../controllers/IngredientController.php';

require_once __DIR__ . '/../../controllers/UserController.php';

require_once __DIR__ . '/../../models/Objectif.php';



// Initialize controllers

$mealController = new MealController();

$ingredientController = new IngredientController();

$userController = new UserController();



// Get data

$meals = $mealController->getAll();

$ingredients = $ingredientController->getAll();



// Check for new objectives

$newObjectives = [];

if (isset($_SESSION['user_id'])) {

    if (empty($_SESSION['last_login'])) {

        $profile = $userController->getProfile();

        $_SESSION['last_login'] = $profile['last_login'] ?? null;

    }



    if (!empty($_SESSION['last_login'])) {

        $objectifModel = new Objectif();

        $newObjectives = $objectifModel->getAddedAfter($_SESSION['last_login']);

    }

}



// Handle delete operations

if (isset($_GET['delete_meal'])) {

    $deleteId = htmlspecialchars($_GET['delete_meal']);

    if ($mealController->delete($deleteId)) {

        $_SESSION['success_message'] = "Repas supprimé avec succès!";

        header('Location: index.php');

        exit;

    } else {

        $_SESSION['error_message'] = "Erreur lors de la suppression du repas!";

    } 

}
 


if (isset($_GET['delete_ingredient'])) {

    $deleteId = htmlspecialchars($_GET['delete_ingredient']);

    if ($ingredientController->delete($deleteId)) {

        $_SESSION['success_message'] = "Ingrédient supprimé avec succès!";

        header('Location: index.php');

        exit;

    } else {

        $_SESSION['error_message'] = "Erreur lors de la suppression de l'ingrédient!";

    }

}

?>

    }

}

?>

<!DOCTYPE html>

<html lang="en">





<!-- Mirrored from themewagon.github.io/inapp/ by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 09 Apr 2026 13:10:19 GMT -->

<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<head>

  <meta charset="UTF-8" />

  <title>InApp Inventory Dashboard</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/logooo.png">

  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logooo.png">

  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/logooo.png">

  <link rel="manifest" href="assets/site.webmanifest">



  <script type="module" crossorigin src="assets/js/main.js"></script>

  <link rel="stylesheet" crossorigin href="assets/css/main.css">

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

            aria-expanded="false" href="#" role="button">

            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"

              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"

              class="icon icon-tabler icons-tabler-outline icon-tabler-bell">

              <path stroke="none" d="M0 0h24v24H0z" fill="none" />

              <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />

              <path d="M9 17v1a3 3 0 0 0 6 0v-1" />

            </svg>

            <?php if (!empty($newObjectives)): ?>

              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger mt-2 ms-n2">

                <?php echo count($newObjectives); ?>

                <span class="visually-hidden">new objectives</span>

              </span>

            <?php endif; ?>

          </a>

          <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-0">

            <ul class="list-unstyled p-0 m-0">

              <?php if (!empty($newObjectives)): ?>

                <li class="p-3 border-bottom ">

                  <div class="d-flex gap-3">

                    <div class="avatar avatar-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center">

                      <span><?php echo count($newObjectives); ?></span>

                    </div>

                    <div class="flex-grow-1 small">

                      <p class="mb-0 fw-bold"><?php echo count($newObjectives); ?> new objective<?php echo count($newObjectives) > 1 ? 's' : ''; ?></p>

                      <p class="mb-1">Added since your last login.</p>

                    </div>

                  </div>

                </li>

              <?php endif; ?>

              <li class="p-3 border-bottom ">

                <div class="d-flex gap-3">

                  <img src="assets/images/avatar-1.jpg" alt="" class="avatar avatar-sm rounded-circle" />

                  <div class="flex-grow-1 small">

                    <p class="mb-0">New order received</p>

                    <p class="mb-1">Order #12345 has been placed</p>

                    <div class="text-secondary">5 minutes ago</div>

                  </div>

                </div>

              </li>

              <li class="p-3 border-bottom ">

                <div class="d-flex gap-3">

                  <img src="assets/images/avatar-4.jpg" alt="" class="avatar avatar-sm rounded-circle" />

                  <div class="flex-grow-1 small">

                    <p class="mb-0">New user registered</p>

                    <p class="mb-1">User @john_doe has signed up</p>

                    <div class="text-secondary">30 minutes ago</div>

                  </div>

                </div>

              </li>

              <li class="p-3 border-bottom">

                <div class="d-flex gap-3">

                  <img src="assets/images/avatar-2.jpg" alt="" class="avatar avatar-sm rounded-circle" />

                  <div class="flex-grow-1 small">

                    <p class="mb-0">Payment confirmed</p>

                    <p class="mb-1">Payment of $299 has been received</p>

                    <div class="text-secondary">1 hour ago</div>

                  </div>

                </div>

              </li>

              <li class="px-4 py-3 text-center">

                <a href="objectives.php" class="text-success">View all objectives</a>

              </li>

            </ul>

          </div>

        </li> 

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

                  <span>Profil</span>

                </a>

                <a href="#" onclick="logout(); return false;" class="text-decoration-none text-body d-flex align-items-center gap-2 px-2 py-2 rounded" style="transition: all 0.2s ease;">

                  <i class="ti ti-logout text-danger"></i>

                  <span>Déconnexion</span>

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
      <li><a class="nav-link active" href="index.php"><i class="ti ti-home"></i><span
            class="nav-text">Tableau de bord</span></a></li>
      <li><a class="nav-link" href="users.php"><i class="ti ti-users"></i><span
            class="nav-text">Utilisateurs</span></a></li>
      <li class="px-4 py-2"><small class="nav-text">Nutrition</small></li>
      <li><a class="nav-link" href="#meals-section"><i class="ti ti-tools-kitchen-2"></i><span
            class="nav-text">Repas</span></a></li>
      <li><a class="nav-link" href="#ingredients-section"><i class="ti ti-leaf"></i><span
            class="nav-text">Ingrédients</span></a></li>
      <li class="px-4 py-2"><small class="nav-text">Planning</small></li>
      <li><a class="nav-link" href="planning_list.php"><i class="ti ti-calendar-event"></i><span
            class="nav-text">Gérer les plans</span></a></li>
      <li><a class="nav-link" href="planning_create.php"><i class="ti ti-plus"></i><span
            class="nav-text">Créer un plan</span></a></li>
      <li><a class="nav-link" href="objectives.php"><i class="ti ti-target"></i><span
            class="nav-text">Objectifs</span></a></li>

      <li class="px-4 py-2"><small class="nav-text">Sport</small></li>
        <li><a class="nav-link" href="../../index.php?c=activite"><i class="ti ti-activity"></i><span class="nav-text">Activites Sportives</span></a></li>
        <li class="px-4 py-2"><small class="nav-text">Posts</small></li>
        <li><a class="nav-link" href="post_list.php"><i class="ti ti-news"></i><span class="nav-text">Manage Posts</span></a></li>
        <li><a class="nav-link" href="post_create.php"><i class="ti ti-plus"></i><span class="nav-text">Create Post</span></a></li>
        <li><a class="nav-link" href="comment_list.php"><i class="ti ti-message-2"></i><span class="nav-text">Comments</span></a></li>
      <li><a class="nav-link" href="../../index.php?c=activite"><i class="ti ti-activity"></i><span
            class="nav-text">Activit�s Sportives</span></a></li>

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

            <h1 class="fs-3 mb-1">Tableau de Bord</h1>

            <p>Votre contenu principal va ici…</p>

          </div>

        </div>

      </div>

      <div class="row g-3 mb-3">

        <div class="col-lg-3 col-12">
          <a href="objectives.php" class="text-decoration-none">
            <div class="card p-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-2 h-100" style="cursor:pointer; transition: box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(13,110,253,0.15)'" onmouseout="this.style.boxShadow=''">
              <div class="d-flex gap-3 align-items-center">
                <div class="icon-shape icon-md bg-primary text-white rounded-2">
                  <i class="ti ti-target fs-4"></i>
                </div>
                <div>
                  <h2 class="mb-1 fs-6 text-body">Objectifs</h2>
                  <p class="text-primary mb-0 small fw-semibold">Gérer les objectifs →</p>
                </div>
              </div>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-12">



          <div class="card p-4  bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">



            <div class="d-flex gap-3 ">

              <div class="icon-shape icon-md bg-success text-white rounded-2">

                <i class="ti ti-report-analytics fs-4"></i>

              </div>

              <div>

                <h2 class="mb-3 fs-6">Total Sales</h2>

                <h3 class="fw-bold mb-0">$25,000</h3>

                <p class="text-success mb-0 small">+5% since last month</p>

              </div>

            </div>

          </div>





        </div>

        <div class="col-lg-3 col-12">



          <div class="card p-4  bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">



            <div class="d-flex gap-3 ">

              <div class="icon-shape icon-md bg-success text-white rounded-2">

                <i class="ti ti-repeat fs-4"></i>

              </div>

              <div>

                <h2 class="mb-3 fs-6">Total Purchase</h2>

                <h3 class="fw-bold mb-0">$18,000</h3>

                <p class="text-success mb-0 small">+22% since last month</p>

              </div>

            </div>

          </div>





        </div>

        <div class="col-lg-3 col-12">



          <div class="card p-4  bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">



            <div class="d-flex gap-3 ">

              <div class="icon-shape icon-md bg-success text-white rounded-2">

                <i class="ti ti-currency-dollar fs-4"></i>

              </div>

              <div>

                <h2 class="mb-3 fs-6">Total Expenses</h2>

                <h3 class="fw-bold mb-0">$9,000</h3>

                <p class="text-success mb-0 small">+10% since last month</p>

              </div>

            </div>

          </div>





        </div>

        <div class="col-lg-3 col-12">



          <div class="card p-4  bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">



            <div class="d-flex gap-3 ">

              <div class="icon-shape icon-md bg-success text-white rounded-2">

                <i class="ti ti-notes fs-4"></i>

              </div>

              <div>

                <h2 class="mb-3 fs-6">Invoice Due</h2>

                <h3 class="fw-bold mb-0">$25,000</h3>

                <p class="text-success mb-0 small">+35% since last month</p>

              </div>

            </div>

          </div>





        </div>



      </div>

      <div class="row g-3 mb-3">

        <div class="col-lg-4 col-12">

          <div class="card">

            <div class="card-body p-4">

              <div class="d-flex justify-content-between border-bottom pb-5 mb-3">

                <div>

                  <h3 class="fw-bold h4">$25,458</h3>

                  <span>Total Profit</span>

                </div>

                <div>

                  <i class="ti ti-layers-subtract fs-1 text-success"></i>

                </div>

              </div>

              <div class="d-flex justify-content-between align-items-center small">

                <div class="text-muted"><span class="text-success">+35%</span> vs Last Month</div>

                <div><a href="#" class="link-success text-decoration-underline">View</a></div>

              </div>

            </div>

          </div>



        </div>

        <div class="col-lg-4 col-12">

          <div class="card">

            <div class="card-body p-4">

              <div class="d-flex justify-content-between border-bottom pb-5 mb-3">

                <div>

                  <h3 class="fw-bold h4">$45,458</h3>

                  <span>Total Payment Returns</span>

                </div>

                <div>

                  <i class="ti ti-credit-card fs-1 text-danger"></i>

                </div>

              </div>

              <div class="d-flex justify-content-between align-items-center small">

                <div class="text-muted"><span class="text-danger">-20%</span> vs Last Month</div>

                 <div><a href="#" class="link-primary text-decoration-underline">View</a></div>

              </div>

            </div>

          </div>



        </div>

        <div class="col-lg-4 col-12">

          <div class="card">

            <div class="card-body p-4">

              <div class="d-flex justify-content-between border-bottom pb-5 mb-3">

                <div>

                  <h3 class="fw-bold h4">$34,458</h3>

                  <span>Total Expenses</span>

                </div>

                <div>

                  <i class="ti ti-cash-banknote fs-1 text-success"></i>

                </div>

              </div>

              <div class="d-flex justify-content-between align-items-center small">

                <div class="text-muted"><span class="text-success">-20%</span> vs Last Month</div>

                <div><a href="#" class="link-primary text-decoration-underline">View</a></div>

              </div>

            </div>

          </div>



        </div>



      </div>

      <div class="row g-3 mb-3">

        <div class="col-12 col-lg-6">

          <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center bg-transparent px-4 py-3">

              <h3 class="h5 mb-0">Sales vs Purchase</h3>

              <div>

                <select class="form-select form-select-sm">

                  <option selected>This Year</option>

                  <option>This Month</option>

                  <option>This Week</option>

                </select>

              </div>

            </div>

            <div class="card-body p-4">



              <div id="salesPurchaseChart"></div>

            </div>

          </div>

        </div>





        <div class="col-12 col-lg-6">

          <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center bg-transparent px-4 py-3">

              <h3 class="h5 mb-0">Overall Information</h3>

              <div>

                <select class="form-select form-select-sm">

                  <option selected>Last 6 Months</option>

                  <option>This Month</option>

                  <option>This Week</option>

                </select>

              </div>

            </div>

            <div class="card-body p-4">

              <h3 class="h6">Customers Overview</h3>

              <div class="row align-items-center">

                <div class="col-sm-6">

                  <div id="customerChart">



                  </div>

                </div>

                <div class="col-sm-6">

                  <div class="row">

                    <div class="col-6 border-end">

                      <div class="text-center ">

                        <h2 class="mb-1">5.5K</h2>

                        <p class="text-success mb-2">First Time</p>

                        <span class="badge bg-success"><i class="ti ti-arrow-up-left me-1"></i>25%</span>

                      </div>

                    </div>

                    <div class="col-6">

                      <div class="text-center">

                        <h2 class="mb-1">3.5K</h2>

                        <p class="text-success mb-2">Return</p>

                        <span class="badge bg-success badge-xs d-inline-flex align-items-center"><i

                            class="ti ti-arrow-up-left me-1"></i>21%</span>

                      </div>

                    </div>

                  </div>

                </div>





              </div>

              <div class="row text-center border-top mt-4 pt-4">

                <div class="col-4 border-end">

                  <h3 class="fw-bold mb-2">6987</h3>

                  <small class="text-secondary">Suppliers</small>

                </div>

                <div class="col-4 border-end">

                  <h3 class="fw-bold mb-2">4896</h3>

                  <small class="text-secondary">Customers</small>

                </div>

                <div class="col-4">

                  <h3 class="fw-bold mb-2">487</h3>

                  <small class="text-secondary">Orders</small>

                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

      <div class="row g-3">



        <!-- CARD 1 — Top Selling Products -->

        <div class="col-lg-4">

          <div class="card  h-100">

            <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">

              <h4 class="mb-0 h5">Top Selling Products</h4>

              <button class="btn btn-sm btn-outline-secondary">

                <i class="ti ti-calendar"></i> Today

              </button>

            </div>



            <ul class="list-group list-group-flush">



              <!-- item -->

              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-2.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">Wireless Earphones</p>

                  <div class="d-flex align-items-center gap-2 text-muted">

                    <small class="fw-semibold">$89 </small>

                    <small>•</small>

                    <small>1,250 Units</small>

                  </div>

                </div>

                <span class="badge bg-danger-subtle text-danger border border-danger">18%</span>

              </li>



              <!-- repeat -->

              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-1.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">Gaming Joy Stick</p>

                  <div class="d-flex align-items-center gap-2 text-muted">

                    <small class="fw-semibold">$49 </small>

                    <small>•</small>

                    <small>5,420 Units</small>

                  </div>



                </div>

                <span class="badge bg-success-subtle text-success border border-success">32%</span>

              </li>



              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-3.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">Smart Watch Pro</p>

                  <div class="d-flex align-items-center gap-2 text-muted">

                    <small class="fw-semibold">$98 </small>

                    <small>•</small>

                    <small>862 Units</small>

                  </div>



                </div>

                <span class="badge bg-success-subtle text-success border border-success">22%</span>

              </li>

              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-4.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">USB-C Fast Charger</p>

                  <div class="d-flex align-items-center gap-2 text-muted">

                    <small class="fw-semibold">$35 </small>

                    <small>•</small>

                    <small>3,200 Units</small>

                  </div>



                </div>

                <span class="badge bg-success-subtle text-success border border-success">28%</span>

              </li>

              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-5.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">Portable Bluetooth Speaker</p>

                  <div class="d-flex align-items-center gap-2 text-muted">

                    <small class="fw-semibold">$65 </small>

                    <small>•</small>

                    <small>2,890 Units</small>

                  </div>



                </div>

                <span class="badge bg-success-subtle text-success border border-success">25%</span>

              </li>

            </ul>

          </div>

        </div>



        <!-- CARD 2 — Low Stock Products -->

        <div class="col-lg-4">

          <div class="card  h-100">

            <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">

              <div class="d-flex align-items-center">



                <h4 class="mb-0 h5">Low Stock Products</h4>

              </div>

              <a href="#" class="small text-success text-decoration-underline">View All</a>

            </div>



            <ul class="list-group list-group-flush">



              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-8.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">Wireless Headphones</p>

                  <small>ID: #554433</small>

                </div>

                <div class="d-flex flex-column gap-0 align-items-center">

                  <span class="fw-semibold text-success">06</span>

                  <small class="text-muted">In Stock</small>

                </div>

              </li>



              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-4.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">USB-C Cable Pack</p>

                  <small>ID: #887766</small>

                </div>

                <div class="d-flex flex-column gap-0 align-items-center">

                  <span class="fw-semibold text-success">09</span>

                  <small class="text-muted">In Stock</small>

                </div>

              </li>



              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-10.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">Phone Screen Protector</p>

                  <small>ID: #332211</small>

                </div>

                <div class="d-flex flex-column gap-0 align-items-center">

                  <span class="fw-semibold text-success">03</span>

                  <small class="text-muted">In Stock</small>

                </div>

              </li>

              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-4.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">Portable Charger 20000mAh</p>

                  <small>ID: #998877</small>

                </div>

                <div class="d-flex flex-column gap-0 align-items-center">

                  <span class="fw-semibold text-success">07</span>

                  <small class="text-muted">In Stock</small>

                </div>

              </li>

              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-6.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">Mechanical Keyboard RGB</p>

                  <small>ID: #665544</small>

                </div>

                <div class="d-flex flex-column gap-0 align-items-center">

                  <span class="fw-semibold text-success">02</span>

                  <small class="text-muted">In Stock</small>

                </div>

              </li>

            </ul>

          </div>

        </div>



        <!-- CARD 3 — Recent Sales -->

        <div class="col-lg-4">

          <div class="card  h-100">

            <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">

              <h4 class="mb-0 h5">Recent Sales</h4>

              <button class="btn btn-sm btn-outline-secondary">

                <i class="ti ti-calendar-event"></i> Weekly

              </button>

            </div>



            <ul class="list-group list-group-flush">



              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-7.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">MacBook Pro 16"</p>

                  <div class="d-flex align-items-center gap-2 text-muted">

                    <small class="fw-semibold">Computers </small>

                    <small>•</small>

                    <small>2,$2,499</small>

                  </div>



                </div>

                <span class="badge bg-success-subtle text-success">Completed</span>

              </li>



              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-9.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">AirPods Pro Max</p>

                  <div class="d-flex align-items-center gap-2 text-muted">

                    <small class="fw-semibold">Audio </small>

                    <small>•</small>

                    <small>$549</small>

                  </div>



                </div>

                <span class="badge bg-success-subtle text-success">Processing</span>

              </li>



              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-8.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">iPad Air 11"</p>

                  <div class="d-flex align-items-center gap-2 text-muted">

                    <small class="fw-semibold">Tablets </small>

                    <small>•</small>

                    <small>$799</small>

                  </div>

                </div>

                <span class="badge bg-success-subtle text-success">Completed</span>

              </li>



              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-3.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">Apple Watch Ultra</p>

                  <div class="d-flex align-items-center gap-2 text-muted">

                    <small class="fw-semibold">Wearables </small>

                    <small>•</small>

                    <small>$799</small>

                  </div>

                </div>

                <span class="badge bg-success-subtle text-success">Pending</span>

              </li>



              <li class="list-group-item d-flex align-items-center gap-3">

                <img src="assets/images/product-6.png" class="rounded" width="48">

                <div class="flex-grow-1">

                  <p class="mb-1">Magic Keyboard</p>

                  <div class="d-flex align-items-center gap-2 text-muted">

                    <small class="fw-semibold">Accessories </small>

                    <small>•</small>

                    <small>$299</small>

                  </div>



                </div>

                <span class="badge bg-danger-subtle text-danger">Cancelled</span>

              </li>

            </ul>

          </div>

        </div>



      </div>

      

      <!-- Meals Section -->

      <div id="meals-section" class="row g-3 mb-3">

        <div class="col-12">

          <div class="card">

            <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">

              <h4 class="mb-0 h5"><i class="ti ti-utensils me-2"></i>Gestion des Repas</h4>

            </div>

            <div class="card-body p-4">

              <!-- Success/Error Messages -->

              <?php if (isset($_SESSION['success_message'])): ?>

                <div class="alert alert-success alert-dismissible fade show" role="alert">

                  <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>

                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                  </button>

                </div>

              <?php endif; ?>



              <?php if (isset($_SESSION['error_message'])): ?>

                <div class="alert alert-danger alert-dismissible fade show" role="alert">

                  <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>

                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                  </button>

                </div>

              <?php endif; ?>

              <!-- Search and Sort Controls for Meals -->

              <div class="row mb-3 g-2">

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

              <div class="table-responsive">

                <table class="table table-striped table-hover" id="mealsTable">

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

                      <tr>

                        <td colspan="4" class="text-center"><em>Aucun repas trouvé</em></td>

                      </tr>

                    <?php else: ?>

                      <?php foreach ($meals as $meal): ?>

                        <tr>

                          <td><?php echo htmlspecialchars($meal['name']); ?></td>

                          <td><?php echo htmlspecialchars($meal['date']); ?></td>

                          <td><?php echo htmlspecialchars(substr($meal['notes'], 0, 50)) . (strlen($meal['notes']) > 50 ? '...' : ''); ?></td>

                          <td>

                            <a href="#" class="btn btn-sm btn-danger delete-meal" data-id="<?php echo htmlspecialchars($meal['id']); ?>"><i class="ti ti-trash"></i> Supprimer</a>

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



      <!-- Ingredients Section -->

      <div id="ingredients-section" class="row g-3 mb-3">

        <div class="col-12">

          <div class="card">

            <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">

              <h4 class="mb-0 h5"><i class="ti ti-leaf me-2"></i>Gestion des Ingrédients</h4>

            </div>

            <div class="card-body p-4">

              <!-- Search and Sort Controls for Ingredients -->

              <div class="row mb-3 g-2">

                <div class="col-md-6">

                  <input type="text" id="ingredientsSearchInput" class="form-control" placeholder="Rechercher les ingrédients par nom...">

                </div>

                <div class="col-md-6">

                  <select id="ingredientsSortSelect" class="form-select">

                    <option value="name-asc">Trier par: Nom (A-Z)</option>

                    <option value="name-desc">Trier par: Nom (Z-A)</option>

                    <option value="calories-high">Trier par: Calories (Haut à Bas)</option>

                    <option value="calories-low">Trier par: Calories (Bas à Haut)</option>

                    <option value="protein-high">Trier par: Protéines (Haut à Bas)</option>

                    <option value="protein-low">Trier par: Protéines (Bas à Haut)</option>

                  </select>

                </div>

              </div>

              <div class="table-responsive">

                <table class="table table-striped table-hover" id="ingredientsTable">

                  <thead class="table-dark">

                    <tr>

                      <th>Nom</th>

                      <th>Cal (kcal)</th>

                      <th>Prot (g)</th>

                      <th>Glucides (g)</th>

                      <th>Lipides (g)</th>

                      <th>Actions</th>

                    </tr>

                  </thead>

                  <tbody id="ingredientsTableBody">

                    <?php if (empty($ingredients)): ?>

                      <tr>

                        <td colspan="6" class="text-center"><em>Aucun ingrédient trouvé</em></td>

                      </tr>

                    <?php else: ?>

                      <?php foreach ($ingredients as $ing): ?>

                        <tr>

                          <td><?php echo htmlspecialchars($ing['name']); ?></td>

                          <td><?php echo htmlspecialchars($ing['calories']); ?></td>

                          <td><?php echo htmlspecialchars($ing['proteins']); ?></td>

                          <td><?php echo htmlspecialchars($ing['glucides']); ?></td>

                          <td><?php echo htmlspecialchars($ing['lipides']); ?></td>

                          <td>

                            <a href="#" class="btn btn-sm btn-danger delete-ingredient" data-id="<?php echo htmlspecialchars($ing['id']); ?>"><i class="ti ti-trash"></i> Supprimer</a>

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



      <div class="row">

        <div class="col-12">

<footer class="text-center py-2 mt-6 text-secondary ">

        <p class="mb-0">Copyright © 2026 InApp Inventory Dashboard. Developed by <a href="https://codescandy.com/" target="_blank" class="text-success">CodesCandy</a> • Distributed by <a href="https://themewagon.com/" target="_blank" class="text-success">ThemeWagon</a> </p>

      </footer>

        </div>



      </div>



    </div>

  </main>



  <!-- Bootstrap JS -->



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>



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



        // Delete meal functionality

        document.querySelectorAll('.delete-meal').forEach(button => {

            button.addEventListener('click', function(e) {

                e.preventDefault();

                const mealId = this.getAttribute('data-id');

                if (confirm('Êtes-vous sûr de vouloir supprimer ce repas ?')) {

                    window.location.href = `index.php?delete_meal=${mealId}`;

                }

            });

        });



        // Delete ingredient functionality

        document.querySelectorAll('.delete-ingredient').forEach(button => {

            button.addEventListener('click', function(e) {

                e.preventDefault();

                const ingredientId = this.getAttribute('data-id');

                if (confirm('Êtes-vous sûr de vouloir supprimer cet ingrédient ?')) {

                    window.location.href = `index.php?delete_ingredient=${ingredientId}`;

                }

            });

        });



        // Smooth scrolling function

        window.scrollToSection = function(sectionId) {

            const element = document.getElementById(sectionId);

            if (element) {

                element.scrollIntoView({ behavior: 'smooth' });

            }

        };



        // ===== MEALS SEARCH AND SORT FUNCTIONALITY =====

        const mealsSearchInput = document.getElementById('mealsSearchInput');

        const mealsSortSelect = document.getElementById('mealsSortSelect');

        const mealsTableBody = document.getElementById('mealsTableBody');

        let mealsData = [];



        // Collect initial meals data

        function initializeMealsData() {

            mealsData = [];

            const rows = mealsTableBody.querySelectorAll('tr');

            rows.forEach(row => {

                const cells = row.querySelectorAll('td');

                if (cells.length > 0 && cells[0].textContent.trim() !== 'Aucun repas trouvé') {

                    mealsData.push({

                        name: cells[0].textContent.trim(),

                        date: cells[1].textContent.trim(),

                        notes: cells[2].textContent.trim(),

                        actions: cells[3].innerHTML,

                        originalHTML: row.innerHTML

                    });

                }

            });

        }



        // Filter and sort meals

        function filterAndSortMeals() {

            const searchTerm = mealsSearchInput.value.toLowerCase();

            const sortValue = mealsSortSelect.value;



            let filteredMeals = mealsData.filter(meal => 

                meal.name.toLowerCase().includes(searchTerm)

            );



            // Sort meals

            if (sortValue === 'name-asc') {

                filteredMeals.sort((a, b) => a.name.localeCompare(b.name));

            } else if (sortValue === 'name-desc') {

                filteredMeals.sort((a, b) => b.name.localeCompare(a.name));

            } else if (sortValue === 'date-newest') {

                filteredMeals.sort((a, b) => new Date(b.date) - new Date(a.date));

            } else if (sortValue === 'date-oldest') {

                filteredMeals.sort((a, b) => new Date(a.date) - new Date(b.date));

            }



            // Update table

            if (filteredMeals.length === 0) {

                mealsTableBody.innerHTML = '<tr><td colspan="4" class="text-center"><em>Aucun repas trouvé</em></td></tr>';

            } else {

                mealsTableBody.innerHTML = filteredMeals.map(meal => 

                    `<tr><td>${meal.name}</td><td>${meal.date}</td><td>${meal.notes}</td><td>${meal.actions}</td></tr>`

                ).join('');

                // Re-attach delete handlers

                reattachDeleteHandlers();

            }

        }



        // ===== INGREDIENTS SEARCH AND SORT FUNCTIONALITY =====

        const ingredientsSearchInput = document.getElementById('ingredientsSearchInput');

        const ingredientsSortSelect = document.getElementById('ingredientsSortSelect');

        const ingredientsTableBody = document.getElementById('ingredientsTableBody');

        let ingredientsData = [];



        // Collect initial ingredients data

        function initializeIngredientsData() {

            ingredientsData = [];

            const rows = ingredientsTableBody.querySelectorAll('tr');

            rows.forEach(row => {

                const cells = row.querySelectorAll('td');

                if (cells.length > 0 && cells[0].textContent.trim() !== 'Aucun ingrédient trouvé') {

                    ingredientsData.push({

                        name: cells[0].textContent.trim(),

                        calories: parseFloat(cells[1].textContent.trim()) || 0,

                        proteins: parseFloat(cells[2].textContent.trim()) || 0,

                        glucides: cells[3].textContent.trim(),

                        lipides: cells[4].textContent.trim(),

                        actions: cells[5].innerHTML,

                        originalHTML: row.innerHTML

                    });

                }

            });

        }



        // Filter and sort ingredients

        function filterAndSortIngredients() {

            const searchTerm = ingredientsSearchInput.value.toLowerCase();

            const sortValue = ingredientsSortSelect.value;



            let filteredIngredients = ingredientsData.filter(ing => 

                ing.name.toLowerCase().includes(searchTerm)

            );



            // Sort ingredients

            if (sortValue === 'name-asc') {

                filteredIngredients.sort((a, b) => a.name.localeCompare(b.name));

            } else if (sortValue === 'name-desc') {

                filteredIngredients.sort((a, b) => b.name.localeCompare(a.name));

            } else if (sortValue === 'calories-high') {

                filteredIngredients.sort((a, b) => b.calories - a.calories);

            } else if (sortValue === 'calories-low') {

                filteredIngredients.sort((a, b) => a.calories - b.calories);

            } else if (sortValue === 'protein-high') {

                filteredIngredients.sort((a, b) => b.proteins - a.proteins);

            } else if (sortValue === 'protein-low') {

                filteredIngredients.sort((a, b) => a.proteins - b.proteins);

            }



            // Update table

            if (filteredIngredients.length === 0) {

                ingredientsTableBody.innerHTML = '<tr><td colspan="6" class="text-center"><em>Aucun ingrédient trouvé</em></td></tr>';

            } else {

                ingredientsTableBody.innerHTML = filteredIngredients.map(ing => 

                    `<tr><td>${ing.name}</td><td>${ing.calories}</td><td>${ing.proteins}</td><td>${ing.glucides}</td><td>${ing.lipides}</td><td>${ing.actions}</td></tr>`

                ).join('');

                // Re-attach delete handlers

                reattachDeleteHandlers();

            }

        }



        // Re-attach delete handlers after table update

        function reattachDeleteHandlers() {

            document.querySelectorAll('.delete-meal').forEach(button => {

                button.addEventListener('click', function(e) {

                    e.preventDefault();

                    const mealId = this.getAttribute('data-id');

                    if (confirm('Êtes-vous sûr de vouloir supprimer ce repas ?')) {

                        window.location.href = `index.php?delete_meal=${mealId}`;

                    }

                });

            });



            document.querySelectorAll('.delete-ingredient').forEach(button => {

                button.addEventListener('click', function(e) {

                    e.preventDefault();

                    const ingredientId = this.getAttribute('data-id');

                    if (confirm('Êtes-vous sûr de vouloir supprimer cet ingrédient ?')) {

                        window.location.href = `index.php?delete_ingredient=${ingredientId}`;

                    }

                });

            });

        }



        // Event listeners for meals

        if (mealsSearchInput && mealsSortSelect) {

            mealsSearchInput.addEventListener('input', filterAndSortMeals);

            mealsSortSelect.addEventListener('change', filterAndSortMeals);

            initializeMealsData();

        }



        // Event listeners for ingredients

        if (ingredientsSearchInput && ingredientsSortSelect) {

            ingredientsSearchInput.addEventListener('input', filterAndSortIngredients);

            ingredientsSortSelect.addEventListener('change', filterAndSortIngredients);

            initializeIngredientsData();

        }

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

    

    @keyframes slideIn {

        from {

            transform: translateY(-50px);

            opacity: 0;

        }

        to {

            transform: translateY(0);

            opacity: 1;

        }

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

<<<<<<< HEAD


  <!-- New Objectives Modal -->

=======
  <!-- Modal Nouveaux Objectifs -->
>>>>>>> planning
  <div class="modal fade" id="newObjectivesModal" tabindex="-1" aria-labelledby="newObjectivesModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">

      <div class="modal-content">

        <div class="modal-header">
<<<<<<< HEAD

          <h5 class="modal-title" id="newObjectivesModalLabel">New Objectives Added</h5>

          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

=======
          <h5 class="modal-title" id="newObjectivesModalLabel">Nouveaux objectifs ajoutés</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
>>>>>>> planning
        </div>

        <div class="modal-body">
<<<<<<< HEAD

          <p>The following objectives have been added since your last login:</p>

=======
          <p>Les objectifs suivants ont été ajoutés depuis votre dernière connexion :</p>
>>>>>>> planning
          <div class="table-responsive">

            <table class="table table-striped">

              <thead>

                <tr>
<<<<<<< HEAD

                  <th>User</th>

                  <th>Type</th>

                  <th>Target Value</th>

                  <th>Deadline</th>

                  <th>Status</th>

                  <th>Created</th>

=======
                  <th>Utilisateur</th>
                  <th>Type</th>
                  <th>Valeur cible</th>
                  <th>Date limite</th>
                  <th>Statut</th>
                  <th>Créé le</th>
>>>>>>> planning
                </tr>

              </thead>

              <tbody>

                <?php if (!empty($newObjectives)): ?>

                  <?php foreach ($newObjectives as $obj): ?>

                    <tr>
<<<<<<< HEAD

                      <td><?php echo htmlspecialchars($obj['user_nom'] ?? 'Unknown'); ?></td>

=======
                      <td><?php echo htmlspecialchars($obj['user_nom'] ?? 'Inconnu'); ?></td>
>>>>>>> planning
                      <td><?php echo htmlspecialchars($obj['type_objectif']); ?></td>

                      <td><?php echo htmlspecialchars($obj['valeur_cible']); ?></td>

                      <td><?php echo htmlspecialchars($obj['date_limite']); ?></td>

                      <td><?php echo htmlspecialchars($obj['statut']); ?></td>

                      <td><?php echo htmlspecialchars($obj['date_creation']); ?></td>

                    </tr>

                  <?php endforeach; ?>

                <?php else: ?>

                  <tr>
<<<<<<< HEAD

                    <td colspan="6" class="text-center">No new objectives.</td>

=======
                    <td colspan="6" class="text-center">Aucun nouvel objectif.</td>
>>>>>>> planning
                  </tr>

                <?php endif; ?>

              </tbody>

            </table>

          </div>

        </div>

        <div class="modal-footer">
<<<<<<< HEAD

          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

          <a href="objectives.php" class="btn btn-primary">View All Objectives</a>

=======
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
          <a href="objectives.php" class="btn btn-primary">Voir tous les objectifs</a>
>>>>>>> planning
        </div>

      </div>

    </div>

  </div>



  <script>

    <?php if (!empty($newObjectives)): ?>
<<<<<<< HEAD

      // Show modal on page load

      document.addEventListener('DOMContentLoaded', function() {

        var modal = new bootstrap.Modal(document.getElementById('newObjectivesModal'));

        modal.show();

=======
      // Afficher le modal une seule fois par session
      document.addEventListener('DOMContentLoaded', function() {
        var sessionKey = 'objectivesModalShown_<?php echo md5(serialize(array_column($newObjectives, "id_objectif"))); ?>';
        if (!sessionStorage.getItem(sessionKey)) {
          var modal = new bootstrap.Modal(document.getElementById('newObjectivesModal'));
          modal.show();
          sessionStorage.setItem(sessionKey, '1');
        }
>>>>>>> planning
      });

    <?php endif; ?>

  </script>



</body>





<!-- Mirrored from themewagon.github.io/inapp/ by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 09 Apr 2026 13:10:27 GMT -->

</html>
