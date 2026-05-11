<?php
session_start();
require_once '../controllers/MealController.php';

$mealController = new MealController();

$perPage     = 10;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if (isset($_GET['delete'])) {
    $deleteId = htmlspecialchars($_GET['delete']);
    if ($mealController->delete($deleteId)) {
        $_SESSION['success_message'] = "Repas supprime avec succes!";
        header('Location: meal_list.php?page=' . $currentPage);
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du repas!";
    }
}

$paginationData = $mealController->getPaginated($currentPage, $perPage);
$meals          = $paginationData['meals'];
$totalItems     = $paginationData['totalItems'];
$totalPages     = $paginationData['totalPages'];
$currentPage    = $paginationData['currentPage'];
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

<?php if (isset($_SESSION['success_message'])): ?>
<div class="row mb-4"><div class="col-lg-12">
<div class="alert alert-success alert-dismissible fade show" role="alert">
<?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div></div></div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
<div class="row mb-4"><div class="col-lg-12">
<div class="alert alert-danger alert-dismissible fade show" role="alert">
<?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div></div></div>
<?php endif; ?>

<!-- Action buttons -->
<div class="row mb-4">
<div class="col-lg-12">
<div class="meal-btn-group">
<a href="meal_create.php"       class="boxed-btn"><i class="fas fa-plus"></i> Ajouter un repas</a>
<a href="ai_nutrition.php"      class="boxed-btn">📸 Analyse Nutrition IA</a>
<a href="statistics.php"        class="boxed-btn">📊 Statistiques</a>
<a href="anomaly_detector.php"  class="boxed-btn">🚨 Détecteur d'Anomalies</a>
</div>
</div>
</div>

<!-- Search and Sort -->
<div class="row mb-4">
<div class="col-md-6">
<input type="text" id="mealsSearchInput" class="form-control" placeholder="Rechercher les repas par nom...">
</div>
<div class="col-md-6">
<select id="mealsSortSelect" class="form-select">
<option value="name-asc">Trier par: Nom (A-Z)</option>
<option value="name-desc">Trier par: Nom (Z-A)</option>
<option value="date-newest">Trier par: Date (Plus récent)</option>
<option value="date-oldest">Trier par: Date (Plus ancien)</option>
</select>
</div>
</div>

<!-- Meals Table -->
<div class="row">
<div class="col-lg-12">
<div class="table-responsive">
<table class="table table-striped table-hover" id="mealsTable">
<thead class="table-dark">
<tr><th>Nom</th><th>Date</th><th>Notes</th><th>Actions</th></tr>
</thead>
<tbody id="mealsTableBody">
<?php if (empty($meals)): ?>
<tr><td colspan="4" class="text-center"><em>Aucun repas trouvé</em></td></tr>
<?php else: ?>
<?php foreach ($meals as $meal): ?>
<tr>
<td><?php echo htmlspecialchars($meal['name']); ?></td>
<td><?php echo htmlspecialchars($meal['date']); ?></td>
<td><?php echo htmlspecialchars(substr($meal['notes'],0,50)).(strlen($meal['notes'])>50?'...':''); ?></td>
<td>
<a href="meal_edit.php?id=<?php echo htmlspecialchars($meal['id']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Modifier</a>
<a href="#" class="btn btn-sm btn-danger delete-meal" data-id="<?php echo htmlspecialchars($meal['id']); ?>"><i class="fas fa-trash"></i> Supprimer</a>
</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 0): ?>
<div class="row mt-3 align-items-center" id="paginationWrapper">
<div class="col-md-4 text-center text-md-left mb-2 mb-md-0">
<small class="text-muted">
<?php
$from = ($currentPage-1)*$perPage+1;
$to   = min($currentPage*$perPage,$totalItems);
echo "Affichage de $from à $to sur $totalItems repas";
?>
</small>
</div>
<div class="col-md-8">
<nav aria-label="Pagination des repas">
<ul class="pagination justify-content-center justify-content-md-end mb-0">
<li class="page-item <?php echo $currentPage<=1?'disabled':''; ?>">
<a class="page-link" href="meal_list.php?page=<?php echo $currentPage-1; ?>">&laquo;</a>
</li>
<?php for ($p=1;$p<=$totalPages;$p++): ?>
<li class="page-item <?php echo $p===$currentPage?'active':''; ?>">
<a class="page-link" href="meal_list.php?page=<?php echo $p; ?>"><?php echo $p; ?></a>
</li>
<?php endfor; ?>
<li class="page-item <?php echo $currentPage>=$totalPages?'disabled':''; ?>">
<a class="page-link" href="meal_list.php?page=<?php echo $currentPage+1; ?>">&raquo;</a>
</li>
</ul>
</nav>
</div>
</div>
<?php endif; ?>

</div>
</div>

<?php include 'footer.php'; ?>

<script>
function showDeleteModal(id) {
    const modal = document.createElement('div');
    modal.className = 'delete-modal-overlay';
    modal.innerHTML = `<div class="delete-modal"><div class="delete-modal-content">
        <h3>Confirmer la Suppression</h3>
        <p>Êtes-vous sûr de vouloir supprimer ce repas? Cette action est irréversible.</p>
        <div class="delete-modal-buttons">
            <button class="delete-btn-cancel">Annuler</button>
            <button class="delete-btn-confirm">Supprimer</button>
        </div></div></div>`;
    document.body.appendChild(modal);
    const closeModal = () => modal.remove();
    modal.querySelector('.delete-btn-cancel').addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => { if (e.target===modal) closeModal(); });
    modal.querySelector('.delete-btn-confirm').addEventListener('click', () => {
        const page = new URLSearchParams(window.location.search).get('page') || 1;
        window.location.href = 'meal_list.php?delete=' + id + '&page=' + page;
    });
}
function reattachMealDeleteHandlers() {
    document.querySelectorAll('.delete-meal').forEach(btn => {
        btn.addEventListener('click', (e) => { e.preventDefault(); showDeleteModal(btn.getAttribute('data-id')); });
    });
}
reattachMealDeleteHandlers();

const mealsSearchInput  = document.getElementById('mealsSearchInput');
const mealsSortSelect   = document.getElementById('mealsSortSelect');
const mealsTableBody    = document.getElementById('mealsTableBody');
const paginationWrapper = document.getElementById('paginationWrapper');
let mealsData = [];

function initializeMealsData() {
    mealsData = [];
    mealsTableBody.querySelectorAll('tr').forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 0 && cells[0].textContent.trim() !== 'Aucun repas trouvé') {
            mealsData.push({ name: cells[0].textContent.trim(), date: cells[1].textContent.trim(), notes: cells[2].textContent.trim(), actions: cells[3].innerHTML });
        }
    });
}

function filterAndSortMeals() {
    const searchTerm = mealsSearchInput.value.toLowerCase();
    const sortValue  = mealsSortSelect.value;
    let filtered = mealsData.filter(m => m.name.toLowerCase().includes(searchTerm));
    if (sortValue==='name-asc')         filtered.sort((a,b)=>a.name.localeCompare(b.name));
    else if (sortValue==='name-desc')   filtered.sort((a,b)=>b.name.localeCompare(a.name));
    else if (sortValue==='date-newest') filtered.sort((a,b)=>new Date(b.date)-new Date(a.date));
    else if (sortValue==='date-oldest') filtered.sort((a,b)=>new Date(a.date)-new Date(b.date));
    if (filtered.length===0) {
        mealsTableBody.innerHTML = '<tr><td colspan="4" class="text-center"><em>Aucun repas trouvé</em></td></tr>';
    } else {
        mealsTableBody.innerHTML = filtered.map(m=>`<tr><td>${m.name}</td><td>${m.date}</td><td>${m.notes}</td><td>${m.actions}</td></tr>`).join('');
        reattachMealDeleteHandlers();
    }
    if (paginationWrapper) paginationWrapper.style.display = searchTerm ? 'none' : '';
}

if (mealsSearchInput && mealsSortSelect) {
    mealsSearchInput.addEventListener('input', filterAndSortMeals);
    mealsSortSelect.addEventListener('change', filterAndSortMeals);
    initializeMealsData();
}
</script>

<style>
/* ============================================================
   MEAL LIST — Modern Animated UI
   1. Animated gradient background (green → blue → cyan)
   2. Floating food particles
   3. Table glass card + row stagger + hover lift
   4. Button lift + glow
   5. Section title fade-in
   6. Search/sort focus glow
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
#mealsTableBody tr {
    animation: rowFadeIn .45s ease both;
    transition: background .2s, transform .2s, box-shadow .2s;
}
#mealsTableBody tr:hover {
    background: rgba(232,245,233,.85) !important;
    transform: translateX(4px);
    box-shadow: inset 4px 0 0 #26a69a;
}
@keyframes rowFadeIn {
    from { opacity:0; transform:translateY(10px); }
    to   { opacity:1; transform:translateY(0);    }
}
#mealsTableBody tr:nth-child(1)  { animation-delay:.05s; }
#mealsTableBody tr:nth-child(2)  { animation-delay:.10s; }
#mealsTableBody tr:nth-child(3)  { animation-delay:.15s; }
#mealsTableBody tr:nth-child(4)  { animation-delay:.20s; }
#mealsTableBody tr:nth-child(5)  { animation-delay:.25s; }
#mealsTableBody tr:nth-child(6)  { animation-delay:.30s; }
#mealsTableBody tr:nth-child(7)  { animation-delay:.35s; }
#mealsTableBody tr:nth-child(8)  { animation-delay:.40s; }
#mealsTableBody tr:nth-child(9)  { animation-delay:.45s; }
#mealsTableBody tr:nth-child(10) { animation-delay:.50s; }

/* 4. Buttons — lift + glow */
.boxed-btn { transition: transform .25s, box-shadow .25s !important; }
.boxed-btn:hover { transform: translateY(-3px) !important; box-shadow: 0 8px 20px rgba(242,129,35,.35) !important; }

/* 5. Section title fade-in */
.section-title { animation: titleFadeIn .6s ease both; }
@keyframes titleFadeIn {
    from { opacity:0; transform:translateY(-16px); }
    to   { opacity:1; transform:translateY(0);     }
}

/* 6. Search & sort focus glow */
#mealsSearchInput, #mealsSortSelect {
    transition: border-color .25s, box-shadow .25s;
    border-radius: 8px !important;
}
#mealsSearchInput:focus, #mealsSortSelect:focus {
    border-color: #26a69a !important;
    box-shadow: 0 0 0 3px rgba(38,166,154,.18) !important;
    outline: none;
}

/* ── Button group ── */
.meal-btn-group { display:flex; flex-wrap:wrap; gap:10px; justify-content:center; }
.meal-btn-group .boxed-btn { margin:0; }
@media(max-width:576px){ .meal-btn-group .boxed-btn { width:100%; text-align:center; } }

/* ── Pagination ── */
.pagination .page-link { color:#f28123; border-color:#dee2e6; transition:background .2s,color .2s,transform .2s; border-radius:6px !important; }
.pagination .page-link:hover { background-color:#f28123; border-color:#f28123; color:#fff; transform:translateY(-2px); }
.pagination .page-item.active .page-link { background-color:#f28123; border-color:#f28123; color:#fff; }
.pagination .page-item.disabled .page-link { color:#aaa; }

/* ── Delete modal ── */
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

