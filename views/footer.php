<?php
if (!isset($asset_path)) {
    $asset_path = file_exists('assets/css/main.css') ? 'assets/' : 'views/assets/';
}
if (!isset($front_asset_path)) {
    $front_asset_path = file_exists('assets_front/css/sport-ai-chat.css') ? 'assets_front/' : 'views/assets_front/';
}
?>
	<!-- footer -->
	<div class="footer-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 col-md-6">
					<div class="footer-box about-widget">
						<h2 class="widget-title">À propos de nous</h2>
						<p>NutriMind vous propose les meilleurs outils de gestion nutritionnelle. Trouvez tous les ingrédients et repas que vous recherchez.</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="footer-box get-in-touch">
						<h2 class="widget-title">Nous Contacter</h2>
						<ul>
							<li>34/8, East Hukupara, Gifirtok, Sadan.</li>
							<li>support@NutriMind.com</li>
							<li>+00 111 222 3333</li>
						</ul>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="footer-box pages">
						<h2 class="widget-title">Pages</h2>
						<ul>
							<li><a href="index.php">Accueil</a></li>
							<li><a href="about.html">À propos</a></li>
							<li><a href="ingredient_list.php">Ingrédients</a></li>
							<li><a href="meal_list.php">Repas</a></li>
							<li><a href="contact.html">Contact</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="footer-box subscribe">
						<h2 class="widget-title">S'abonner</h2>
						<p>Abonnez-vous à notre liste de diffusion pour obtenir les dernières mises à jour.</p>
						<form action="index.html">
							<input type="email" placeholder="Email">
							<button type="submit"><i class="fas fa-paper-plane"></i></button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end footer -->
	
	<!-- copyright -->
	<div class="copyright">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-12">
					<p>Droits d'auteur &copy; 2026 - <a href="https://NutriMind.com/">NutriMind</a>, Tous droits réservés.</p>
				</div>
				<div class="col-lg-6 text-right col-md-12">
					<div class="social-icons">
						<ul>
							<li><a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
							<li><a href="#" target="_blank"><i class="fab fa-twitter"></i></a></li>
							<li><a href="#" target="_blank"><i class="fab fa-instagram"></i></a></li>
							<li><a href="#" target="_blank"><i class="fab fa-linkedin"></i></a></li>
							<li><a href="#" target="_blank"><i class="fab fa-dribbble"></i></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end copyright -->
	
	<!-- jquery -->
	<script src="<?php echo $asset_path; ?>js/jquery-1.11.3.min.js"></script>
	<!-- bootstrap -->
	<script src="<?php echo $asset_path; ?>bootstrap/js/bootstrap.min.js"></script>
	<!-- count down -->
	<script src="<?php echo $asset_path; ?>js/jquery.countdown.js"></script>
	<!-- isotope -->
	<script src="<?php echo $asset_path; ?>js/jquery.isotope-3.0.6.min.js"></script>
	<!-- waypoints -->
	<script src="<?php echo $asset_path; ?>js/waypoints.js"></script>
	<!-- owl carousel -->
	<script src="<?php echo $asset_path; ?>js/owl.carousel.min.js"></script>
	<!-- magnific popup -->
	<script src="<?php echo $asset_path; ?>js/jquery.magnific-popup.min.js"></script>
	<!-- mean menu -->
	<script src="<?php echo $asset_path; ?>js/jquery.meanmenu.min.js"></script>
	<!-- sticker js -->
	<script src="<?php echo $asset_path; ?>js/sticker.js"></script>
	<!-- main js -->
	<script src="<?php echo $asset_path; ?>js/main.js"></script>

	<!-- NutriMind Sport AI Chat Widget -->
	<script src="<?php echo $front_asset_path; ?>js/sport-ai-chat.js"></script>
	<script src="<?php echo $front_asset_path; ?>js/sport-ai-widget.js"></script>

	<!-- NutriMind — Système de Notifications Séances -->
	<script>window.NM_API_URL = '<?= rtrim(BASE_URL ?? '/Nutrimind-main/', '/') ?>/api_planning.php';</script>
	<script src="<?php echo $front_asset_path; ?>js/sport-notifications.js"></script>

</body>
</html>
