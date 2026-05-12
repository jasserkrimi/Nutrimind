<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="NutriMind - Gestion de la Nutrition">

	<!-- title -->
	<title>NutriMind</title>

	<!-- favicon -->
	<link rel="shortcut icon" type="image/png" href="assets/img/favicon.png">
	<!-- google font -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
	<!-- fontawesome -->
	<link rel="stylesheet" href="assets/css/all.min.css">
	<!-- bootstrap -->
	<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
	<!-- owl carousel -->
	<link rel="stylesheet" href="assets/css/owl.carousel.css">
	<!-- magnific popup -->
	<link rel="stylesheet" href="assets/css/magnific-popup.css">
	<!-- animate css -->
	<link rel="stylesheet" href="assets/css/animate.css">
	<!-- mean menu css -->
	<link rel="stylesheet" href="assets/css/meanmenu.min.css">
	<!-- main style -->
	<link rel="stylesheet" href="assets/css/main.css">
	<!-- responsive -->
	<link rel="stylesheet" href="assets/css/responsive.css">

</head>
<body>

<!-- ── Animated gradient background ── -->
<style>
body {
    background: linear-gradient(-45deg,#e8f5e9,#e3f2fd,#e0f7fa,#f1f8e9,#e8f5e9) !important;
    background-size: 400% 400% !important;
    animation: bgShift 16s ease infinite !important;
}
@keyframes bgShift {
    0%   { background-position: 0%   50%; }
    25%  { background-position: 100% 50%; }
    50%  { background-position: 100% 0%;  }
    75%  { background-position: 0%   100%;}
    100% { background-position: 0%   50%; }
}
.food-particles { position:fixed; inset:0; pointer-events:none; z-index:0; overflow:hidden; }
.food-particles span { position:absolute; bottom:-60px; opacity:0; animation:floatUp linear infinite; user-select:none; }
@keyframes floatUp {
    0%   { transform:translateY(0) rotate(0deg);       opacity:0;  }
    10%  { opacity:.4; }
    90%  { opacity:.2; }
    100% { transform:translateY(-110vh) rotate(360deg); opacity:0; }
}
</style>

<!-- ── Floating food particles ── -->
<div class="food-particles" id="foodParticles" aria-hidden="true"></div>
<script>
(function(){
    var e=['🥗','🍎','🥦','🍋','🥕','🍇','🥑','🍓','🌽','🥝','🍊','🫐'];
    var c=document.getElementById('foodParticles');
    for(var i=0;i<20;i++){
        var s=document.createElement('span');
        s.textContent=e[i%e.length];
        s.style.left=(Math.random()*100)+'%';
        s.style.fontSize=(16+Math.random()*20)+'px';
        s.style.animationDuration=(12+Math.random()*20)+'s';
        s.style.animationDelay=(Math.random()*16)+'s';
        c.appendChild(s);
    }
}());
</script>
	
	<!--PreLoader-->
    <div class="loader">
        <div class="loader-inner">
            <div class="circle"></div>
        </div>
    </div>
    <!--PreLoader Ends-->
	
	<!-- header -->
	<div class="top-header-area" id="sticker">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-sm-12 text-center">
					<div class="main-menu-wrap">
						<!-- logo -->
						<div class="site-logo">
							<a href="index.php">
								<img src="assets/img/logo.png" alt="">
							</a>
						</div>
						<!-- logo -->

						<!-- menu start -->
						<nav class="main-menu">
							<ul>
								<li class="current-list-item"><a href="#">Accueil</a>
									<ul class="sub-menu">
										<li><a href="index.php">Accueil</a></li>
									</ul>
								</li>
								<li><a href="about.html">À propos</a></li>
								<li><a href="#">Pages</a>
									<ul class="sub-menu">
										<li><a href="404.html">Page 404</a></li>
										<li><a href="about.html">À propos</a></li>
										<li><a href="cart.html">Panier</a></li>
										<li><a href="checkout.html">Passer la Commande</a></li>
										<li><a href="contact.html">Contact</a></li>
										<li><a href="news.html">Actualités</a></li>
										<li><a href="shop.html">Boutique</a></li>
									</ul>
								</li>
								<li><a href="#">Repas</a>
									<ul class="sub-menu">
										<li><a href="meal_list.php">Repas</a></li>
										<li><a href="ingredient_list.php">Ingrédients</a></li>
									</ul>
								</li>
								<li><a href="contact.html">Contact</a></li>
								<li><a href="shop.html">Boutique</a>
									<ul class="sub-menu">
									<li><a href="shop.html">Boutique</a></li>
									<li><a href="checkout.html">Passer la Commande</a></li>
									<li><a href="single-product.html">Produit Unique</a></li>
									<li><a href="cart.html">Panier</a></li>
									</ul>
								</li>
								<li>
									<div class="header-icons">
										<a class="shopping-cart" href="cart.html"><i class="fas fa-shopping-cart"></i></a>
										<a class="mobile-hide search-bar-icon" href="#"><i class="fas fa-search"></i></a>
									</div>
								</li>
							</ul>
						</nav>
						<a class="mobile-show search-bar-icon" href="#"><i class="fas fa-search"></i></a>
						<div class="mobile-menu"></div>
						<!-- menu end -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end header -->
