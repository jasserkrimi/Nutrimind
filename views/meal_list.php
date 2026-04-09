<?php
session_start();
require_once '../controllers/MealController.php';

$mealController = new MealController();
$meals = $mealController->getAll();

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = htmlspecialchars($_GET['delete']);
    if ($mealController->delete($deleteId)) {
        $_SESSION['success_message'] = "Repas supprimé avec succès!";
        header('Location: meal_list.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du repas!";
    }
}
?>
<?php include 'header.php'; ?>

	<!-- meal section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">	
						<h3><span class="orange-text">Nos</span> Repas</h3>
						<p>Gérez votre liste de repas avec ingrédients</p>
					</div>
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
					<a href="meal_create.php" class="boxed-btn"><i class="fas fa-plus"></i> Ajouter un repas</a>
				</div>
			</div>

			<!-- Meals Table -->
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead class="table-dark">
								<tr>
									<th>ID</th>
									<th>Nom</th>
									<th>Date</th>
									<th>Notes</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php if (empty($meals)): ?>
									<tr>
										<td colspan="5" class="text-center"><em>Aucun repas trouvé</em></td>
									</tr>
								<?php else: ?>
									<?php foreach ($meals as $meal): ?>
										<tr>
											<td><?php echo htmlspecialchars($meal['id']); ?></td>
											<td><?php echo htmlspecialchars($meal['name']); ?></td>
											<td><?php echo htmlspecialchars($meal['date']); ?></td>
											<td><?php echo htmlspecialchars(substr($meal['notes'], 0, 50)) . (strlen($meal['notes']) > 50 ? '...' : ''); ?></td>
											<td>
												<a href="meal_edit.php?id=<?php echo htmlspecialchars($meal['id']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Modifier</a>
												<a href="meal_list.php?delete=<?php echo htmlspecialchars($meal['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr?')"><i class="fas fa-trash"></i> Supprimer</a>
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
	<!-- end meal section -->

<?php include 'footer.php'; ?>
