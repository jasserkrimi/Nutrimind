<?php
session_start();
require_once '../controllers/IngredientController.php';

$ingredientController = new IngredientController();
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $ingredientController->create($_POST);
    
    if ($result['success']) {
        $_SESSION['success_message'] = "Ingrédient créé avec succès!";
        header('Location: ingredient_list.php');
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

	<!-- create ingredient section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2">
					<div class="section-title text-center mb-5">	
						<h3><span class="orange-text">Créer</span> un Ingrédient</h3>
						<p>Ajouter un nouvel ingrédient à votre inventaire</p>
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
						<label for="name" class="form-label font-weight-bold">Nom de l'ingrédient <span class="text-danger">*</span></label>
						<input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
							id="name" name="name" placeholder="Ex: Poitrine de poulet" 
								value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
							<?php if (isset($errors['name'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['name']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-row">
							<div class="form-group col-md-6 mb-4">
								<label for="calories" class="form-label font-weight-bold">Calories (kcal) <span class="text-danger">*</span></label>
								<input type="number" step="0.01" min="0" class="form-control <?php echo isset($errors['calories']) ? 'is-invalid' : ''; ?>" 
									id="calories" name="calories" placeholder="0.00"
									value="<?php echo isset($_POST['calories']) ? htmlspecialchars($_POST['calories']) : ''; ?>" required>
								<?php if (isset($errors['calories'])): ?>
									<div class="invalid-feedback d-block">
										<?php echo $errors['calories']; ?>
									</div>
								<?php endif; ?>
							</div>

							<div class="form-group col-md-6 mb-4">
							<label for="proteins" class="form-label font-weight-bold">Protéines (g) <span class="text-danger">*</span></label>
								<input type="number" step="0.01" min="0" class="form-control <?php echo isset($errors['proteins']) ? 'is-invalid' : ''; ?>" 
									id="proteins" name="proteins" placeholder="0.00"
									value="<?php echo isset($_POST['proteins']) ? htmlspecialchars($_POST['proteins']) : ''; ?>" required>
								<?php if (isset($errors['proteins'])): ?>
									<div class="invalid-feedback d-block">
										<?php echo $errors['proteins']; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-6 mb-4">
							<label for="glucides" class="form-label font-weight-bold">Glucides (g) <span class="text-danger">*</span></label>
								<input type="number" step="0.01" min="0" class="form-control <?php echo isset($errors['glucides']) ? 'is-invalid' : ''; ?>" 
									id="glucides" name="glucides" placeholder="0.00"
									value="<?php echo isset($_POST['glucides']) ? htmlspecialchars($_POST['glucides']) : ''; ?>" required>
								<?php if (isset($errors['glucides'])): ?>
									<div class="invalid-feedback d-block">
										<?php echo $errors['glucides']; ?>
									</div>
								<?php endif; ?>
							</div>

							<div class="form-group col-md-6 mb-4">
							<label for="lipides" class="form-label font-weight-bold">Lipides (g) <span class="text-danger">*</span></label>
								<input type="number" step="0.01" min="0" class="form-control <?php echo isset($errors['lipides']) ? 'is-invalid' : ''; ?>" 
									id="lipides" name="lipides" placeholder="0.00"
									value="<?php echo isset($_POST['lipides']) ? htmlspecialchars($_POST['lipides']) : ''; ?>" required>
								<?php if (isset($errors['lipides'])): ?>
									<div class="invalid-feedback d-block">
										<?php echo $errors['lipides']; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<div class="form-group mt-5">
						<button type="submit" class="boxed-btn btn-block"><i class="fas fa-save"></i> Créer l'ingrédient</button>
						<a href="ingredient_list.php" class="btn btn-secondary btn-block mt-2">Annuler</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- end create ingredient section -->

<style>
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
