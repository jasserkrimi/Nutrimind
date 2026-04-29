<?php
session_start();
require_once '../controllers/ObjectiveController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$objectiveController = new ObjectiveController();
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $objectiveController->create($_POST, $_SESSION['user_id']);

    if ($result['success']) {
        $_SESSION['success_message'] = "Objectif créé avec succès!";
        header('Location: objectif_list.php');
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<?php include 'header.php'; ?>

	<!-- create objective section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2">
					<div class="section-title text-center mb-5">
						<h3><span class="orange-text">Créer</span> un Objectif</h3>
						<p>Définissez un nouvel objectif pour atteindre vos buts</p>
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
							<label for="type_objectif" class="form-label font-weight-bold">Type d'objectif <span class="text-danger">*</span></label>
							<select class="form-control <?php echo isset($errors['type_objectif']) ? 'is-invalid' : ''; ?>"
								id="type_objectif" name="type_objectif" required>
								<option value="">Sélectionnez un type</option>
								<option value="perte_poids" <?php echo (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'perte_poids') ? 'selected' : ''; ?>>Perte de poids</option>
								<option value="prise_poids" <?php echo (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'prise_poids') ? 'selected' : ''; ?>>Prise de poids</option>
								<option value="maintien_poids" <?php echo (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'maintien_poids') ? 'selected' : ''; ?>>Maintien du poids</option>
								<option value="augmentation_muscle" <?php echo (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'augmentation_muscle') ? 'selected' : ''; ?>>Augmentation musculaire</option>
								<option value="amelioration_endurance" <?php echo (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'amelioration_endurance') ? 'selected' : ''; ?>>Amélioration de l'endurance</option>
								<option value="reduction_gras" <?php echo (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'reduction_gras') ? 'selected' : ''; ?>>Réduction du taux de graisse</option>
							</select>
							<?php if (isset($errors['type_objectif'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['type_objectif']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-group mb-4">
							<label for="valeur_cible" class="form-label font-weight-bold">Valeur cible <span class="text-danger">*</span></label>
							<input type="number" step="0.01" min="0.01" max="999.99" class="form-control <?php echo isset($errors['valeur_cible']) ? 'is-invalid' : ''; ?>"
								id="valeur_cible" name="valeur_cible" placeholder="Ex: 70.5 (kg)"
								value="<?php echo isset($_POST['valeur_cible']) ? htmlspecialchars($_POST['valeur_cible']) : ''; ?>" required>
							<small class="form-text text-muted">Entrez la valeur cible (poids en kg, etc.) - Entre 0.01 et 999.99</small>
							<?php if (isset($errors['valeur_cible'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['valeur_cible']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-group mb-4">
							<label for="poids_initial" class="form-label font-weight-bold">Poids initial (optionnel)</label>
							<input type="number" step="0.01" min="0.01" max="500" class="form-control"
								id="poids_initial" name="poids_initial" placeholder="Ex: 75.0"
								value="<?php echo isset($_POST['poids_initial']) ? htmlspecialchars($_POST['poids_initial']) : ''; ?>">
							<small class="form-text text-muted">Votre poids actuel en kg (entre 0.01 et 500)</small>
						</div>

						<div class="form-group mb-4">
							<label for="date_limite" class="form-label font-weight-bold">Date limite (optionnel)</label>
							<input type="date" class="form-control"
								id="date_limite" name="date_limite" min="<?php echo date('Y-m-d'); ?>"
								value="<?php echo isset($_POST['date_limite']) ? htmlspecialchars($_POST['date_limite']) : ''; ?>">
							<small class="form-text text-muted">Date à laquelle vous souhaitez atteindre l'objectif (doit être dans le futur)</small>
						</div>

						<div class="form-group mb-4">
							<label for="description" class="form-label font-weight-bold">Description (optionnel)</label>
							<textarea class="form-control" id="description" name="description" rows="4"
								placeholder="Décrivez votre objectif en détail..." maxlength="1000"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
							<div class="d-flex justify-content-between align-items-center mt-1">
								<small id="descError" class="text-danger" style="display:none;"></small>
								<small class="form-text text-muted ms-auto">
									<span id="charCount">0</span>/1000 caractères
								</small>
							</div>
						</div>

						<div class="form-group mb-4">
							<label for="statut" class="form-label font-weight-bold">Statut</label>
							<select class="form-control" id="statut" name="statut">
								<option value="en_attente" <?php echo (!isset($_POST['statut']) || $_POST['statut'] == 'en_attente') ? 'selected' : ''; ?>>En attente</option>
								<option value="en_cours" <?php echo (isset($_POST['statut']) && $_POST['statut'] == 'en_cours') ? 'selected' : ''; ?>>En cours</option>
								<option value="termine" <?php echo (isset($_POST['statut']) && $_POST['statut'] == 'termine') ? 'selected' : ''; ?>>Terminé</option>
								<option value="annule" <?php echo (isset($_POST['statut']) && $_POST['statut'] == 'annule') ? 'selected' : ''; ?>>Annulé</option>
							</select>
						</div>

						<div class="form-group mb-4">
							<label for="niveau_priorite" class="form-label font-weight-bold">Niveau de priorité</label>
							<select class="form-control" id="niveau_priorite" name="niveau_priorite">
								<option value="faible" <?php echo (isset($_POST['niveau_priorite']) && $_POST['niveau_priorite'] == 'faible') ? 'selected' : ''; ?>>Faible</option>
								<option value="moyen" <?php echo (!isset($_POST['niveau_priorite']) || $_POST['niveau_priorite'] == 'moyen') ? 'selected' : ''; ?>>Moyen</option>
								<option value="eleve" <?php echo (isset($_POST['niveau_priorite']) && $_POST['niveau_priorite'] == 'eleve') ? 'selected' : ''; ?>>Élevé</option>
							</select>
						</div>

						<div class="form-group text-center">
							<button type="submit" class="boxed-btn">Créer l'objectif</button>
							<a href="objectif_list.php" class="boxed-btn ml-3" style="background-color: #6c757d;">Annuler</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<style>
		.field-error { font-size: .82rem; color: #dc3545; margin-top: .25rem; display: none; }
		.form-control.is-invalid { border-color: #dc3545; box-shadow: 0 0 0 .15rem rgba(220,53,69,.15); }
		.form-control.is-valid   { border-color: #10b981; box-shadow: 0 0 0 .15rem rgba(16,185,129,.12); }
		#charCount { font-weight: 600; }
		#charCount.warn  { color: #f59e0b; }
		#charCount.error { color: #dc3545; }
		#charCount.ok    { color: #10b981; }
	</style>

	<script>
	(function () {

		// ── helpers ──────────────────────────────────────────────────────
		function showError(field, msgEl, msg) {
			field.classList.add('is-invalid');
			field.classList.remove('is-valid');
			msgEl.textContent = msg;
			msgEl.style.display = 'inline';
		}
		function showValid(field, msgEl) {
			field.classList.remove('is-invalid');
			field.classList.add('is-valid');
			msgEl.textContent = '';
			msgEl.style.display = 'none';
		}
		function clearState(field, msgEl) {
			field.classList.remove('is-invalid', 'is-valid');
			if (msgEl) { msgEl.textContent = ''; msgEl.style.display = 'none'; }
		}

		// ── type_objectif ────────────────────────────────────────────────
		const typeField = document.getElementById('type_objectif');
		const typeErr   = document.createElement('div');
		typeErr.className = 'field-error';
		typeField.parentNode.appendChild(typeErr);

		function validateType() {
			if (!typeField.value) {
				showError(typeField, typeErr, 'Veuillez sélectionner un type d\'objectif.');
				return false;
			}
			showValid(typeField, typeErr);
			return true;
		}
		typeField.addEventListener('change', validateType);

		// ── valeur_cible ─────────────────────────────────────────────────
		const valeurField = document.getElementById('valeur_cible');
		const valeurErr   = document.createElement('div');
		valeurErr.className = 'field-error';
		valeurField.parentNode.appendChild(valeurErr);

		function validateValeur() {
			const v = parseFloat(valeurField.value);
			if (valeurField.value === '') {
				showError(valeurField, valeurErr, 'La valeur cible est obligatoire.');
				return false;
			}
			if (isNaN(v) || v <= 0) {
				showError(valeurField, valeurErr, 'La valeur doit être un nombre positif.');
				return false;
			}
			if (v > 999.99) {
				showError(valeurField, valeurErr, 'La valeur ne peut pas dépasser 999.99.');
				return false;
			}
			showValid(valeurField, valeurErr);
			return true;
		}
		valeurField.addEventListener('input', validateValeur);

		// ── poids_initial ────────────────────────────────────────────────
		const poidsField = document.getElementById('poids_initial');
		const poidsErr   = document.createElement('div');
		poidsErr.className = 'field-error';
		poidsField.parentNode.appendChild(poidsErr);

		function validatePoids() {
			if (poidsField.value === '') { clearState(poidsField, poidsErr); return true; }
			const v = parseFloat(poidsField.value);
			if (isNaN(v) || v <= 0 || v > 500) {
				showError(poidsField, poidsErr, 'Le poids doit être entre 0.01 et 500 kg.');
				return false;
			}
			showValid(poidsField, poidsErr);
			return true;
		}
		poidsField.addEventListener('input', validatePoids);

		// ── date_limite ──────────────────────────────────────────────────
		const dateField = document.getElementById('date_limite');
		const dateErr   = document.createElement('div');
		dateErr.className = 'field-error';
		dateField.parentNode.appendChild(dateErr);

		function validateDate() {
			if (dateField.value === '') { clearState(dateField, dateErr); return true; }
			const selected = new Date(dateField.value);
			const today    = new Date();
			today.setHours(0, 0, 0, 0);
			if (selected < today) {
				showError(dateField, dateErr, 'La date limite doit être dans le futur.');
				return false;
			}
			showValid(dateField, dateErr);
			return true;
		}
		dateField.addEventListener('change', validateDate);

		// ── description ──────────────────────────────────────────────────
		const descField  = document.getElementById('description');
		const descErr    = document.getElementById('descError');
		const charCount  = document.getElementById('charCount');

		function validateDesc() {
			const len = descField.value.trim().length;
			charCount.textContent = descField.value.length;

			// colour counter
			charCount.className = len === 0 ? '' : len < 15 ? 'error' : len > 900 ? 'warn' : 'ok';

			if (descField.value.length > 0 && len < 15) {
				showError(descField, descErr, `La description doit contenir au moins 15 caractères (${len}/15).`);
				return false;
			}
			if (descField.value.length > 1000) {
				showError(descField, descErr, 'La description ne peut pas dépasser 1000 caractères.');
				return false;
			}
			clearState(descField, descErr);
			if (descField.value.length > 0) descField.classList.add('is-valid');
			return true;
		}
		descField.addEventListener('input', validateDesc);

		// ── form submit ──────────────────────────────────────────────────
		document.querySelector('form').addEventListener('submit', function (e) {
			const ok = [
				validateType(),
				validateValeur(),
				validatePoids(),
				validateDate(),
				validateDesc()
			].every(Boolean);

			if (!ok) {
				e.preventDefault();
				// Scroll to first error
				const first = document.querySelector('.is-invalid');
				if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
			}
		});

		// ── init on load ─────────────────────────────────────────────────
		document.addEventListener('DOMContentLoaded', function () {
			charCount.textContent = descField.value.length;
		});

	})();
	</script>

<?php include 'footer.php'; ?>