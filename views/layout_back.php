<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Sport Management - NutriMind Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_back/images/logooo.png">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_back/images/logooo.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_back/images/logooo.png">

  <script type="module" crossorigin src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_back/js/main.js"></script>
  <link rel="stylesheet" crossorigin href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_back/css/main.css">
</head>

<body>
  <div id="overlay" class="overlay"></div>
  <nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
    <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm ">
      <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>
    <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
      <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>
    <div>
      <h4 class="mb-0">NutriMind Sport Management</h4>
    </div>
  </nav>

  <aside id="sidebar" class="sidebar">
    <div class="logo-area">
     <a href="index.php" class="d-inline-flex"><img src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_back/images/logooo.png" alt="Nutrimind" style="max-height: 50px; width: auto;"></a>
    </div>
    <ul class="nav flex-column">
      <li class="px-4 py-2"><small class="nav-text">Main</small></li>
      <li><a class="nav-link" href="views/backoffice/index.php"><i class="ti ti-home"></i><span class="nav-text">Dashboard</span></a></li>
      <li class="px-4 py-2"><small class="nav-text">Sport</small></li>
      <li><a class="nav-link" href="index.php?c=activite"><i class="ti ti-activity"></i><span class="nav-text">Activités Sportives</span></a></li>
      <li><a class="nav-link" href="index.php?c=exercice"><i class="ti ti-stretching"></i><span class="nav-text">Exercices</span></a></li>
      <li class="px-4 py-2"><small class="nav-text">Planning</small></li>
      <li><a class="nav-link" href="index.php?c=seance"><i class="ti ti-calendar"></i><span class="nav-text">Emploi du Temps</span></a></li>
      <li class="px-4 pt-4 pb-2"><small class="nav-text">Account</small></li>
      <li><a class="nav-link" href="index.php?c=home"><i class="ti ti-logout"></i><span class="nav-text">Retour site</span></a></li>
    </ul>
  </aside>

  <main id="content" class="content py-10">
    <div class="container-fluid">
        <?php if (!empty($errors)): ?>
            <div class="row mb-4">
              <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                  </ul>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              </div>
            </div>
        <?php endif; ?>

        <?php echo $content; ?>
        
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
