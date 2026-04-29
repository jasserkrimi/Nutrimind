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
							<div class="row">
								<?php foreach ($ingredients as $ingredient): ?>
									<div class="col-md-6 mb-3">
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
		async function findRecipeSuggestion() {
			const input = document.getElementById('name').value.trim();
			const resultsDiv = document.getElementById('recipeResults');
			resultsDiv.style.display = 'none';
			resultsDiv.innerHTML = '';

			if (!input) {
				alert('Veuillez entrer un nom de repas.');
				return;
			}

			try {
				const datamuseResponse = await fetch(`https://api.datamuse.com/words?sl=${encodeURIComponent(input)}`);
				if (!datamuseResponse.ok) {
					throw new Error('Erreur Datamuse');
				}
				const words = await datamuseResponse.json();
				const correctedWord = (words && words.length > 0 && words[0].word) ? words[0].word : input;

				const mealResponse = await fetch(`https://www.themealdb.com/api/json/v1/1/search.php?s=${encodeURIComponent(correctedWord)}`);
				if (!mealResponse.ok) {
					throw new Error('Erreur TheMealDB');
				}
				const mealData = await mealResponse.json();

				if (!mealData.meals || mealData.meals.length === 0) {
					alert('Aucune recette trouvée pour ce nom.');
					return;
				}

				const meal = mealData.meals[0];
				const ingredients = [];
				for (let i = 1; i <= 20; i++) {
					const ingredient = meal[`strIngredient${i}`];
					const measure = meal[`strMeasure${i}`];
					if (ingredient && ingredient.trim()) {
						ingredients.push(`${measure ? measure.trim() : ''} ${ingredient.trim()}`.trim());
					}
				}

				resultsDiv.innerHTML = `
					<div class="card p-3">
						<div class="row">
							<div class="col-md-4 mb-3 mb-md-0">
								<img src="${meal.strMealThumb}" alt="${meal.strMeal}" class="img-fluid">
							</div>
							<div class="col-md-8">
								<p><strong>Recette proposée :</strong> ${meal.strMeal}</p>
								<p><strong>Catégorie :</strong> ${meal.strCategory || '–'}</p>
								<p><strong>Ingrédients :</strong></p>
								<ul>
									${ingredients.map(item => `<li>${item}</li>`).join('')}
								</ul>
							</div>
						</div>
						<div class="mt-3">
							<p><strong>Instructions :</strong></p>
							<p>${meal.strInstructions ? meal.strInstructions.replace(/\n/g, '<br>') : 'Aucune instruction disponible.'}</p>
						</div>
					</div>
				`;
				resultsDiv.style.display = 'block';
			} catch (error) {
				alert('Erreur lors de la récupération de la recette.');
			}
		}

		document.getElementById('findRecipeBtn').addEventListener('click', findRecipeSuggestion);
	</script>

<?php include 'footer.php'; ?>
