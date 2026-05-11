<?php
session_start();
require_once __DIR__ . '/../controllers/MealController.php';
require_once __DIR__ . '/../models/Ingredient.php';

$mealController = new MealController();
$ingredientModel = new Ingredient();
$ingredients = $ingredientModel->getAll();
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $mealController->create($_POST);
    
    if ($result['success']) {
        $_SESSION['success_message'] = "Repas créé avec succès!";
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

	<!-- create meal section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2">
					<div class="section-title text-center mb-5">	
						<h3><span class="orange-text">Créer</span> un Repas</h3>
						<p>Ajouter un nouveau repas à votre inventaire</p>
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

					<!-- Create Form -->
					<form method="POST" class="form">
						<div class="form-group mb-4">
							<label for="name" class="form-label font-weight-bold">Nom du repas <span class="text-danger">*</span></label>
							<div class="d-flex">
								<input type="text" class="form-control flex-grow-1 <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
									id="name" name="name" placeholder="Ex: Déjeuner du lundi" 
									value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
								<button type="button" class="btn btn-outline-primary ml-2" id="findRecipeBtn">🔍 Trouver recette automatiquement</button>
							</div>
							<!-- Validation error for recipe button -->
							<div id="recipeValidationError" class="recipe-validation-error d-none">
								<i class="fas fa-exclamation-circle mr-1"></i>
								Veuillez saisir un nom de repas avant de chercher une recette.
							</div>
							<?php if (isset($errors['name'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['name']; ?>
								</div>
							<?php endif; ?>
						</div>
						<div id="recipeResults" class="mt-3" style="display: none;"></div>

						<div class="form-group mb-4">
							<label for="date" class="form-label font-weight-bold">Date du repas <span class="text-danger">*</span></label>
							<input type="date" class="form-control <?php echo isset($errors['date']) ? 'is-invalid' : ''; ?>" 
								id="date" name="date" 
								value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>" required>
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
								maxlength="500"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
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
													<?php echo (isset($_POST['ingredients']) && in_array($ingredient['id'], $_POST['ingredients'])) ? 'checked' : ''; ?>>
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
													value="<?php echo isset($_POST['quantities'][$ingredient['id']]) ? htmlspecialchars($_POST['quantities'][$ingredient['id']]) : ''; ?>">
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
							<button type="submit" class="boxed-btn btn-block"><i class="fas fa-save"></i> Créer le repas</button>
							<a href="meal_list.php" class="btn btn-secondary btn-block mt-2">Annuler</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- end create meal section -->

	<script>
		const findRecipeBtn       = document.getElementById('findRecipeBtn');
		const nameInput           = document.getElementById('name');
		const recipeResults       = document.getElementById('recipeResults');
		const recipeValidationErr = document.getElementById('recipeValidationError');

		// ── Validation helpers ────────────────────────────────────────────────
		function showRecipeValidation() {
			recipeValidationErr.classList.remove('d-none');
			nameInput.classList.add('is-invalid-recipe');
			// Shake the button
			findRecipeBtn.classList.add('recipe-btn-shake');
			setTimeout(() => findRecipeBtn.classList.remove('recipe-btn-shake'), 500);
			// Auto-hide after 3 seconds
			setTimeout(hideRecipeValidation, 3000);
		}

		function hideRecipeValidation() {
			recipeValidationErr.classList.add('d-none');
			nameInput.classList.remove('is-invalid-recipe');
		}

		// Hide validation as soon as user starts typing
		nameInput.addEventListener('input', () => {
			if (nameInput.value.trim()) hideRecipeValidation();
		});

		// ── Recipe search ─────────────────────────────────────────────────────
		async function findRecipeSuggestion() {
			const input = nameInput.value.trim();
			recipeResults.style.display = 'none';
			recipeResults.innerHTML = '';

			// ── Validation ──
			if (!input) {
				showRecipeValidation();
				nameInput.focus();
				return;
			}

			hideRecipeValidation();

			// Show loading state on button
			findRecipeBtn.disabled = true;
			findRecipeBtn.textContent = '⏳ Recherche…';

			try {
				const datamuseResponse = await fetch(`https://api.datamuse.com/words?sl=${encodeURIComponent(input)}`);
				if (!datamuseResponse.ok) throw new Error('Erreur Datamuse');

				const words = await datamuseResponse.json();
				const correctedWord = (words && words.length > 0 && words[0].word) ? words[0].word : input;

				const mealResponse = await fetch(`https://www.themealdb.com/api/json/v1/1/search.php?s=${encodeURIComponent(correctedWord)}`);
				if (!mealResponse.ok) throw new Error('Erreur TheMealDB');

				const mealData = await mealResponse.json();

				if (!mealData.meals || mealData.meals.length === 0) {
					// Show inline "not found" message instead of alert
					recipeResults.innerHTML = `
						<div class="alert alert-warning">
							<i class="fas fa-search mr-2"></i>
							Aucune recette trouvée pour <strong>"${input}"</strong>. Essayez un autre nom.
						</div>`;
					recipeResults.style.display = 'block';
					return;
				}

				const meal = mealData.meals[0];
				const ingredients = [];
				for (let i = 1; i <= 20; i++) {
					const ingredient = meal[`strIngredient${i}`];
					const measure    = meal[`strMeasure${i}`];
					if (ingredient && ingredient.trim()) {
						ingredients.push(`${measure ? measure.trim() : ''} ${ingredient.trim()}`.trim());
					}
				}

				recipeResults.innerHTML = `
					<div class="card p-3">
						<div class="row">
							<div class="col-md-4 mb-3 mb-md-0">
								<img src="${meal.strMealThumb}" alt="${meal.strMeal}" class="img-fluid rounded">
							</div>
							<div class="col-md-8">
								<p><strong>Recette proposée :</strong> ${meal.strMeal}</p>
								<p><strong>Catégorie :</strong> ${meal.strCategory || '–'}</p>
								<p><strong>Ingrédients :</strong></p>
								<ul>${ingredients.map(item => `<li>${item}</li>`).join('')}</ul>
							</div>
						</div>
						<div class="mt-3">
							<p><strong>Instructions :</strong></p>
							<p>${meal.strInstructions ? meal.strInstructions.replace(/\n/g, '<br>') : 'Aucune instruction disponible.'}</p>
						</div>
					</div>`;
				recipeResults.style.display = 'block';

			} catch (error) {
				// Show inline error instead of alert
				recipeResults.innerHTML = `
					<div class="alert alert-danger">
						<i class="fas fa-exclamation-triangle mr-2"></i>
						Erreur lors de la récupération de la recette. Vérifiez votre connexion et réessayez.
					</div>`;
				recipeResults.style.display = 'block';
			} finally {
				findRecipeBtn.disabled = false;
				findRecipeBtn.innerHTML = '🔍 Trouver recette automatiquement';
			}
		}

		findRecipeBtn.addEventListener('click', findRecipeSuggestion);
	</script>

	<script>
		// ── Live ingredient search ────────────────────────────────────────────
		const ingredientSearch    = document.getElementById('ingredientSearch');
		const ingredientCards     = document.querySelectorAll('.ingredient-card-wrap');
		const ingredientNoResults = document.getElementById('ingredientNoResults');
		const ingredientSearchTerm = document.getElementById('ingredientSearchTerm');
		const ingredientSearchCount = document.getElementById('ingredientSearchCount');
		const totalIngredients    = ingredientCards.length;

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
		/* ── Recipe validation error ── */
		.recipe-validation-error {
			margin-top: 6px;
			color: #dc3545;
			font-size: 13px;
			font-weight: 600;
			background: #fde8ea;
			border: 1px solid #f5c6cb;
			border-radius: 6px;
			padding: 6px 14px;
			display: inline-block;
			animation: recFadeIn .25s ease-out;
		}
		@keyframes recFadeIn {
			from { opacity: 0; transform: translateY(-5px); }
			to   { opacity: 1; transform: translateY(0); }
		}
		/* Red border on input when empty */
		.is-invalid-recipe {
			border-color: #dc3545 !important;
			box-shadow: 0 0 0 .2rem rgba(220,53,69,.15) !important;
		}
		/* Shake animation on button */
		@keyframes recipe-shake {
			0%, 100% { transform: translateX(0); }
			20%       { transform: translateX(-5px); }
			40%       { transform: translateX(5px); }
			60%       { transform: translateX(-3px); }
			80%       { transform: translateX(3px); }
		}
		.recipe-btn-shake { animation: recipe-shake .4s ease-out; }

/* ── Animated gradient background ── */
body {
    background: linear-gradient(-45deg,#e8f5e9,#e3f2fd,#e0f7fa,#f1f8e9,#e8f5e9);
    background-size: 400% 400%;
    animation: bgShift 16s ease infinite;
}
@keyframes bgShift {
    0%  { background-position: 0%   50%; }
    25% { background-position: 100% 50%; }
    50% { background-position: 100% 0%;  }
    75% { background-position: 0%   100%;}
    100%{ background-position: 0%   50%; }
}
/* ── Floating food particles ── */
.food-particles { position:fixed; inset:0; pointer-events:none; z-index:0; overflow:hidden; }
.food-particles span { position:absolute; bottom:-60px; opacity:0; animation:floatUp linear infinite; user-select:none; }
@keyframes floatUp {
    0%  { transform:translateY(0) rotate(0deg);    opacity:0;   }
    10% { opacity:.45; }
    90% { opacity:.25; }
    100%{ transform:translateY(-110vh) rotate(360deg); opacity:0; }
}
/* ── Table glass card ── */
.table-responsive {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(8px);
    border-radius: 14px;
    box-shadow: 0 4px 24px rgba(0,0,0,.08);
    padding: 4px;
    transition: box-shadow .3s;
}
.table-responsive:hover { box-shadow: 0 8px 32px rgba(38,166,154,.18); }
/* ── Buttons lift + glow ── */
.boxed-btn { transition: transform .25s, box-shadow .25s !important; }
.boxed-btn:hover { transform: translateY(-3px) !important; box-shadow: 0 8px 20px rgba(242,129,35,.35) !important; }
/* ── Section title fade-in ── */
.section-title { animation: titleFadeIn .6s ease both; }
@keyframes titleFadeIn {
    from { opacity:0; transform:translateY(-16px); }
    to   { opacity:1; transform:translateY(0);     }
}
/* ── Card lift on hover ── */
.card { transition: transform .25s, box-shadow .25s; }
.card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.12); }
/* ── Form inputs focus glow ── */
.form-control:focus, .form-select:focus {
    border-color: #26a69a !important;
    box-shadow: 0 0 0 3px rgba(38,166,154,.18) !important;
    outline: none;
}
	</style>

<?php include 'footer.php'; ?>
