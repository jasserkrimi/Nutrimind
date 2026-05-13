<?php
// Layout Front
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>NutriMind - ActivitÃ©s Sportives</title>

	<link rel="shortcut icon" type="image/png" href="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/img/favicon.png">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/css/all.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/css/owl.carousel.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/css/magnific-popup.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/css/animate.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/css/meanmenu.min.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/css/main.css">
	<link rel="stylesheet" href="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/css/responsive.css">
	<!-- NutriMind Sport AI Chat Widget -->
	<link rel="stylesheet" href="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/css/sport-ai-chat.css">
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
								<img src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/img/logo.png" alt="">
							</a>
						</div>

						<nav class="main-menu">
							<ul>
								<li><a href="views/index.php">🏠 Home</a></li>
								<li><a href="views/post_list.php">Posts</a></li>
								<li><a href="views/meal_list.php">Repas</a></li>
								<li><a href="views/objectif_list.php">Planning</a></li>
								<li class="current-list-item"><a href="#">🏋️ Sport</a>
									<ul class="sub-menu">
										<li><a href="index.php?c=home">🏃 Activités & Exercices</a></li>
										<li><a href="index.php?c=home&action=planning">📅 Planning Sportif</a></li>
									</ul>
								</li>
								<li><a href="views/shop.html">Shop</a></li>
								<li>
									<div class="header-icons">
										<a href="index.php?c=boutique" id="nm-cart-nav" onclick="window.location.href='index.php?c=boutique';return false;" style="position:relative;display:inline-block"><i class="fas fa-shopping-cart"></i><span id="nm-cart-nav-count" style="display:none;background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;font-size:.65rem;font-weight:800;text-align:center;line-height:18px;position:absolute;top:-8px;right:-10px">0</span></a>
										<a class="mobile-hide search-bar-icon" href="#" id="nm-search-toggle"><i class="fas fa-search"></i></a>
									</div>
								</li>
							</ul>
						</nav>
						<a class="mobile-show search-bar-icon" href="#"><i class="fas fa-search"></i></a>
						<!-- Barre de recherche Sport -->
						<div id="nm-search-bar" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;z-index:9999;padding:16px 24px;box-shadow:0 8px 24px rgba(0,0,0,.15);border-top:3px solid #F28123;">
							<form action="index.php" method="GET" style="display:flex;gap:10px;max-width:600px;margin:0 auto">
								<input type="hidden" name="c" value="boutique">
								<input type="text" name="q" placeholder="🔍 Rechercher un produit sport..." style="flex:1;padding:10px 16px;border:2px solid #F28123;border-radius:8px;font-size:15px;outline:none">
								<button type="submit" style="background:#F28123;color:#fff;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;font-weight:700">Chercher</button>
							</form>
						</div>
						<script>
						document.getElementById('nm-search-toggle').addEventListener('click',function(e){
							e.preventDefault();
							var bar=document.getElementById('nm-search-bar');
							bar.style.display=bar.style.display==='none'?'block':'none';
							if(bar.style.display==='block') bar.querySelector('input[name=q]').focus();
						});
						// Badge panier dans la nav
						(function(){
							try{
								var c=JSON.parse(localStorage.getItem('nm_sport_cart')||'[]');
								var n=c.reduce(function(s,i){return s+(i.qty||0);},0);
								var b=document.getElementById('nm-cart-nav-count');
								if(b&&n>0){b.textContent=n;b.style.display='inline-block';}
							}catch(e){}
						})();
						</script>
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
						<p>DÃ©couvrez notre</p>
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
						<h2 class="widget-title">Ã€ propos de nous</h2>
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
					<p>Droits d'auteur &copy; 2026 - NutriMind, Tous droits rÃ©servÃ©s.</p>
				</div>
			</div>
		</div>
	</div>

	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/jquery-1.11.3.min.js"></script>
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/jquery.countdown.js"></script>
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/jquery.isotope-3.0.6.min.js"></script>
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/waypoints.js"></script>
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/owl.carousel.min.js"></script>
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/jquery.magnific-popup.min.js"></script>
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/jquery.meanmenu.min.js"></script>
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/sticker.js"></script>
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/main.js"></script>

	<!-- NutriMind Sport AI Chat Widget -->
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/sport-ai-chat.js"></script>
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/sport-ai-widget.js"></script>

	<!-- NutriMind — Système de Notifications Séances -->
	<script>window.NM_API_URL = '<?= rtrim(BASE_URL ?? '/nutrimind_int/', '/') ?>/api_notifications.php';</script>
	<script src="<?= BASE_URL ?? '/nutrimind_int/' ?>views/assets_front/js/sport-notifications.js"></script>

</body>
</html>
