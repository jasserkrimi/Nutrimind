<?php
session_start();
require_once '../controllers/IngredientController.php';

$ingredientController = new IngredientController();

// ── Pagination parameters ─────────────────────────────────────────────────────
$perPage     = 10;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Handle delete BEFORE fetching so counts are correct after deletion
if (isset($_GET['delete'])) {
    $deleteId = htmlspecialchars($_GET['delete']);
    if ($ingredientController->delete($deleteId)) {
        $_SESSION['success_message'] = "Ingrédient supprimé avec succès!";
        header('Location: ingredient_list.php?page=' . $currentPage);
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression de l'ingrédient!";
    }
}

// Fetch paginated data + metadata
$paginationData = $ingredientController->getPaginated($currentPage, $perPage);
$ingredients    = $paginationData['ingredients'];
$totalItems     = $paginationData['totalItems'];
$totalPages     = $paginationData['totalPages'];
$currentPage    = $paginationData['currentPage']; // clamped value
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

			<!-- Search, Sort and Analyse button on same row -->
			<div class="row mb-4">
				<div class="col-md-5">
					<input type="text" id="ingredientsSearchInput" class="form-control" placeholder="Rechercher les ingrédients par nom...">
				</div>
				<div class="col-md-4">
					<select id="ingredientsSortSelect" class="form-select">
						<option value="name-asc">Trier par: Nom (A-Z)</option>
						<option value="name-desc">Trier par: Nom (Z-A)</option>
						<option value="calories-high">Trier par: Calories (Haut à Bas)</option>
						<option value="calories-low">Trier par: Calories (Bas à Haut)</option>
						<option value="protein-high">Trier par: Protéines (Haut à Bas)</option>
						<option value="protein-low">Trier par: Protéines (Bas à Haut)</option>
					</select>
				</div>
				<div class="col-md-3 mt-2 mt-md-0">
					<button id="analyseIaBtn" class="boxed-btn w-100">
						🔬 Analyser la santé de l'ingrédient
					</button>
				</div>
			</div>

			<!-- Selection hint + validation error -->
			<div class="row mb-2">
				<div class="col-lg-12">
					<small class="text-muted" id="selectionHint">
						<i class="fas fa-info-circle mr-1"></i>
						Sélectionnez un ingrédient dans le tableau puis cliquez sur le bouton pour analyser.
					</small>
					<div id="selectionError" class="ing-validation-error d-none">
						<i class="fas fa-exclamation-circle mr-1"></i>
						Veuillez sélectionner un ingrédient avant d'analyser.
					</div>
				</div>
			</div>

			<!-- Ingredients Table -->
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table class="table table-striped table-hover" id="ingredientsTable">
							<thead class="table-dark">
								<tr>
									<th style="width:40px;"></th>
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
										<td colspan="7" class="text-center"><em>Aucun ingrédient trouvé</em></td>
									</tr>
								<?php else: ?>
									<?php foreach ($ingredients as $ing): ?>
										<tr class="ing-row" data-name="<?php echo htmlspecialchars($ing['name']); ?>"
										    data-cal="<?php echo htmlspecialchars($ing['calories']); ?>"
										    data-prot="<?php echo htmlspecialchars($ing['proteins']); ?>"
										    data-gluc="<?php echo htmlspecialchars($ing['glucides']); ?>"
										    data-lip="<?php echo htmlspecialchars($ing['lipides']); ?>">
											<td>
												<input type="radio" name="selectedIngredient" class="ing-radio"
												       value="<?php echo htmlspecialchars($ing['name']); ?>"
												       data-cal="<?php echo htmlspecialchars($ing['calories']); ?>"
												       data-prot="<?php echo htmlspecialchars($ing['proteins']); ?>"
												       data-gluc="<?php echo htmlspecialchars($ing['glucides']); ?>"
												       data-lip="<?php echo htmlspecialchars($ing['lipides']); ?>">
											</td>
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

			<!-- ── Pagination info + navigation ─────────────────────────────── -->
			<?php if ($totalPages > 0): ?>
			<div class="row mt-3 align-items-center" id="paginationWrapper">

				<!-- Item count info -->
				<div class="col-md-4 text-center text-md-left mb-2 mb-md-0">
					<small class="text-muted">
						<?php
							$from = ($currentPage - 1) * $perPage + 1;
							$to   = min($currentPage * $perPage, $totalItems);
							echo "Affichage de $from à $to sur $totalItems ingrédients";
						?>
					</small>
				</div>

				<!-- Pagination nav -->
				<div class="col-md-8">
					<nav aria-label="Pagination des ingrédients">
						<ul class="pagination justify-content-center justify-content-md-end mb-0" id="ingredientsPagination">

							<!-- Previous button -->
							<li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
								<a class="page-link"
								   href="ingredient_list.php?page=<?php echo $currentPage - 1; ?>"
								   aria-label="Précédent">
									<span aria-hidden="true">&laquo;</span>
								</a>
							</li>

							<!-- Page numbers -->
							<?php for ($p = 1; $p <= $totalPages; $p++): ?>
								<li class="page-item <?php echo $p === $currentPage ? 'active' : ''; ?>">
									<a class="page-link" href="ingredient_list.php?page=<?php echo $p; ?>">
										<?php echo $p; ?>
									</a>
								</li>
							<?php endfor; ?>

							<!-- Next button -->
							<li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
								<a class="page-link"
								   href="ingredient_list.php?page=<?php echo $currentPage + 1; ?>"
								   aria-label="Suivant">
									<span aria-hidden="true">&raquo;</span>
								</a>
							</li>

						</ul>
					</nav>
				</div>

			</div>
			<?php endif; ?>
			<!-- ── End pagination ────────────────────────────────────────────── -->

		</div>
	</div>
	<!-- end ingredient section -->

<!-- ================================================================
     INGREDIENT HEALTH RISK ANALYZER — AI Modal (Groq · Llama 3)
================================================================ -->
<div id="healthModal" class="health-modal-overlay d-none" role="dialog" aria-modal="true">
	<div class="health-modal">

		<!-- Modal header -->
		<div class="health-modal-header">
			<div>
				<h4 class="health-modal-title">
					🔬 Analyse Santé IA
				</h4>
				<p class="health-modal-subtitle" id="healthModalIngName"></p>
			</div>
			<button class="health-modal-close" id="healthModalClose" aria-label="Fermer">&times;</button>
		</div>

		<!-- Nutrition quick-stats bar -->
		<div class="health-modal-stats" id="healthModalStats"></div>

		<!-- Loading -->
		<div id="healthLoading" class="health-loading">
			<div class="health-spinner"></div>
			<p>L'IA analyse cet ingrédient…</p>
		</div>

		<!-- Error -->
		<div id="healthError" class="alert alert-danger d-none mx-3 mb-3" role="alert">
			<i class="fas fa-exclamation-triangle mr-2"></i>
			<span id="healthErrorText"></span>
		</div>

		<!-- AI Result -->
		<div id="healthResult" class="health-result d-none"></div>

		<!-- Footer -->
		<div class="health-modal-footer">
			<small class="text-muted"><i class="fas fa-robot mr-1"></i>Analyse générée par Groq · Llama 3 — à titre indicatif uniquement.</small>
			<button class="btn btn-secondary btn-sm" id="healthModalClose2">Fermer</button>
		</div>

	</div>
</div>

<?php include 'footer.php'; ?>

<script>
	// ── Delete confirmation modal ─────────────────────────────────────────────
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

		const cancelBtn  = modal.querySelector('.delete-btn-cancel');
		const confirmBtn = modal.querySelector('.delete-btn-confirm');
		const closeModal = () => modal.remove();

		cancelBtn.addEventListener('click', closeModal);
		modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
		confirmBtn.addEventListener('click', () => {
			const page = new URLSearchParams(window.location.search).get('page') || 1;
			window.location.href = 'ingredient_list.php?delete=' + id + '&page=' + page;
		});
	}

	function reattachIngredientDeleteHandlers() {
		document.querySelectorAll('.delete-ingredient').forEach(btn => {
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				showDeleteModal(btn.getAttribute('data-id'));
			});
		});
	}
	reattachIngredientDeleteHandlers();

	// ================================================================
	// INGREDIENT HEALTH RISK ANALYZER
	// ================================================================
	const analyseBtn     = document.getElementById('analyseIaBtn');
	const selectionHint  = document.getElementById('selectionHint');
	const healthModal    = document.getElementById('healthModal');
	const healthLoading  = document.getElementById('healthLoading');
	const healthError    = document.getElementById('healthError');
	const healthErrorTxt = document.getElementById('healthErrorText');
	const healthResult   = document.getElementById('healthResult');
	const healthIngName  = document.getElementById('healthModalIngName');
	const healthStats    = document.getElementById('healthModalStats');

	let selectedIngredient = null;

	// ── Show / hide validation error ──────────────────────────────────────────
	const selectionError = document.getElementById('selectionError');

	function showValidationError() {
		selectionError.classList.remove('d-none');
		// Auto-hide after 3 seconds
		setTimeout(() => selectionError.classList.add('d-none'), 3000);
		// Shake the button
		analyseBtn.classList.add('ing-btn-shake');
		setTimeout(() => analyseBtn.classList.remove('ing-btn-shake'), 500);
	}

	function hideValidationError() {
		selectionError.classList.add('d-none');
	}

	// ── Update hint when a radio is selected ──────────────────────────────────
	document.addEventListener('change', (e) => {
		if (e.target.classList.contains('ing-radio')) {
			selectedIngredient = {
				name : e.target.value,
				cal  : e.target.dataset.cal,
				prot : e.target.dataset.prot,
				gluc : e.target.dataset.gluc,
				lip  : e.target.dataset.lip,
			};
			hideValidationError();
			selectionHint.innerHTML =
				`<i class="fas fa-check-circle text-success mr-1"></i>
				 Ingrédient sélectionné : <strong>${selectedIngredient.name}</strong> — cliquez sur le bouton pour analyser.`;
		}
	});

	// ── Click on a row also selects its radio ─────────────────────────────────
	document.addEventListener('click', (e) => {
		const row = e.target.closest('.ing-row');
		if (row) {
			const radio = row.querySelector('.ing-radio');
			if (radio) { radio.checked = true; radio.dispatchEvent(new Event('change', {bubbles:true})); }
		}
	});

	// ── Open modal and call Groq ──────────────────────────────────────────────
	analyseBtn.addEventListener('click', async () => {

		// ── Validation: ingredient must be selected ──
		if (!selectedIngredient) {
			showValidationError();
			return;
		}

		// Reset modal state
		healthError.classList.add('d-none');
		healthResult.classList.add('d-none');
		healthLoading.classList.remove('d-none');
		healthIngName.textContent = selectedIngredient.name;

		// Nutrition quick-stats bar
		healthStats.innerHTML = `
			<div class="health-stat"><span class="health-stat-val">${selectedIngredient.cal}</span><span class="health-stat-lbl">kcal</span></div>
			<div class="health-stat"><span class="health-stat-val">${selectedIngredient.prot}g</span><span class="health-stat-lbl">Protéines</span></div>
			<div class="health-stat"><span class="health-stat-val">${selectedIngredient.gluc}g</span><span class="health-stat-lbl">Glucides</span></div>
			<div class="health-stat"><span class="health-stat-val">${selectedIngredient.lip}g</span><span class="health-stat-lbl">Lipides</span></div>
		`;

		// Show modal
		healthModal.classList.remove('d-none');
		document.body.style.overflow = 'hidden';

		// Build the AI prompt
		const prompt =
			`Analyse de santé approfondie pour l'ingrédient : "${selectedIngredient.name}"\n` +
			`Données nutritionnelles (pour 100g) : ${selectedIngredient.cal} kcal, ` +
			`${selectedIngredient.prot}g protéines, ${selectedIngredient.gluc}g glucides, ${selectedIngredient.lip}g lipides.\n\n` +
			`Fournis une analyse structurée avec exactement ces 5 sections :\n\n` +
			`1. 🔥 Inflammatoire ou Anti-inflammatoire ?\n` +
			`   Explique si cet ingrédient favorise ou réduit l'inflammation dans le corps.\n\n` +
			`2. 📊 Indice Glycémique\n` +
			`   Donne l'indice glycémique approximatif (bas/moyen/élevé) et son impact sur la glycémie.\n\n` +
			`3. ⚠️ Qui doit éviter cet ingrédient ?\n` +
			`   Mentionne les personnes à risque : diabétiques, hypertendus, allergiques, femmes enceintes, etc.\n\n` +
			`4. ⏰ Meilleur moment pour le consommer\n` +
			`   Matin, midi, soir, avant/après sport — et pourquoi.\n\n` +
			`5. 🤝 Avec quoi le combiner nutritionnellement ?\n` +
			`   Cite 3 aliments qui se marient bien avec lui pour maximiser l'absorption des nutriments.`;

		try {
			const res  = await fetch('../controllers/api_groq.php', {
				method  : 'POST',
				headers : { 'Content-Type': 'application/json' },
				body    : JSON.stringify({ prompt }),
			});
			const data = await res.json();

			if (!res.ok || data.error) throw new Error(data.error || 'Erreur API Groq');

			// Render response — bold markdown + newlines
			healthResult.innerHTML = data.response
				.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
				.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
				.replace(/\n/g, '<br>');
			healthResult.classList.remove('d-none');

		} catch (err) {
			healthErrorTxt.textContent = err.message;
			healthError.classList.remove('d-none');
		} finally {
			healthLoading.classList.add('d-none');
		}
	});

	// ── Close modal ───────────────────────────────────────────────────────────
	function closeHealthModal() {
		healthModal.classList.add('d-none');
		document.body.style.overflow = '';
	}
	document.getElementById('healthModalClose').addEventListener('click',  closeHealthModal);
	document.getElementById('healthModalClose2').addEventListener('click', closeHealthModal);
	healthModal.addEventListener('click', (e) => { if (e.target === healthModal) closeHealthModal(); });
	document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeHealthModal(); });

	// ── Search & Sort (operates on current page rows) ─────────────────────────
	const ingredientsSearchInput = document.getElementById('ingredientsSearchInput');
	const ingredientsSortSelect  = document.getElementById('ingredientsSortSelect');
	const ingredientsTableBody   = document.getElementById('ingredientsTableBody');
	const paginationWrapper      = document.getElementById('paginationWrapper');
	let ingredientsData = [];

	function initializeIngredientsData() {
		ingredientsData = [];
		ingredientsTableBody.querySelectorAll('tr').forEach(row => {
			const cells = row.querySelectorAll('td');
			// cells[0] = radio, cells[1] = name, cells[2] = cal, cells[3] = prot, cells[4] = gluc, cells[5] = lip, cells[6] = actions
			if (cells.length > 0 && cells[1] && cells[1].textContent.trim() !== 'Aucun ingrédient trouvé') {
				ingredientsData.push({
					name:     cells[1].textContent.trim(),
					calories: parseFloat(cells[2].textContent.trim()) || 0,
					proteins: parseFloat(cells[3].textContent.trim()) || 0,
					glucides: cells[4].textContent.trim(),
					lipides:  cells[5].textContent.trim(),
					actions:  cells[6] ? cells[6].innerHTML : '',
					radioHtml: cells[0].innerHTML,
				});
			}
		});
	}

	function filterAndSortIngredients() {
		const searchTerm = ingredientsSearchInput.value.toLowerCase();
		const sortValue  = ingredientsSortSelect.value;

		let filtered = ingredientsData.filter(i => i.name.toLowerCase().includes(searchTerm));

		if (sortValue === 'name-asc')         filtered.sort((a, b) => a.name.localeCompare(b.name));
		else if (sortValue === 'name-desc')   filtered.sort((a, b) => b.name.localeCompare(a.name));
		else if (sortValue === 'calories-high') filtered.sort((a, b) => b.calories - a.calories);
		else if (sortValue === 'calories-low')  filtered.sort((a, b) => a.calories - b.calories);
		else if (sortValue === 'protein-high')  filtered.sort((a, b) => b.proteins - a.proteins);
		else if (sortValue === 'protein-low')   filtered.sort((a, b) => a.proteins - b.proteins);

		if (filtered.length === 0) {
			ingredientsTableBody.innerHTML = '<tr><td colspan="7" class="text-center"><em>Aucun ingrédient trouvé</em></td></tr>';
		} else {
			ingredientsTableBody.innerHTML = filtered.map(i =>
				`<tr class="ing-row">
					<td>${i.radioHtml}</td>
					<td>${i.name}</td><td>${i.calories}</td><td>${i.proteins}</td>
					<td>${i.glucides}</td><td>${i.lipides}</td><td>${i.actions}</td>
				</tr>`
			).join('');
			reattachIngredientDeleteHandlers();
		}

		// Hide pagination nav while filtering so numbers don't mislead
		if (paginationWrapper) {
			paginationWrapper.style.display = searchTerm ? 'none' : '';
		}
	}

	if (ingredientsSearchInput && ingredientsSortSelect) {
		ingredientsSearchInput.addEventListener('input', filterAndSortIngredients);
		ingredientsSortSelect.addEventListener('change', filterAndSortIngredients);
		initializeIngredientsData();
	}
</script>

<style>
/* ============================================================
   INGREDIENT LIST — Modern Animated UI
   1. Animated gradient background (green → blue → cyan)
   2. Floating food particles
   3. Table glass card + row stagger + hover lift
   4. Button lift + glow
   5. Section title fade-in
   6. Search/sort focus glow
   7. Validation, row selection, health modal, pagination, delete modal
============================================================ */

/* 1. Animated gradient background */
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

/* 2. Floating food particles */
.food-particles { position:fixed; inset:0; pointer-events:none; z-index:0; overflow:hidden; }
.food-particles span { position:absolute; bottom:-60px; opacity:0; animation:floatUp linear infinite; user-select:none; }
@keyframes floatUp {
    0%  { transform:translateY(0) rotate(0deg);    opacity:0;   }
    10% { opacity:.45; }
    90% { opacity:.25; }
    100%{ transform:translateY(-110vh) rotate(360deg); opacity:0; }
}

/* 3. Table wrapper — glass card */
.table-responsive {
    background: rgba(255,255,255,.82);
    backdrop-filter: blur(8px);
    border-radius: 14px;
    box-shadow: 0 4px 24px rgba(0,0,0,.08);
    padding: 4px;
    transition: box-shadow .3s;
}
.table-responsive:hover { box-shadow: 0 8px 32px rgba(38,166,154,.18); }

/* 3b. Row staggered fade-in + hover lift */
#ingredientsTableBody tr {
    animation: rowFadeIn .45s ease both;
    transition: background .2s, transform .2s, box-shadow .2s;
}
#ingredientsTableBody tr:hover {
    background: rgba(232,245,233,.85) !important;
    transform: translateX(4px);
    box-shadow: inset 4px 0 0 #26a69a;
}
@keyframes rowFadeIn {
    from { opacity:0; transform:translateY(10px); }
    to   { opacity:1; transform:translateY(0);    }
}
#ingredientsTableBody tr:nth-child(1)  { animation-delay:.05s; }
#ingredientsTableBody tr:nth-child(2)  { animation-delay:.10s; }
#ingredientsTableBody tr:nth-child(3)  { animation-delay:.15s; }
#ingredientsTableBody tr:nth-child(4)  { animation-delay:.20s; }
#ingredientsTableBody tr:nth-child(5)  { animation-delay:.25s; }
#ingredientsTableBody tr:nth-child(6)  { animation-delay:.30s; }
#ingredientsTableBody tr:nth-child(7)  { animation-delay:.35s; }
#ingredientsTableBody tr:nth-child(8)  { animation-delay:.40s; }
#ingredientsTableBody tr:nth-child(9)  { animation-delay:.45s; }
#ingredientsTableBody tr:nth-child(10) { animation-delay:.50s; }

/* 4. Buttons — lift + glow */
.boxed-btn { transition: transform .25s, box-shadow .25s !important; }
.boxed-btn:hover { transform: translateY(-3px) !important; box-shadow: 0 8px 20px rgba(242,129,35,.35) !important; }
#analyseIaBtn { transition: transform .25s, box-shadow .25s !important; }
#analyseIaBtn:hover { transform: translateY(-3px) !important; box-shadow: 0 8px 20px rgba(242,129,35,.35) !important; }

/* 5. Section title fade-in */
.section-title { animation: titleFadeIn .6s ease both; }
@keyframes titleFadeIn {
    from { opacity:0; transform:translateY(-16px); }
    to   { opacity:1; transform:translateY(0);     }
}

/* 6. Search & sort focus glow */
#ingredientsSearchInput, #ingredientsSortSelect {
    transition: border-color .25s, box-shadow .25s;
    border-radius: 8px !important;
}
#ingredientsSearchInput:focus, #ingredientsSortSelect:focus {
    border-color: #26a69a !important;
    box-shadow: 0 0 0 3px rgba(38,166,154,.18) !important;
    outline: none;
}

/* 7a. Validation error */
.ing-validation-error { display:inline-block; margin-top:8px; color:#dc3545; font-size:13px; font-weight:600; background:#fde8ea; border:1px solid #f5c6cb; border-radius:6px; padding:6px 14px; animation:fadeInDown .25s ease-out; }
@keyframes fadeInDown { from{opacity:0;transform:translateY(-6px);} to{opacity:1;transform:translateY(0);} }
@keyframes ing-shake { 0%,100%{transform:translateX(0);} 20%{transform:translateX(-6px);} 40%{transform:translateX(6px);} 60%{transform:translateX(-4px);} 80%{transform:translateX(4px);} }
.ing-btn-shake { animation: ing-shake .4s ease-out; }

/* 7b. Row selection */
.ing-row { cursor:pointer; transition:background .15s; }
.ing-row:hover { background:#fff8f2 !important; }
.ing-radio { accent-color:#f28123; width:16px; height:16px; cursor:pointer; }

/* 7c. Health Modal */
.health-modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.55); display:flex; align-items:center; justify-content:center; z-index:11000; padding:16px; }
.health-modal { background:#fff; border-radius:14px; box-shadow:0 8px 40px rgba(0,0,0,.22); width:100%; max-width:680px; max-height:88vh; display:flex; flex-direction:column; animation:hmSlideIn .3s ease-out; overflow:hidden; }
@keyframes hmSlideIn { from{transform:translateY(-40px);opacity:0;} to{transform:translateY(0);opacity:1;} }
.health-modal-header { display:flex; align-items:flex-start; justify-content:space-between; padding:20px 24px 14px; border-bottom:2px solid #f5f5f5; flex-shrink:0; }
.health-modal-title { font-size:20px; font-weight:700; color:#2c2c2c; margin:0 0 4px; }
.health-modal-subtitle { font-size:16px; color:#f28123; font-weight:600; margin:0; }
.health-modal-close { background:none; border:none; font-size:28px; color:#aaa; cursor:pointer; line-height:1; padding:0 4px; transition:color .2s; }
.health-modal-close:hover { color:#333; }
.health-modal-stats { display:flex; flex-shrink:0; border-bottom:2px solid #f5f5f5; }
.health-stat { flex:1; text-align:center; padding:10px 8px; border-right:1px solid #f0f0f0; }
.health-stat:last-child { border-right:none; }
.health-stat-val { display:block; font-size:18px; font-weight:700; color:#f28123; }
.health-stat-lbl { display:block; font-size:11px; color:#999; text-transform:uppercase; letter-spacing:.4px; margin-top:2px; }
.health-loading { text-align:center; padding:32px 20px; flex-shrink:0; }
.health-spinner { width:44px; height:44px; border:5px solid #f0e0d0; border-top-color:#f28123; border-radius:50%; animation:spin .8s linear infinite; margin:0 auto 14px; }
@keyframes spin { to{transform:rotate(360deg);} }
.health-loading p { color:#777; font-size:14px; margin:0; }
.health-result { padding:20px 24px; font-size:14px; line-height:1.85; color:#333; overflow-y:auto; flex:1; }
.health-modal-footer { display:flex; align-items:center; justify-content:space-between; padding:12px 24px; border-top:2px solid #f5f5f5; flex-shrink:0; background:#fafafa; }

/* 7d. Pagination */
.pagination .page-link { color:#f28123; border-color:#dee2e6; transition:background .2s,color .2s,transform .2s; border-radius:6px !important; }
.pagination .page-link:hover { background-color:#f28123; border-color:#f28123; color:#fff; transform:translateY(-2px); }
.pagination .page-item.active .page-link { background-color:#f28123; border-color:#f28123; color:#fff; }
.pagination .page-item.disabled .page-link { color:#aaa; }

/* 7e. Delete modal */
.delete-modal-overlay { position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center; z-index:10000; }
.delete-modal { background:white; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,.25); animation:slideIn .3s ease-out; }
.delete-modal-content { padding:30px; min-width:400px; text-align:center; }
.delete-modal-content h3 { margin:0 0 15px; color:#333; font-size:22px; }
.delete-modal-content p  { margin:0 0 30px; color:#666; font-size:16px; }
.delete-modal-buttons { display:flex; gap:10px; justify-content:center; }
.delete-btn-cancel,.delete-btn-confirm { padding:12px 30px; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-size:14px; transition:all .25s; }
.delete-btn-cancel  { background-color:#e0e0e0; color:#333; }
.delete-btn-cancel:hover  { background-color:#d0d0d0; transform:translateY(-2px); }
.delete-btn-confirm { background-color:#dc3545; color:white; }
.delete-btn-confirm:hover { background-color:#c82333; transform:translateY(-2px); box-shadow:0 4px 12px rgba(220,53,69,.4); }
@keyframes slideIn { from{transform:translateY(-40px);opacity:0;} to{transform:translateY(0);opacity:1;} }
@media(max-width:480px){ .delete-modal-content{min-width:300px;} .delete-modal-buttons{flex-direction:column;} }
</style>
