<?php ob_start(); ?>

<style>
/* ===== PAGE PANIER ===== */
.panier-hero{background:linear-gradient(135deg,#1a1a2e,#0f3460);border-radius:20px;padding:40px;margin-bottom:40px;display:flex;align-items:center;gap:20px}
.panier-hero h1{color:#fff;font-size:2rem;font-weight:800;margin:0}
.panier-hero p{color:rgba(255,255,255,.7);margin:6px 0 0}
.panier-icon{font-size:3.5rem;background:rgba(255,255,255,.1);border-radius:16px;padding:16px;line-height:1}

.panier-layout{display:grid;grid-template-columns:1fr 340px;gap:28px;align-items:start}
@media(max-width:768px){.panier-layout{grid-template-columns:1fr}}

/* Carte produit panier */
.panier-card{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.07);margin-bottom:16px;display:flex;align-items:center;gap:0;border:1px solid #f0f0f0;transition:box-shadow .2s}
.panier-card:hover{box-shadow:0 6px 24px rgba(0,0,0,.12)}
.panier-emoji{width:90px;min-height:90px;background:linear-gradient(135deg,#f0f4ff,#e8eeff);display:flex;align-items:center;justify-content:center;font-size:2.8rem;flex-shrink:0}
.panier-info{flex:1;padding:16px 20px}
.panier-nom{font-size:1rem;font-weight:700;color:#1a1a2e;margin-bottom:4px}
.panier-cat{font-size:.75rem;color:#F28123;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px}
.panier-prix-unit{font-size:.85rem;color:#999}
.panier-qty-row{display:flex;align-items:center;gap:0;border:2px solid #e8e8e8;border-radius:8px;overflow:hidden;width:fit-content;margin-top:8px}
.qty-btn{background:#f8f9fa;border:none;width:32px;height:32px;cursor:pointer;font-size:1.1rem;font-weight:700;color:#444;transition:background .2s}
.qty-btn:hover{background:#F28123;color:#fff}
.qty-val{width:40px;height:32px;text-align:center;border:none;border-left:2px solid #e8e8e8;border-right:2px solid #e8e8e8;font-weight:700;font-size:.9rem;color:#1a1a2e;background:#fff}
.panier-line-price{font-size:1.2rem;font-weight:800;color:#1a1a2e;padding:16px 20px;white-space:nowrap}
.panier-remove{padding:12px 16px;color:#dc3545;cursor:pointer;background:none;border:none;font-size:1.2rem;transition:transform .2s}
.panier-remove:hover{transform:scale(1.2)}

/* Récap panier */
.recap-card{background:#fff;border-radius:16px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.07);border:1px solid #f0f0f0;position:sticky;top:20px}
.recap-card h3{font-size:1.1rem;font-weight:800;color:#1a1a2e;margin-bottom:20px;padding-bottom:14px;border-bottom:2px solid #f0f0f0}
.recap-line{display:flex;justify-content:space-between;align-items:center;padding:8px 0;font-size:.9rem;color:#555;border-bottom:1px solid #f8f8f8}
.recap-line:last-child{border-bottom:none}
.recap-total{display:flex;justify-content:space-between;align-items:center;padding:16px 0 0;margin-top:8px;border-top:2px solid #1a1a2e}
.recap-total span:first-child{font-size:1rem;font-weight:700;color:#1a1a2e}
.recap-total span:last-child{font-size:1.6rem;font-weight:800;color:#F28123}
.btn-commander{width:100%;padding:15px;background:linear-gradient(135deg,#F28123,#ff6b35);color:#fff;border:none;border-radius:12px;font-size:1rem;font-weight:800;cursor:pointer;margin-top:20px;transition:all .2s;letter-spacing:.3px}
.btn-commander:hover{opacity:.9;transform:translateY(-2px);box-shadow:0 8px 20px rgba(242,129,35,.4)}
.btn-continuer{width:100%;padding:12px;background:#f0f4ff;color:#4f46e5;border:2px solid #e0e4ff;border-radius:12px;font-size:.9rem;font-weight:700;cursor:pointer;margin-top:10px;transition:all .2s;text-decoration:none;display:block;text-align:center}
.btn-continuer:hover{background:#e0e4ff;color:#4f46e5}

/* Panier vide */
.panier-vide{text-align:center;padding:80px 20px;background:#fff;border-radius:20px;box-shadow:0 2px 12px rgba(0,0,0,.06)}
.panier-vide-icon{font-size:5rem;margin-bottom:20px;display:block}
.panier-vide h2{color:#1a1a2e;font-size:1.5rem;font-weight:700;margin-bottom:10px}
.panier-vide p{color:#94a3b8;font-size:.95rem;margin-bottom:30px}
.btn-shop{display:inline-flex;align-items:center;gap:8px;padding:14px 28px;background:linear-gradient(135deg,#F28123,#ff6b35);color:#fff;border-radius:12px;font-weight:700;text-decoration:none;font-size:.95rem;transition:all .2s}
.btn-shop:hover{opacity:.9;transform:translateY(-2px);color:#fff}

/* Toast commande */
.commande-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99999;display:flex;align-items:center;justify-content:center;animation:fadeIn .3s ease}
.commande-modal{background:#fff;border-radius:24px;padding:50px 40px;text-align:center;max-width:440px;width:90%;animation:modalIn .4s cubic-bezier(.34,1.56,.64,1)}
.commande-modal h2{font-size:1.6rem;font-weight:800;color:#1a1a2e;margin:16px 0 10px}
.commande-modal p{color:#64748b;line-height:1.6;margin-bottom:24px}
.commande-modal .btn-ok{background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;border:none;padding:14px 32px;border-radius:12px;font-weight:700;font-size:1rem;cursor:pointer}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
@keyframes modalIn{from{opacity:0;transform:scale(.8)}to{opacity:1;transform:scale(1)}}
</style>

<!-- HERO -->
<div class="panier-hero">
  <div class="panier-icon">🛒</div>
  <div>
    <h1>Mon Panier</h1>
    <p>Vérifiez vos articles et passez commande</p>
  </div>
</div>

<!-- Panier vide (affiché si JS détecte panier vide) -->
<div id="paniervide" style="display:none">
  <div class="panier-vide">
    <span class="panier-vide-icon">🛒</span>
    <h2>Votre panier est vide</h2>
    <p>Vous n'avez pas encore ajouté de produits à votre panier.<br>Découvrez notre catalogue d'équipements sportifs !</p>
    <a href="index.php?c=boutique" class="btn-shop">🏋️ Voir la boutique</a>
  </div>
</div>

<!-- Layout panier (affiché si produits) -->
<div class="panier-layout" id="panierLayout" style="display:none">
  <!-- Liste articles -->
  <div>
    <div id="panierItems"></div>
    <div style="margin-top:8px">
      <button onclick="viderPanier()" style="background:none;border:1px solid #dc3545;color:#dc3545;padding:8px 18px;border-radius:8px;cursor:pointer;font-size:.85rem;font-weight:600;transition:all .2s" onmouseover="this.style.background='#dc3545';this.style.color='#fff'" onmouseout="this.style.background='none';this.style.color='#dc3545'">
        🗑️ Vider le panier
      </button>
    </div>
  </div>

  <!-- Récapitulatif -->
  <div class="recap-card" id="recapCard">
    <h3>🧾 Récapitulatif</h3>
    <div id="recapLines"></div>
    <div class="recap-total">
      <span>Total</span>
      <span id="totalFinal">0,000 DT</span>
    </div>
    <button class="btn-commander" onclick="passerCommande()">
      ✅ Passer la commande
    </button>
    <a href="index.php?c=boutique" class="btn-continuer">← Continuer les achats</a>
    <div style="margin-top:16px;padding:12px;background:#f8f9ff;border-radius:10px;font-size:.78rem;color:#64748b;text-align:center;line-height:1.6">
      🚚 Livraison sous 48h · 🔄 30j de retour · 🔒 Paiement sécurisé
    </div>
  </div>
</div>

<script>
var cart = JSON.parse(localStorage.getItem('nm_sport_cart') || '[]');

function fmtDT(n){ return n.toFixed(3).replace('.',',') + ' DT'; }

function renderPanier(){
  cart = JSON.parse(localStorage.getItem('nm_sport_cart') || '[]');

  if(cart.length === 0){
    document.getElementById('paniervide').style.display = 'block';
    document.getElementById('panierLayout').style.display = 'none';
    return;
  }

  document.getElementById('paniervide').style.display = 'none';
  document.getElementById('panierLayout').style.display = 'grid';

  // Items
  var html = '';
  cart.forEach(function(item, idx){
    html += '<div class="panier-card">' +
      '<div class="panier-emoji">' + item.emoji + '</div>' +
      '<div class="panier-info">' +
        '<div class="panier-nom">' + item.nom + '</div>' +
        '<div class="panier-prix-unit">' + fmtDT(item.prix) + ' / unité</div>' +
        '<div class="panier-qty-row">' +
          '<button class="qty-btn" onclick="changeQty(' + idx + ',-1)">−</button>' +
          '<input class="qty-val" type="number" min="1" value="' + item.qty + '" onchange="setQty(' + idx + ',this.value)">' +
          '<button class="qty-btn" onclick="changeQty(' + idx + ',1)">+</button>' +
        '</div>' +
      '</div>' +
      '<div class="panier-line-price">' + fmtDT(item.prix * item.qty) + '</div>' +
      '<button class="panier-remove" onclick="removeItem(' + idx + ')" title="Supprimer">✕</button>' +
    '</div>';
  });
  document.getElementById('panierItems').innerHTML = html;

  // Récap
  var totalHT = cart.reduce(function(s,i){return s+i.prix*i.qty;},0);
  var livraison = totalHT >= 200 ? 0 : 7.5;
  var totalTTC = totalHT + livraison;
  var lignes = '';
  cart.forEach(function(i){
    lignes += '<div class="recap-line"><span>' + i.emoji + ' ' + i.nom + ' ×' + i.qty + '</span><span>' + fmtDT(i.prix*i.qty) + '</span></div>';
  });
  lignes += '<div class="recap-line"><span>🚚 Livraison</span><span>' + (livraison===0 ? '<span style="color:#22c55e;font-weight:700">Gratuite</span>' : fmtDT(livraison)) + '</span></div>';
  if(totalHT < 200) lignes += '<div style="background:#fff3e0;border-radius:8px;padding:8px 10px;font-size:.75rem;color:#e65100;margin:6px 0">🎁 Plus que <strong>' + fmtDT(200-totalHT) + '</strong> pour la livraison gratuite !</div>';
  document.getElementById('recapLines').innerHTML = lignes;
  document.getElementById('totalFinal').textContent = fmtDT(totalTTC);
}

function changeQty(idx, delta){
  cart[idx].qty = Math.max(1, cart[idx].qty + delta);
  save();
}

function setQty(idx, val){
  cart[idx].qty = Math.max(1, parseInt(val) || 1);
  save();
}

function removeItem(idx){
  cart.splice(idx, 1);
  save();
}

function viderPanier(){
  if(confirm('Vider tout le panier ?')){ cart = []; save(); }
}

function save(){
  localStorage.setItem('nm_sport_cart', JSON.stringify(cart));
  updateCartBadge();
  renderPanier();
}

function updateCartBadge(){
  var badge = document.getElementById('nm-cart-count');
  if(badge) badge.textContent = cart.reduce(function(s,i){return s+i.qty;},0);
}

function passerCommande(){
  var total = cart.reduce(function(s,i){return s+i.prix*i.qty;},0);
  var overlay = document.createElement('div');
  overlay.className = 'commande-overlay';
  overlay.innerHTML =
    '<div class="commande-modal">' +
      '<div style="font-size:4rem">🎉</div>' +
      '<h2>Commande confirmée !</h2>' +
      '<p>Merci pour votre commande.<br>Vous recevrez une confirmation par email sous peu.<br>' +
      '<strong>Total payé : ' + fmtDT(total) + '</strong></p>' +
      '<button class="btn-ok" onclick="confirmerCommande(this)">Parfait !</button>' +
    '</div>';
  document.body.appendChild(overlay);
}

function confirmerCommande(btn){
  cart = [];
  localStorage.setItem('nm_sport_cart', '[]');
  updateCartBadge();
  btn.closest('.commande-overlay').remove();
  window.location.href = 'index.php?c=boutique';
}

// Mise à jour du compteur dans le nav (si le widget cart est présent)
updateCartBadge();
renderPanier();
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
						<h1>Panier Sport</h1>
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
