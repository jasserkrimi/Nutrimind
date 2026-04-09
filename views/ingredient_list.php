<?php
session_start();
require_once '../controllers/IngredientController.php';

$ingredientController = new IngredientController();
$ingredients = $ingredientController->getAll();

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = htmlspecialchars($_GET['delete']);
    if ($ingredientController->delete($deleteId)) {
        $_SESSION['success_message'] = "Ingrédient supprimé avec succès!";
        header('Location: ingredient_list.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression de l'ingrédient!";
    }
}
?>
<?php include 'header.php'; ?>

	<!-- ingredient section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">	
						<h3><span class="orange-text">Nos</span> Ingrédients</h3>
						<p>Gérez votre inventaire d'ingrédients nutritionnels</p>
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
					<a href="ingredient_create.php" class="boxed-btn"><i class="fas fa-plus"></i> Ajouter un ingrédient</a>
				</div>
			</div>

			<!-- Ingredients Table -->
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead class="table-dark">
								<tr>
									<th>ID</th>
									<th>Nom</th>
									<th>Cal (kcal)</th>
									<th>Prot (g)</th>
									<th>Glucides (g)</th>
									<th>Lipides (g)</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php if (empty($ingredients)): ?>
									<tr>
										<td colspan="7" class="text-center"><em>Aucun ingrédient trouvé</em></td>
									</tr>
								<?php else: ?>
									<?php foreach ($ingredients as $ing): ?>
										<tr>
											<td><?php echo htmlspecialchars($ing['id']); ?></td>
											<td><?php echo htmlspecialchars($ing['name']); ?></td>
											<td><?php echo htmlspecialchars($ing['calories']); ?></td>
											<td><?php echo htmlspecialchars($ing['proteins']); ?></td>
											<td><?php echo htmlspecialchars($ing['glucides']); ?></td>
											<td><?php echo htmlspecialchars($ing['lipides']); ?></td>
											<td>
											<a href="ingredient_edit.php?id=<?php echo htmlspecialchars($ing['id']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Modifier</a>
											<a href="ingredient_list.php?delete=<?php echo htmlspecialchars($ing['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr?')"><i class="fas fa-trash"></i> Supprimer</a>
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
	<!-- end ingredient section -->

<?php include 'footer.php'; ?>
