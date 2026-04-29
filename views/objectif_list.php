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

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = htmlspecialchars($_GET['delete']);
    if ($objectiveController->delete($deleteId)) {
        $_SESSION['success_message'] = "Objectif supprimé avec succès!";
        header('Location: objectif_list.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression de l'objectif!";
    }
}
?>
<?php include 'header.php'; ?>

	<!-- objective section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">
						<h3><span class="orange-text">Mes</span> Objectifs</h3>
						<p>Gérez vos objectifs nutritionnels et de remise en forme</p>
					</div>
				</div>
			</div>

			<!-- Navigation Buttons -->
			<div class="row mb-4">
				<div class="col-lg-12 text-center">
					<a href="objectif_list.php" class="btn btn-primary me-2" style="background:#e07b39;border-color:#e07b39;">
						<i class="fas fa-bullseye"></i> Mes Objectifs
					</a>
					<a href="mes_plans.php" class="btn btn-outline-secondary me-2">
						<i class="fas fa-utensils"></i> Mes Plans Nutritionnels
					</a>
					<a href="mes_statistiques.php" class="btn btn-outline-secondary">
						<i class="fas fa-chart-pie"></i> Statistiques
					</a>
				</div>
			</div>

			<!-- Success/Error Messages -->
			<?php if (isset($_SESSION['success_message'])): ?>
				<div class="row mb-4">
					<div class="col-lg-12">
						<div class="alert alert-success alert-dismissible fade show" role="alert">
							<?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (isset($_SESSION['error_message'])): ?>
				<div class="row mb-4">
					<div class="col-lg-12">
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<!-- Add New Button -->
			<div class="row mb-4">
				<div class="col-lg-12 text-center">
					<a href="objectif_create.php" class="boxed-btn"><i class="fas fa-plus"></i> Ajouter un objectif</a>
				</div>
			</div>

			<!-- Objectives Search -->
			<div class="row mb-4">
				<div class="col-lg-12">
					<input type="text" id="objectivesSearchInput" placeholder="Rechercher dans vos objectifs..."
					       class="form-control" style="max-width: 500px;">
				</div>
			</div>

			<!-- Objectives Table -->
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table class="table table-striped table-hover" id="objectivesTable">
							<thead class="table-dark">
								<tr>
									<th>ID</th>
									<th>Type</th>
									<th>Valeur Cible</th>
									<th>Poids Initial</th>
									<th>Date Limite</th>
									<th>Statut</th>
									<th>Priorité</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody id="objectivesTableBody">
								<?php if (empty($objectives)): ?>
									<tr>
										<td colspan="8" class="text-center"><em>Aucun objectif trouvé</em></td>
									</tr>
								<?php else: ?>
									<?php foreach ($objectives as $objectif): ?>
										<tr>
											<td><?php echo htmlspecialchars($objectif['id_objectif']); ?></td>
											<td><?php echo htmlspecialchars($objectif['type_objectif']); ?></td>
											<td><?php echo htmlspecialchars($objectif['valeur_cible']); ?></td>
											<td><?php echo htmlspecialchars($objectif['poids_initial'] ?? '-'); ?></td>
											<td><?php echo htmlspecialchars($objectif['date_limite'] ?? '-'); ?></td>
											<td>
												<span class="badge badge-<?php
													switch($objectif['statut']) {
														case 'en_attente': echo 'secondary'; break;
														case 'en_cours':   echo 'primary';   break;
														case 'termine':    echo 'success';   break;
														case 'annule':     echo 'danger';    break;
														default:           echo 'light';
													}
												?>">
													<?php echo htmlspecialchars($objectif['statut']); ?>
												</span>
											</td>
											<td>
												<span class="badge badge-<?php
													switch($objectif['niveau_priorite']) {
														case 'faible': echo 'info';    break;
														case 'moyen':  echo 'warning'; break;
														case 'eleve':  echo 'danger';  break;
														default:       echo 'light';
													}
												?>">
													<?php echo htmlspecialchars($objectif['niveau_priorite']); ?>
												</span>
											</td>
											<td style="white-space:nowrap;">
												<a href="objectif_edit.php?id=<?php echo htmlspecialchars($objectif['id_objectif']); ?>"
												   class="btn btn-sm btn-warning mb-1">
													<i class="fas fa-edit"></i> Modifier
												</a>
												<a href="#" class="btn btn-sm btn-danger delete-objective mb-1"
												   data-id="<?php echo htmlspecialchars($objectif['id_objectif']); ?>">
													<i class="fas fa-trash"></i> Supprimer
												</a>
												<a href="ai_planning.php?objectif_id=<?php echo htmlspecialchars($objectif['id_objectif']); ?>"
												   class="btn btn-sm mb-1"
												   style="background:#6366f1;border-color:#6366f1;color:#fff;">
													<i class="fas fa-robot"></i> IA Planning
												</a>
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

	<!-- Delete Confirmation Modal -->
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Êtes-vous sûr de vouloir supprimer cet objectif ? Cette action est irréversible.
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
					<a href="#" id="confirmDelete" class="btn btn-danger">Supprimer</a>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.querySelectorAll('.delete-objective').forEach(button => {
			button.addEventListener('click', function(e) {
				e.preventDefault();
				const objectiveId = this.getAttribute('data-id');
				document.getElementById('confirmDelete').href = 'objectif_list.php?delete=' + objectiveId;
				$('#deleteModal').modal('show');
			});
		});

		document.getElementById('objectivesSearchInput').addEventListener('keyup', function() {
			const searchValue = this.value.toLowerCase();
			document.querySelectorAll('#objectivesTableBody tr').forEach(row => {
				row.style.display = row.textContent.toLowerCase().includes(searchValue) ? '' : 'none';
			});
		});
	</script>

<?php include 'footer.php'; ?>
