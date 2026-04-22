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

			<!-- Search and Sort Controls -->
			<div class="row mb-4">
				<div class="col-md-6">
					<input type="text" id="ingredientsSearchInput" class="form-control" placeholder="Rechercher les ingrédients par nom...">
				</div>
				<div class="col-md-6">
					<select id="ingredientsSortSelect" class="form-select">
						<option value="name-asc">Trier par: Nom (A-Z)</option>
						<option value="name-desc">Trier par: Nom (Z-A)</option>
						<option value="calories-high">Trier par: Calories (Haut à Bas)</option>
						<option value="calories-low">Trier par: Calories (Bas à Haut)</option>
						<option value="protein-high">Trier par: Protéines (Haut à Bas)</option>
						<option value="protein-low">Trier par: Protéines (Bas à Haut)</option>
					</select>
				</div>
			</div>

			<!-- Ingredients Table -->
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table class="table table-striped table-hover" id="ingredientsTable">
							<thead class="table-dark">
								<tr>
									<th>Nom</th>
									<th>Cal (kcal)</th>
									<th>Prot (g)</th>
									<th>Glucides (g)</th>
									<th>Lipides (g)</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody id="ingredientsTableBody">
								<?php if (empty($ingredients)): ?>
									<tr>
										<td colspan="6" class="text-center"><em>Aucun ingrédient trouvé</em></td>
									</tr>
								<?php else: ?>
									<?php foreach ($ingredients as $ing): ?>
										<tr>
											<td><?php echo htmlspecialchars($ing['name']); ?></td>
											<td><?php echo htmlspecialchars($ing['calories']); ?></td>
											<td><?php echo htmlspecialchars($ing['proteins']); ?></td>
											<td><?php echo htmlspecialchars($ing['glucides']); ?></td>
											<td><?php echo htmlspecialchars($ing['lipides']); ?></td>
											<td>
											<a href="ingredient_edit.php?id=<?php echo htmlspecialchars($ing['id']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Modifier</a>
											<a href="#" class="btn btn-sm btn-danger delete-ingredient" data-id="<?php echo htmlspecialchars($ing['id']); ?>"><i class="fas fa-trash"></i> Supprimer</a>
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

<script>
	// Delete confirmation modal
	function showDeleteModal(id) {
		const modal = document.createElement('div');
		modal.className = 'delete-modal-overlay';
		modal.innerHTML = `
			<div class="delete-modal">
				<div class="delete-modal-content">
					<h3>Confirmer la Suppression</h3>
					<p>Êtes-vous sûr de vouloir supprimer cet ingrédient? Cette action est irréversible.</p>
					<div class="delete-modal-buttons">
						<button class="delete-btn-cancel">Annuler</button>
						<button class="delete-btn-confirm">Supprimer</button>
					</div>
				</div>
			</div>
		`;
		
		document.body.appendChild(modal);
		
		const cancelBtn = modal.querySelector('.delete-btn-cancel');
		const confirmBtn = modal.querySelector('.delete-btn-confirm');
		
		const closeModal = () => modal.remove();
		
		cancelBtn.addEventListener('click', closeModal);
		modal.addEventListener('click', (e) => {
			if (e.target === modal) closeModal();
		});
		
		confirmBtn.addEventListener('click', () => {
			window.location.href = 'ingredient_list.php?delete=' + id;
		});
	}

	// Attach click handlers to delete buttons
	function reattachIngredientDeleteHandlers() {
		document.querySelectorAll('.delete-ingredient').forEach(btn => {
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				const id = btn.getAttribute('data-id');
				showDeleteModal(id);
			});
		});
	}

	// Initial attachment
	reattachIngredientDeleteHandlers();

	// ===== INGREDIENTS SEARCH AND SORT FUNCTIONALITY =====
	const ingredientsSearchInput = document.getElementById('ingredientsSearchInput');
	const ingredientsSortSelect = document.getElementById('ingredientsSortSelect');
	const ingredientsTableBody = document.getElementById('ingredientsTableBody');
	let ingredientsData = [];

	// Collect initial ingredients data
	function initializeIngredientsData() {
		ingredientsData = [];
		const rows = ingredientsTableBody.querySelectorAll('tr');
		rows.forEach(row => {
			const cells = row.querySelectorAll('td');
			if (cells.length > 0 && cells[0].textContent.trim() !== 'Aucun ingrédient trouvé') {
				ingredientsData.push({
					name: cells[0].textContent.trim(),
					calories: parseFloat(cells[1].textContent.trim()) || 0,
					proteins: parseFloat(cells[2].textContent.trim()) || 0,
					glucides: cells[3].textContent.trim(),
					lipides: cells[4].textContent.trim(),
					actions: cells[5].innerHTML,
					originalHTML: row.innerHTML
				});
			}
		});
	}

	// Filter and sort ingredients
	function filterAndSortIngredients() {
		const searchTerm = ingredientsSearchInput.value.toLowerCase();
		const sortValue = ingredientsSortSelect.value;

		let filteredIngredients = ingredientsData.filter(ing => 
			ing.name.toLowerCase().includes(searchTerm)
		);

		// Sort ingredients
		if (sortValue === 'name-asc') {
			filteredIngredients.sort((a, b) => a.name.localeCompare(b.name));
		} else if (sortValue === 'name-desc') {
			filteredIngredients.sort((a, b) => b.name.localeCompare(a.name));
		} else if (sortValue === 'calories-high') {
			filteredIngredients.sort((a, b) => b.calories - a.calories);
		} else if (sortValue === 'calories-low') {
			filteredIngredients.sort((a, b) => a.calories - b.calories);
		} else if (sortValue === 'protein-high') {
			filteredIngredients.sort((a, b) => b.proteins - a.proteins);
		} else if (sortValue === 'protein-low') {
			filteredIngredients.sort((a, b) => a.proteins - b.proteins);
		}

		// Update table
		if (filteredIngredients.length === 0) {
			ingredientsTableBody.innerHTML = '<tr><td colspan="6" class="text-center"><em>Aucun ingrédient trouvé</em></td></tr>';
		} else {
			ingredientsTableBody.innerHTML = filteredIngredients.map(ing => 
				`<tr><td>${ing.name}</td><td>${ing.calories}</td><td>${ing.proteins}</td><td>${ing.glucides}</td><td>${ing.lipides}</td><td>${ing.actions}</td></tr>`
			).join('');
			// Re-attach delete handlers
			reattachIngredientDeleteHandlers();
		}
	}

	// Event listeners for ingredients
	if (ingredientsSearchInput && ingredientsSortSelect) {
		ingredientsSearchInput.addEventListener('input', filterAndSortIngredients);
		ingredientsSortSelect.addEventListener('change', filterAndSortIngredients);
		initializeIngredientsData();
	}
</script>

<style>
	.delete-modal-overlay {
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
	
	.delete-modal {
		background: white;
		border-radius: 8px;
		box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
		animation: slideIn 0.3s ease-out;
	}
	
	.delete-modal-content {
		padding: 30px;
		min-width: 400px;
		text-align: center;
	}
	
	.delete-modal-content h3 {
		margin: 0 0 15px 0;
		color: #333;
		font-size: 22px;
	}
	
	.delete-modal-content p {
		margin: 0 0 30px 0;
		color: #666;
		font-size: 16px;
	}
	
	.delete-modal-buttons {
		display: flex;
		gap: 10px;
		justify-content: center;
	}
	
	.delete-btn-cancel, .delete-btn-confirm {
		padding: 12px 30px;
		border: none;
		border-radius: 5px;
		cursor: pointer;
		font-weight: 600;
		font-size: 14px;
		transition: all 0.3s;
	}
	
	.delete-btn-cancel {
		background-color: #e0e0e0;
		color: #333;
	}
	
	.delete-btn-cancel:hover {
		background-color: #d0d0d0;
	}
	
	.delete-btn-confirm {
		background-color: #dc3545;
		color: white;
	}
	
	.delete-btn-confirm:hover {
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
		.delete-modal-content {
			min-width: 300px;
		}
		
		.delete-modal-buttons {
			flex-direction: column;
		}
	}
</style>
