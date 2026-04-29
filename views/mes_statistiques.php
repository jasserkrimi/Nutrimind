<?php
session_start();
require_once '../controllers/ObjectiveController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$objectiveController = new ObjectiveController();
$objectives = $objectiveController->getAllForUser($_SESSION['user_id']);

// Compute counts server-side for display
$total    = count($objectives);
$counts   = [];
foreach ($objectives as $obj) {
    $s = $obj['statut'] ?? 'inconnu';
    $counts[$s] = ($counts[$s] ?? 0) + 1;
}
?>
<?php include 'header.php'; ?>

	<!-- Statistics Section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">
						<h3><span class="orange-text">Statistiques</span> des Objectifs</h3>
						<p>Vue d'ensemble de vos objectifs par statut</p>
					</div>
				</div>
			</div>

			<!-- Navigation Buttons -->
			<div class="row mb-4">
				<div class="col-lg-12 text-center">
					<a href="objectif_list.php" class="btn btn-outline-secondary me-2">
						<i class="fas fa-bullseye"></i> Mes Objectifs
					</a>
					<a href="mes_plans.php" class="btn btn-outline-secondary me-2">
						<i class="fas fa-utensils"></i> Mes Plans Nutritionnels
					</a>
					<a href="mes_statistiques.php" class="btn btn-primary" style="background:#e07b39;border-color:#e07b39;">
						<i class="fas fa-chart-pie"></i> Statistiques
					</a>
				</div>
			</div>

			<?php if ($total === 0): ?>
				<div class="row">
					<div class="col-lg-8 offset-lg-2 text-center">
						<p class="text-muted">Aucun objectif trouvé. <a href="objectif_create.php">Créez votre premier objectif</a>.</p>
					</div>
				</div>
			<?php else: ?>

			<!-- Summary Cards -->
			<div class="row g-3 mb-5 justify-content-center">
				<div class="col-6 col-md-3">
					<div class="card text-center p-3" style="border-radius:12px;background:linear-gradient(135deg,#6366f1,#818cf8);color:#fff;">
						<div class="fs-2 fw-bold"><?php echo $total; ?></div>
						<div class="small opacity-75">Total</div>
					</div>
				</div>
				<?php foreach ($counts as $statut => $count):
					$color = match($statut) {
						'en_cours'  => 'linear-gradient(135deg,#6366f1,#818cf8)',
						'termine'   => 'linear-gradient(135deg,#10b981,#34d399)',
						'en_attente'=> 'linear-gradient(135deg,#f59e0b,#fbbf24)',
						'annule'    => 'linear-gradient(135deg,#ef4444,#f87171)',
						default     => 'linear-gradient(135deg,#94a3b8,#cbd5e1)'
					};
				?>
				<div class="col-6 col-md-3">
					<div class="card text-center p-3" style="border-radius:12px;background:<?php echo $color; ?>;color:#fff;">
						<div class="fs-2 fw-bold"><?php echo $count; ?></div>
						<div class="small opacity-75"><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($statut))); ?></div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>

			<!-- Pie Chart -->
			<div class="row">
				<div class="col-lg-6 offset-lg-3 text-center">
					<canvas id="objectiveStatsPie" style="max-width: 420px; margin: 0 auto;"></canvas>
				</div>
			</div>

			<?php endif; ?>
		</div>
	</div>

	<!-- Chart.js Library -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

	<script>
		(function() {
			const objectives = <?php echo json_encode($objectives); ?>;
			if (!objectives.length) return;

			const statuts = {};
			objectives.forEach(obj => {
				const s = obj.statut || 'Inconnu';
				statuts[s] = (statuts[s] || 0) + 1;
			});

			const colorMap = {
				'en_attente': '#f59e0b',
				'en_cours':   '#6366f1',
				'termine':    '#10b981',
				'annule':     '#ef4444',
				'active':     '#10b981',
				'inactive':   '#ef4444',
				'pending':    '#f59e0b'
			};

			const labels = Object.keys(statuts).map(l => l.replace('_', ' ').toUpperCase());
			const data   = Object.values(statuts);
			const colors = Object.keys(statuts).map(l => colorMap[l] || '#94a3b8');

			new Chart(document.getElementById('objectiveStatsPie').getContext('2d'), {
				type: 'doughnut',
				data: {
					labels,
					datasets: [{
						data,
						backgroundColor: colors,
						borderColor: '#fff',
						borderWidth: 3,
						hoverOffset: 10
					}]
				},
				options: {
					responsive: true,
					cutout: '60%',
					plugins: {
						legend: {
							position: 'bottom',
							labels: { padding: 20, font: { size: 14 }, usePointStyle: true, pointStyleWidth: 10 }
						},
						tooltip: {
							callbacks: {
								label: ctx => ctx.label + ': ' + ctx.parsed + ' objectif(s)'
							}
						}
					}
				}
			});
		})();
	</script>

<?php include 'footer.php'; ?>
