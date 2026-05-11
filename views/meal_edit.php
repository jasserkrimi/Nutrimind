<?php
session_start();
require_once '../controllers/MealController.php';
require_once __DIR__ . '/../models/Ingredient.php';

$mealController = new MealController();
$ingredientModel = new Ingredient();
$ingredients = $ingredientModel->getAll();
$errors = array();
$mealData = null;

// Get meal ID from URL
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "L'ID du repas n'a pas été fourni!";
    header('Location: meal_list.php');
    exit;
}

$mealId = htmlspecialchars($_GET['id']);
$mealData = $mealController->getById($mealId);

if (!$mealData) {
    $_SESSION['error_message'] = "Repas non trouvé!";
    header('Location: meal_list.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $mealController->update($mealId, $_POST);
    
    if ($result['success']) {
        $_SESSION['success_message'] = "Repas mis à jour avec succès!";
        header('Location: meal_list.php');
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<?php include 'header.php'; ?>

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

	<!-- edit meal section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2">
					<div class="section-title text-center mb-5">	
						<h3><span class="orange-text">Modifier</span> le Repas</h3>
						<p>Mettre à jour les informations du repas</p>
					</div>

					<!-- Error Messages -->
					<?php if (isset($errors['general'])): ?>
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<?php echo $errors['general']; ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					<?php endif; ?>

					<!-- Edit Form -->
					<form method="POST" class="form">
						<div class="form-group mb-4">
							<label for="name" class="form-label font-weight-bold">Nom du repas <span class="text-danger">*</span></label>
							<input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
								id="name" name="name" placeholder="Ex: Déjeuner du lundi" 
								value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($mealData['name']); ?>" required>
							<?php if (isset($errors['name'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['name']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-group mb-4">
							<label for="date" class="form-label font-weight-bold">Date du repas <span class="text-danger">*</span></label>
							<input type="date" class="form-control <?php echo isset($errors['date']) ? 'is-invalid' : ''; ?>" 
								id="date" name="date" 
								value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : htmlspecialchars($mealData['date']); ?>" required>
							<?php if (isset($errors['date'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['date']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-group mb-4">
							<label for="notes" class="form-label font-weight-bold">Notes (optionnel)</label>
							<textarea class="form-control <?php echo isset($errors['notes']) ? 'is-invalid' : ''; ?>" 
								id="notes" name="notes" rows="4" placeholder="Ajouter des notes sur le repas..."
								maxlength="500"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : htmlspecialchars($mealData['notes']); ?></textarea>
							<small class="form-text text-muted">Maximum 500 caractères</small>
							<?php if (isset($errors['notes'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['notes']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-group mb-4">
							<label class="form-label font-weight-bold">Ingrédients <span class="text-danger">*</span></label>
							<p class="text-muted">Sélectionnez les ingrédients et spécifiez les quantités (en grammes)</p>
							<?php if (isset($errors['ingredients'])): ?>
								<div class="alert alert-danger">
									<?php echo $errors['ingredients']; ?>
								</div>
							<?php endif; ?>
							<?php
							// Prepare arrays for pre-filling
							$selectedIngredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : array_column($mealData['ingredients'], 'id');
							$quantities = isset($_POST['quantities']) ? $_POST['quantities'] : array_column($mealData['ingredients'], 'quantity', 'id');
							?>

							<!-- Live search for ingredients -->
							<div class="mb-3">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text bg-white">
											<i class="fas fa-search text-muted"></i>
										</span>
									</div>
									<input type="text" id="ingredientSearch"
										class="form-control"
										placeholder="Rechercher un ingrédient…"
										autocomplete="off">
								</div>
								<small id="ingredientSearchCount" class="form-text text-muted mt-1"></small>
							</div>

							<div class="row" id="ingredientsGrid">
								<?php foreach ($ingredients as $ingredient): ?>
									<div class="col-md-6 mb-3 ingredient-card-wrap"
									     data-name="<?php echo strtolower(htmlspecialchars($ingredient['name'])); ?>">
										<div class="card p-3">
											<div class="form-check">
												<input class="form-check-input" type="checkbox" 
													id="ingredient_<?php echo $ingredient['id']; ?>" 
													name="ingredients[]" 
													value="<?php echo $ingredient['id']; ?>"
													<?php echo in_array($ingredient['id'], $selectedIngredients) ? 'checked' : ''; ?>>
												<label class="form-check-label font-weight-bold" for="ingredient_<?php echo $ingredient['id']; ?>">
													<?php echo htmlspecialchars($ingredient['name']); ?>
												</label>
											</div>
											<div class="mt-2">
												<label for="quantity_<?php echo $ingredient['id']; ?>" class="form-label small">Quantité (g)</label>
												<input type="number" class="form-control form-control-sm" 
													id="quantity_<?php echo $ingredient['id']; ?>" 
													name="quantities[<?php echo $ingredient['id']; ?>]" 
													min="1" step="0.1" placeholder="Ex: 100"
													value="<?php echo isset($quantities[$ingredient['id']]) ? htmlspecialchars($quantities[$ingredient['id']]) : ''; ?>">
											</div>
											<small class="text-muted">
												Cal: <?php echo $ingredient['calories']; ?>/100g, Prot: <?php echo $ingredient['proteins']; ?>g, Gluc: <?php echo $ingredient['glucides']; ?>g, Lip: <?php echo $ingredient['lipides']; ?>g
											</small>
										</div>
									</div>
								<?php endforeach; ?>
							</div>

							<!-- No results message -->
							<div id="ingredientNoResults" class="text-center py-3 d-none">
								<i class="fas fa-search text-muted mr-2"></i>
								<span class="text-muted">Aucun ingrédient trouvé pour "<strong id="ingredientSearchTerm"></strong>"</span>
							</div>
						</div>

						<div class="form-group mt-5">
							<button type="submit" class="boxed-btn btn-block"><i class="fas fa-save"></i> Mettre à jour le repas</button>
							<a href="meal_list.php" class="btn btn-secondary btn-block mt-2">Annuler</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- end edit meal section -->

<script>
	// ── Live ingredient search ────────────────────────────────────────────
	const ingredientSearch     = document.getElementById('ingredientSearch');
	const ingredientCards      = document.querySelectorAll('.ingredient-card-wrap');
	const ingredientNoResults  = document.getElementById('ingredientNoResults');
	const ingredientSearchTerm = document.getElementById('ingredientSearchTerm');
	const ingredientSearchCount = document.getElementById('ingredientSearchCount');
	const totalIngredients     = ingredientCards.length;

	function filterIngredients() {
		const term    = ingredientSearch.value.trim().toLowerCase();
		let   visible = 0;

		ingredientCards.forEach(function (card) {
			const name = card.getAttribute('data-name');
			if (!term || name.includes(term)) {
				card.style.display = '';
				visible++;
			} else {
				card.style.display = 'none';
			}
		});

		// Update count text
		if (term) {
			ingredientSearchCount.textContent =
				visible + ' ingrédient' + (visible !== 1 ? 's' : '') +
				' trouvé' + (visible !== 1 ? 's' : '') +
				' sur ' + totalIngredients;
		} else {
			ingredientSearchCount.textContent = '';
		}

		// Show / hide no-results message
		if (visible === 0 && term) {
			ingredientSearchTerm.textContent = term;
			ingredientNoResults.classList.remove('d-none');
		} else {
			ingredientNoResults.classList.add('d-none');
		}
	}

	ingredientSearch.addEventListener('input', filterIngredients);

	// Clear search with Escape key
	ingredientSearch.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			ingredientSearch.value = '';
			filterIngredients();
			ingredientSearch.blur();
		}
	});
</script>

<style>
/* ── Animated gradient background ── */
body { background:linear-gradient(-45deg,#e8f5e9,#e3f2fd,#e0f7fa,#f1f8e9,#e8f5e9); background-size:400% 400%; animation:bgShift 16s ease infinite; }
@keyframes bgShift { 0%{background-position:0% 50%;} 25%{background-position:100% 50%;} 50%{background-position:100% 0%;} 75%{background-position:0% 100%;} 100%{background-position:0% 50%;} }
/* ── Floating particles ── */
.food-particles { position:fixed; inset:0; pointer-events:none; z-index:0; overflow:hidden; }
.food-particles span { position:absolute; bottom:-60px; opacity:0; animation:floatUp linear infinite; user-select:none; }
@keyframes floatUp { 0%{transform:translateY(0) rotate(0deg);opacity:0;} 10%{opacity:.45;} 90%{opacity:.25;} 100%{transform:translateY(-110vh) rotate(360deg);opacity:0;} }
/* ── Buttons lift + glow ── */
.boxed-btn { transition:transform .25s,box-shadow .25s !important; }
.boxed-btn:hover { transform:translateY(-3px) !important; box-shadow:0 8px 20px rgba(242,129,35,.35) !important; }
/* ── Section title fade-in ── */
.section-title { animation:titleFadeIn .6s ease both; }
@keyframes titleFadeIn { from{opacity:0;transform:translateY(-16px);} to{opacity:1;transform:translateY(0);} }
/* ── Cards lift ── */
.card { transition:transform .25s,box-shadow .25s; }
.card:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(0,0,0,.12); }
/* ── Form inputs focus glow ── */
.form-control:focus, .form-select:focus { border-color:#26a69a !important; box-shadow:0 0 0 3px rgba(38,166,154,.18) !important; outline:none; }
</style>

<?php include 'footer.php'; ?>
