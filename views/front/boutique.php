<?php
ob_start();

require_once 'config.php';
require_once 'models/Database.php';

$recherche = trim($_GET['q'] ?? '');

// Charger depuis la base de données
try {
    $db = Database::getInstance()->getConnection();

    // Créer la table si elle n'existe pas encore
    $db->exec("CREATE TABLE IF NOT EXISTS boutique_produits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(150) NOT NULL,
        description TEXT,
        prix DECIMAL(10,3) NOT NULL,
        ancien_prix DECIMAL(10,3) DEFAULT NULL,
        categorie VARCHAR(80) DEFAULT NULL,
        emoji VARCHAR(20) DEFAULT NULL,
        badge VARCHAR(50) DEFAULT NULL,
        actif TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    $stmt = $db->query("SELECT * FROM boutique_produits WHERE actif=1 ORDER BY created_at DESC");
    $produitsDB = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    $produitsDB = [];
}

// Si la base est vide → produits par défaut
$produitsDef = [
  ['id'=>1, 'nom'=>'Gants de Boxe Pro',         'prix'=>169,  'ancien_prix'=>249,  'cat'=>'Boxe',        'emoji'=>'🥊', 'desc'=>'Gants en cuir synthétique premium, rembourrage haute densité.','badge'=>'Bestseller'],
  ['id'=>2, 'nom'=>'Haltères Réglables 20kg',   'prix'=>299,  'ancien_prix'=>429,  'cat'=>'Musculation', 'emoji'=>'🏋️', 'desc'=>'Jeu d\'haltères réglables de 2 à 20kg, système de clip rapide.','badge'=>'Promo -30%'],
  ['id'=>3, 'nom'=>'Corde à Sauter Speed',       'prix'=>49,   'ancien_prix'=>79,   'cat'=>'Cardio',      'emoji'=>'⚡', 'desc'=>'Corde speed câble acier, roulements à billes.','badge'=>'Nouveau'],
  ['id'=>4, 'nom'=>'Tapis de Yoga Premium 6mm', 'prix'=>115,  'ancien_prix'=>169,  'cat'=>'Yoga',        'emoji'=>'🧘', 'desc'=>'Tapis antidérapant caoutchouc naturel 6mm.','badge'=>null],
  ['id'=>5, 'nom'=>'Kettlebell Fonte 16kg',      'prix'=>149,  'ancien_prix'=>null, 'cat'=>'Force',       'emoji'=>'🔔', 'desc'=>'Kettlebell fonte revêtement émail.','badge'=>null],
  ['id'=>6, 'nom'=>'Sac de Boxe 40kg',           'prix'=>399,  'ancien_prix'=>549,  'cat'=>'Boxe',        'emoji'=>'🥊', 'desc'=>'Sac de frappe similicuir renforcé 40kg.','badge'=>'Promo'],
  ['id'=>7, 'nom'=>'Barre de Traction Doorway', 'prix'=>99,   'ancien_prix'=>139,  'cat'=>'Musculation', 'emoji'=>'💪', 'desc'=>'Barre de traction multi-positions, charge max 150kg.','badge'=>null],
  ['id'=>8, 'nom'=>'Bandes de Résistance x5',   'prix'=>65,   'ancien_prix'=>null, 'cat'=>'Rééducation', 'emoji'=>'🎗️', 'desc'=>'Set 5 bandes élastiques niveaux différents.','badge'=>'Top vente'],
  ['id'=>9, 'nom'=>'Chrono Sport Waterproof',    'prix'=>129,  'ancien_prix'=>199,  'cat'=>'Accessoires', 'emoji'=>'⏱️', 'desc'=>'Chronomètre étanche 50m, 5 modes sport.','badge'=>null],
  ['id'=>10,'nom'=>'Gourde Shaker 750ml',        'prix'=>42,   'ancien_prix'=>null, 'cat'=>'Accessoires', 'emoji'=>'🥤', 'desc'=>'Shaker sans BPA, grille anti-grumeaux inox.','badge'=>'Indispensable'],
  ['id'=>11,'nom'=>'Tapis de Sol Pliable 180cm', 'prix'=>89,   'ancien_prix'=>129,  'cat'=>'Yoga',        'emoji'=>'🟦', 'desc'=>'Tapis gym mousse haute densité 10mm.','badge'=>null],
  ['id'=>12,'nom'=>'Gants d\'Haltérophilie',     'prix'=>59,   'ancien_prix'=>89,   'cat'=>'Musculation', 'emoji'=>'🧤', 'desc'=>'Gants musculation poignets renforcés.','badge'=>null],
];

// Normaliser les produits DB vers le même format
if (!empty($produitsDB)) {
    $produits = array_map(fn($p) => [
        'id'          => $p['id'],
        'nom'         => $p['nom'],
        'prix'        => (float)$p['prix'],
        'ancien_prix' => $p['ancien_prix'] ? (float)$p['ancien_prix'] : null,
        'cat'         => $p['categorie'],
        'emoji'       => $p['emoji'],
        'desc'        => $p['description'],
        'badge'       => $p['badge'],
    ], $produitsDB);
} else {
    $produits = $produitsDef;
}

if ($recherche) {
  $produits = array_values(array_filter($produits, fn($p) =>
    stripos($p['nom'],$recherche)!==false ||
    stripos($p['cat'],$recherche)!==false ||
    stripos($p['desc'],$recherche)!==false
  ));
}

$cats = ['Toutes','Boxe','Musculation','Cardio','Yoga','Force','Rééducation','Accessoires'];
$catActive = $_GET['cat'] ?? 'Toutes';
if ($catActive !== 'Toutes' && !$recherche) {
  $produits = array_values(array_filter($produits, fn($p) => $p['cat'] === $catActive));
}

function dt($n){ return number_format($n, 3, ',', '.') . ' DT'; }
?>

<style>
.boutique-hero{background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);border-radius:20px;padding:60px 40px;text-align:center;margin-bottom:50px;position:relative;overflow:hidden}
.boutique-hero::before{content:'🏋️⚡🥊';font-size:80px;position:absolute;opacity:.05;top:-10px;right:20px;letter-spacing:-10px}
.boutique-hero h1{font-size:2.5rem;font-weight:800;color:#fff;margin-bottom:10px}
.boutique-hero h1 span{background:linear-gradient(135deg,#F28123,#ff6b35);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.boutique-hero p{color:rgba(255,255,255,.7);font-size:1.05rem;margin-bottom:30px}
.hero-stats{display:flex;justify-content:center;gap:40px;flex-wrap:wrap;margin-top:30px}
.hero-stat strong{display:block;font-size:1.8rem;font-weight:800;color:#F28123}
.hero-stat span{font-size:.82rem;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:1px}
.cat-filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:30px;align-items:center}
.cat-btn{padding:8px 18px;border-radius:25px;border:2px solid #e0e0e0;background:#fff;color:#555;font-size:.85rem;font-weight:600;cursor:pointer;text-decoration:none;transition:all .25s}
.cat-btn:hover,.cat-btn.active{background:#F28123;border-color:#F28123;color:#fff;transform:translateY(-2px);box-shadow:0 4px 12px rgba(242,129,35,.3)}
.products-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:24px;margin-top:20px}
.product-card{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);transition:all .3s;border:1px solid #f0f0f0;display:flex;flex-direction:column}
.product-card:hover{transform:translateY(-6px);box-shadow:0 12px 40px rgba(0,0,0,.15)}
.product-img{height:200px;display:flex;align-items:center;justify-content:center;font-size:5rem;background:linear-gradient(135deg,#f8f9ff,#eef2ff);position:relative}
.product-badge{position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#F28123,#ff6b35);color:#fff;font-size:.7rem;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase}
.product-body{padding:20px;flex:1;display:flex;flex-direction:column}
.product-cat{font-size:.75rem;font-weight:700;color:#F28123;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}
.product-name{font-size:1.05rem;font-weight:700;color:#1a1a2e;margin-bottom:8px;line-height:1.3}
.product-desc{font-size:.8rem;color:#666;line-height:1.5;flex:1;margin-bottom:14px}
.product-price-row{display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap}
.product-price{font-size:1.4rem;font-weight:800;color:#1a1a2e}
.product-old-price{font-size:.88rem;color:#999;text-decoration:line-through}
.product-save{font-size:.75rem;color:#22c55e;font-weight:700;background:#dcfce7;padding:2px 8px;border-radius:10px}
.btn-cart{width:100%;padding:12px;background:linear-gradient(135deg,#F28123,#ff6b35);color:#fff;border:none;border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-cart:hover{opacity:.9;transform:scale(1.02)}
#nm-cart-float{position:fixed;bottom:100px;right:30px;background:linear-gradient(135deg,#F28123,#ff6b35);color:#fff;border:none;border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;cursor:pointer;box-shadow:0 8px 24px rgba(242,129,35,.5);z-index:9997;transition:all .3s}
#nm-cart-float:hover{transform:scale(1.1)}
#nm-cart-count{position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;border-radius:50%;width:20px;height:20px;font-size:.7rem;font-weight:800;display:flex;align-items:center;justify-content:center}
.nm-toast{position:fixed;top:80px;right:20px;background:#1a1a2e;color:#fff;padding:14px 20px;border-radius:12px;font-size:.88rem;font-weight:600;z-index:99999;animation:toastIn .3s ease;box-shadow:0 8px 24px rgba(0,0,0,.3);max-width:320px}
@keyframes toastIn{from{opacity:0;transform:translateX(100%)}to{opacity:1;transform:translateX(0)}}
.search-results-header{background:#fff3e0;border:1px solid #F28123;border-radius:10px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:.9rem;color:#e65100}
@media(max-width:600px){.boutique-hero{padding:40px 20px}.boutique-hero h1{font-size:1.8rem}.products-grid{grid-template-columns:1fr 1fr}}
</style>

<div class="boutique-hero">
  <h1>🏋️ Boutique <span>Sport</span></h1>
  <p>Équipez-vous avec les meilleurs produits pour votre entraînement</p>
  <div class="hero-stats">
    <div class="hero-stat"><strong>12+</strong><span>Produits</span></div>
    <div class="hero-stat"><strong>48h</strong><span>Livraison</span></div>
    <div class="hero-stat"><strong>30j</strong><span>Retours</span></div>
    <div class="hero-stat"><strong>★ 4.8</strong><span>Avis</span></div>
  </div>
</div>

<?php if ($recherche): ?>
<div class="search-results-header">
  <i class="fas fa-search"></i>
  Résultats pour "<strong><?= htmlspecialchars($recherche) ?></strong>" — <?= count($produits) ?> produit(s) trouvé(s)
  <a href="index.php?c=boutique" style="margin-left:auto;color:#F28123;font-weight:700">✕ Effacer</a>
</div>
<?php else: ?>
<div class="cat-filters">
  <strong style="color:#444;margin-right:4px">Filtrer :</strong>
  <?php foreach($cats as $c): ?>
    <a href="index.php?c=boutique&cat=<?= urlencode($c) ?>" class="cat-btn <?= $catActive===$c?'active':'' ?>"><?= $c ?></a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="products-grid">
  <?php foreach($produits as $p): ?>
  <div class="product-card">
    <div class="product-img">
      <?php if($p['badge']): ?><span class="product-badge"><?= htmlspecialchars($p['badge']) ?></span><?php endif; ?>
      <span><?= $p['emoji'] ?></span>
    </div>
    <div class="product-body">
      <div class="product-cat"><?= htmlspecialchars($p['cat']) ?></div>
      <div class="product-name"><?= htmlspecialchars($p['nom']) ?></div>
      <div class="product-desc"><?= htmlspecialchars($p['desc']) ?></div>
      <div class="product-price-row">
        <span class="product-price"><?= dt($p['prix']) ?></span>
        <?php if($p['ancien_prix']): ?>
          <span class="product-old-price"><?= dt($p['ancien_prix']) ?></span>
          <span class="product-save">-<?= round(100 - ($p['prix'] / $p['ancien_prix'] * 100)) ?>%</span>
        <?php endif; ?>
      </div>
      <button class="btn-cart" onclick="addToCart(<?= $p['id'] ?>,'<?= addslashes(htmlspecialchars($p['nom'])) ?>',<?= $p['prix'] ?>,'<?= $p['emoji'] ?>')">
        <i class="fas fa-shopping-cart"></i> Ajouter au panier
      </button>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if(empty($produits)): ?>
  <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#999">
    <div style="font-size:3rem;margin-bottom:16px">🔍</div>
    <h3 style="color:#444">Aucun produit trouvé</h3>
    <p>Essayez un autre terme de recherche ou une autre catégorie.</p>
    <a href="index.php?c=boutique" style="color:#F28123;font-weight:700;margin-top:12px;display:inline-block">← Voir tous les produits</a>
  </div>
  <?php endif; ?>
</div>

<div id="nm-cart-float" title="Voir mon panier">
  🛒 <span id="nm-cart-count">0</span>
</div>

<script>
var cart = JSON.parse(localStorage.getItem('nm_sport_cart') || '[]');

function fmtDT(n){ return n.toFixed(3).replace('.',',') + ' DT'; }

function updateCartUI(){
  document.getElementById('nm-cart-count').textContent = cart.reduce(function(s,i){return s+i.qty;},0);
}

function addToCart(id, nom, prix, emoji){
  var existing = cart.find(function(i){return i.id===id;});
  if(existing){ existing.qty++; }
  else{ cart.push({id:id,nom:nom,prix:prix,emoji:emoji,qty:1}); }
  localStorage.setItem('nm_sport_cart', JSON.stringify(cart));
  updateCartUI();
  showToast('✅ '+emoji+' '+nom+' ajouté au panier !');
}

function showToast(msg){
  var t = document.createElement('div');
  t.className = 'nm-toast';
  t.innerHTML = msg;
  document.body.appendChild(t);
  setTimeout(function(){ t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(function(){t.remove();},300); }, 2800);
}

document.getElementById('nm-cart-float').addEventListener('click', function(){
  window.location.href = 'index.php?c=panier';
});

// Badge compteur dans la nav
function syncNavBadge(){
  var total = cart.reduce(function(s,i){return s+i.qty;},0);
  var badge = document.getElementById('nm-cart-nav-count');
  if(badge){
    badge.textContent = total;
    badge.style.display = total > 0 ? 'inline-block' : 'none';
  }
}

updateCartUI();
syncNavBadge();
</script>

<?php
$content = ob_get_clean();
require_once 'views/header.php';
?>

	<!-- breadcrumb-section -->
	<div class="breadcrumb-section breadcrumb-bg">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="breadcrumb-text">
						<p>Fresh and Organic</p>
						<h1>Boutique Sport</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end breadcrumb section -->

	<!-- products -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<?php echo $content; ?>
		</div>
	</div>
	<!-- end products -->

<?php
require_once 'views/footer.php';
?>
