<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
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
								<li class="current-list-item"><a href="#">Home</a>
									<ul class="sub-menu">
										<li><a href="index.php">Accueil</a></li>
										<li><a href="index_2.html">Accueil 2</a></li>
									</ul>
								</li>
								<li><a href="about.html">About</a></li>
								<li><a href="#">Pages</a>
									<ul class="sub-menu">
										<li><a href="404.html">404 page</a></li>
										<li><a href="about.html">About</a></li>
										<li><a href="cart.html">Cart</a></li>
										<li><a href="checkout.html">Check Out</a></li>
										<li><a href="objectif_list.php">Objectifs Nutrition</a></li><li><a href="../index.php">Module Sport</a></li><li><a href="../index.php?c=home&action=planning">Planning Sport</a></li>
										<li><a href="news.html">News</a></li>
										<li><a href="shop.html">Shop</a></li>
									</ul>
								</li>
								<li><a href="#">Repas</a>
									<ul class="sub-menu">
										<li><a href="meal_list.php">Meal</a></li>
										<li><a href="ingredient_list.php">Ingredients</a></li>
									</ul>
								</li>
								<li><a href="post_list.php">Posts</a></li>
								<li><a href="objectif_list.php">Objectifs Nutrition</a></li><li><a href="../index.php">Module Sport</a></li><li><a href="../index.php?c=home&action=planning">Planning Sport</a></li>
								<li><a href="shop.html">Shop</a>
									<ul class="sub-menu">
										<li><a href="shop.html">Shop</a></li>
										<li><a href="checkout.html">Check Out</a></li>
										<li><a href="single-product.html">Single Product</a></li>
										<li><a href="cart.html">Cart</a></li>
									</ul>
								</li>
								<li>
									<div class="header-icons">
										<a class="shopping-cart" href="cart.html"><i class="fas fa-shopping-cart"></i></a>
										<a class="mobile-hide search-bar-icon" href="#"><i class="fas fa-search"></i></a>
										<div class="user-menu-wrapper">
											<a class="mobile-hide user-icon" href="#"><i class="fas fa-user"></i></a>
											<?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
												<div class="user-dropdown">
													<a href="profile.php"><i class="fas fa-cog"></i> Mon Profil</a>
													<a href="#" onclick="logout(); return false;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
												</div>
											<?php else: ?>
												<div class="user-dropdown">
													<a href="auth.php"><i class="fas fa-sign-in-alt"></i> Connexion</a>
													<a href="auth.php"><i class="fas fa-user-plus"></i> S'inscrire</a>
												</div>
											<?php endif; ?>
										</div>
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




	<style>
		.user-menu-wrapper {
			position: relative;
			display: inline-block;
		}

		.user-icon {
			cursor: pointer;
		}

		.user-dropdown {
			display: none;
			position: absolute;
			right: 0;
			top: 30px;
			background: white;
			border: 1px solid #ddd;
			border-radius: 5px;
			min-width: 180px;
			box-shadow: 0 8px 16px rgba(0,0,0,0.2);
			z-index: 1000;
		}

		.user-dropdown.show {
			display: block;
		}

		.user-dropdown a {
			display: block;
			padding: 12px 20px;
			color: #333;
			text-decoration: none;
			border-bottom: 1px solid #eee;
			transition: background-color 0.2s;
		}

		.user-dropdown a:last-child {
			border-bottom: none;
		}

		.user-dropdown a:hover {
			background-color: #f5f5f5;
			color: #1161ee;
		}

		.user-dropdown i {
			margin-right: 10px;
			width: 14px;
		}
	</style>

	<script>
		// User menu toggle
		document.querySelector('.user-icon').addEventListener('click', function(e) {
			e.preventDefault();
			const dropdown = this.nextElementSibling;
			dropdown.classList.toggle('show');
		});

		// Close dropdown when clicking outside
		document.addEventListener('click', function(e) {
			const wrapper = document.querySelector('.user-menu-wrapper');
			if (!wrapper.contains(e.target)) {
				const dropdown = wrapper.querySelector('.user-dropdown');
				dropdown.classList.remove('show');
			}
		});

		// Logout confirmation modal
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
				fetch('../controllers/UserController.php?action=logout')
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							window.location.href = 'index.php';
						} else {
							const msg = document.createElement('div');
							msg.style.cssText = 'position:fixed;top:20px;right:20px;background:#f8d7da;color:#721c24;padding:15px;border-radius:5px;z-index:9999;';
							msg.textContent = 'Erreur lors de la déconnexion';
							document.body.appendChild(msg);
							setTimeout(() => msg.remove(), 3000);
							closeModal();
						}
					})
					.catch(error => {
						console.error('Error:', error);
						const msg = document.createElement('div');
						msg.style.cssText = 'position:fixed;top:20px;right:20px;background:#f8d7da;color:#721c24;padding:15px;border-radius:5px;z-index:9999;';
						msg.textContent = 'Une erreur s\'est produite';
						document.body.appendChild(msg);
						setTimeout(() => msg.remove(), 3000);
						closeModal();
					});
			});
		}
		
		// Logout function
		function logout() {
			showLogoutModal();
		}
		
		/* Logout Modal Styles */
		const logoutModalStyles = `
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
			
			@media (max-width: 480px) {
				.logout-modal-content {
					min-width: 300px;
				}
				
				.logout-modal-buttons {
					flex-direction: column;
				}
			}
		`;
		
		// Inject modal styles
		const styleSheet = document.createElement('style');
		styleSheet.textContent = logoutModalStyles;
		document.head.appendChild(styleSheet);
	</script>
