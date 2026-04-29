<?php
// Layout Front
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>NutriMind - Activités Sportives</title>

	<link rel="shortcut icon" type="image/png" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/img/favicon.png">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/css/all.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/css/owl.carousel.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/css/magnific-popup.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/css/animate.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/css/meanmenu.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/css/main.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/css/responsive.css">
</head>
<body>
    <div class="loader">
        <div class="loader-inner">
            <div class="circle"></div>
        </div>
    </div>
	
	<div class="top-header-area" id="sticker">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-sm-12 text-center">
					<div class="main-menu-wrap">
						<div class="site-logo">
							<a href="index.php?c=home">
								<img src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/img/logo.png" alt="">
							</a>
						</div>

						<nav class="main-menu">
							<ul>
								<li><a href="views/index.php">Accueil General</a></li>
								<li class="current-list-item"><a href="index.php?c=home">Accueil Sport</a></li>
								<li><a href="index.php?c=home&action=planning">Mon Planning</a></li>
								
								<li>
									<div class="header-icons">
										<a class="shopping-cart" href="#"><i class="fas fa-shopping-cart"></i></a>
										<a class="mobile-hide search-bar-icon" href="#"><i class="fas fa-search"></i></a>
									</div>
								</li>
							</ul>
						</nav>
						<a class="mobile-show search-bar-icon" href="#"><i class="fas fa-search"></i></a>
						<div class="mobile-menu"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

    <div class="breadcrumb-section breadcrumb-bg">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="breadcrumb-text">
						<p>Découvrez notre</p>
						<h1>Module Sport</h1>
					</div>
				</div>
			</div>
		</div>
	</div>

    <div class="mt-150 mb-150">
        <div class="container">
            <?php echo $content; ?>
        </div>
    </div>

	<div class="footer-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 col-md-6">
					<div class="footer-box about-widget">
						<h2 class="widget-title">À propos de nous</h2>
						<p>NutriMind vous propose les meilleurs outils de gestion nutritionnelle et sportive.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="copyright">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-12">
					<p>Droits d'auteur &copy; 2026 - NutriMind, Tous droits réservés.</p>
				</div>
			</div>
		</div>
	</div>

	<script src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/js/jquery-1.11.3.min.js"></script>
	<script src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/js/jquery.countdown.js"></script>
	<script src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/js/jquery.isotope-3.0.6.min.js"></script>
	<script src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/js/waypoints.js"></script>
	<script src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/js/owl.carousel.min.js"></script>
	<script src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/js/jquery.magnific-popup.min.js"></script>
	<script src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/js/jquery.meanmenu.min.js"></script>
	<script src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/js/sticker.js"></script>
	<script src="<?= BASE_URL ?? '/projet2a32/' ?>views/assets_front/js/main.js"></script>

</body>
</html>




