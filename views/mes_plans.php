<?php
session_start();
require_once '../controllers/PlanningController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$planningController = new PlanningController();
$plans = $planningController->getAllForUser($_SESSION['user_id']);
?>
<?php include 'header.php'; ?>

	<!-- Plans Section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">
						<h3><span class="orange-text">Mes</span> Plans Nutritionnels</h3>
						<p>Vos plans personnalisés créés par nos experts</p>
					</div>
				</div>
			</div>

			<!-- Navigation Buttons -->
			<div class="row mb-4">
				<div class="col-lg-12 text-center">
					<a href="objectif_list.php" class="btn btn-outline-secondary me-2">
						<i class="fas fa-bullseye"></i> Mes Objectifs
					</a>
					<a href="mes_plans.php" class="btn btn-primary me-2" style="background:#e07b39;border-color:#e07b39;">
						<i class="fas fa-utensils"></i> Mes Plans Nutritionnels
					</a>
					<a href="mes_statistiques.php" class="btn btn-outline-secondary">
						<i class="fas fa-chart-pie"></i> Statistiques
					</a>
				</div>
			</div>

			<!-- Plans Search -->
			<div class="row mb-4">
				<div class="col-lg-12">
					<input type="text" id="plansSearchInput" placeholder="Rechercher dans vos plans nutritionnels..." 
					       class="form-control" style="max-width: 500px;">
				</div>
			</div>

			<!-- Plans Table -->
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table class="table table-striped table-hover" id="plansTable">
							<thead class="table-dark">
								<tr>
									<th>ID</th>
									<th>Titre</th>
									<th>Calories/Jour</th>
									<th>Protéines (g)</th>
									<th>Glucides (g)</th>
									<th>Lipides (g)</th>
									<th>Repas/Jour</th>
									<th>Sommeil (h)</th>
									<th>Entraînement (h)</th>
									<th>Date Début</th>
									<th>Date Fin</th>
									<th>Statut</th>
								</tr>
							</thead>
							<tbody id="plansTableBody">
								<?php if (empty($plans)): ?>
									<tr>
										<td colspan="12" class="text-center"><em>Aucun plan assigné pour le moment</em></td>
									</tr>
								<?php else: ?>
									<?php foreach ($plans as $plan): ?>
										<tr>
											<td><?php echo htmlspecialchars($plan['id_planning']); ?></td>
											<td><?php echo htmlspecialchars($plan['titre'] ?? 'Sans titre'); ?></td>
											<td><?php echo htmlspecialchars($plan['calories_par_jour'] ?? '-'); ?> kcal</td>
											<td><?php echo htmlspecialchars($plan['objectif_proteines'] ?? '-'); ?>g</td>
											<td><?php echo htmlspecialchars($plan['objectif_glucides'] ?? '-'); ?>g</td>
											<td><?php echo htmlspecialchars($plan['objectif_lipides'] ?? '-'); ?>g</td>
											<td><?php echo htmlspecialchars($plan['nombre_repas_par_jour'] ?? '-'); ?></td>
											<td><?php echo htmlspecialchars($plan['heures_sommeil_par_jour'] ?? '-'); ?>h</td>
											<td><?php echo htmlspecialchars($plan['heures_entrainement_par_jour'] ?? '-'); ?>h</td>
											<td><?php echo htmlspecialchars($plan['date_debut'] ?? '-'); ?></td>
											<td><?php echo htmlspecialchars($plan['date_fin'] ?? '-'); ?></td>
											<td>
												<span class="badge badge-<?php
													switch($plan['statut']) {
														case 'actif': echo 'success'; break;
														case 'inactif': echo 'secondary'; break;
														case 'termine': echo 'info'; break;
														default: echo 'light';
													}
												?>">
													<?php echo htmlspecialchars($plan['statut'] ?? 'inconnu'); ?>
												</span>
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

	<script>
		// Dynamic search for plans table
		document.getElementById('plansSearchInput').addEventListener('keyup', function() {
			const searchValue = this.value.toLowerCase();
			document.querySelectorAll('#plansTableBody tr').forEach(row => {
				row.style.display = row.textContent.toLowerCase().includes(searchValue) ? '' : 'none';
			});
		});
	</script>

<?php include 'footer.php'; ?>
