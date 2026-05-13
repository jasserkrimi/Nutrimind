<?php ob_start(); ?>

<?php
// Créer la table si elle n'existe pas
$db->exec("CREATE TABLE IF NOT EXISTS boutique_produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10,3) NOT NULL,
    ancien_prix DECIMAL(10,3) DEFAULT NULL,
    categorie VARCHAR(80),
    emoji VARCHAR(20) DEFAULT NULL,
    badge VARCHAR(50) DEFAULT NULL,
    actif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

$msg = null;

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'create') {
    $stmt = $db->prepare("INSERT INTO boutique_produits (nom, description, prix, ancien_prix, categorie, emoji, badge) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([
        trim($_POST['nom']),
        trim($_POST['description'] ?? ''),
        (float)$_POST['prix'],
        !empty($_POST['ancien_prix']) ? (float)$_POST['ancien_prix'] : null,
        trim($_POST['categorie']),
        trim($_POST['emoji'] ?: '🏋️'),
        !empty($_POST['badge']) ? trim($_POST['badge']) : null,
    ]);
    $msg = ['type'=>'success','text'=>'✅ Produit "'.$_POST['nom'].'" ajouté avec succès !'];
}

// DELETE
if (($_GET['action']??'') === 'delete' && !empty($_GET['id'])) {
    $db->prepare("DELETE FROM boutique_produits WHERE id=?")->execute([(int)$_GET['id']]);
    $msg = ['type'=>'warning','text'=>'🗑️ Produit supprimé.'];
}

// TOGGLE actif/masqué
if (($_GET['action']??'') === 'toggle' && !empty($_GET['id'])) {
    $db->prepare("UPDATE boutique_produits SET actif = 1-actif WHERE id=?")->execute([(int)$_GET['id']]);
    header('Location: index.php?c=produit'); exit;
}

$produits = $db->query("SELECT * FROM boutique_produits ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$total    = count($produits);
$actifs   = count(array_filter($produits, fn($p) => $p['actif']));
$cats     = count(array_unique(array_filter(array_column($produits, 'categorie'))));
$prixMoyen = $total > 0 ? array_sum(array_column($produits,'prix')) / $total : 0;
?>

<!-- Header page -->
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <div>
        <h1 class="fs-3 mb-1">🛒 Boutique Sport</h1>
        <p class="mb-0 text-muted">Gérer les produits de la boutique sportive</p>
      </div>
      <a href="index.php?c=boutique" target="_blank" class="btn btn-outline-primary d-flex align-items-center gap-2">
        <i class="ti ti-eye"></i> Voir la boutique
      </a>
    </div>
  </div>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?> alert-dismissible fade show mb-4" role="alert">
  <?= $msg['text'] ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Statistiques -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="card p-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-2 h-100">
      <div class="d-flex gap-3">
        <div class="bg-primary text-white rounded-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem">🛒</div>
        <div><p class="mb-1 fs-6 text-muted">Total produits</p><h3 class="fw-bold mb-0 text-primary"><?= $total ?></h3></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2 h-100">
      <div class="d-flex gap-3">
        <div class="bg-success text-white rounded-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem">✅</div>
        <div><p class="mb-1 fs-6 text-muted">Actifs</p><h3 class="fw-bold mb-0 text-success"><?= $actifs ?></h3></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card p-4 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-2 h-100">
      <div class="d-flex gap-3">
        <div class="bg-info text-white rounded-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem">📦</div>
        <div><p class="mb-1 fs-6 text-muted">Catégories</p><h3 class="fw-bold mb-0 text-info"><?= $cats ?></h3></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card p-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-2 h-100">
      <div class="d-flex gap-3">
        <div class="bg-warning text-white rounded-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem">💰</div>
        <div><p class="mb-1 fs-6 text-muted">Prix moyen</p><h3 class="fw-bold mb-0 text-warning"><?= number_format($prixMoyen,3,',','.') ?> DT</h3></div>
      </div>
    </div>
  </div>
</div>

<!-- Formulaire ajout -->
<div class="card shadow-sm border-0 mb-4">
  <div class="card-header bg-white py-3">
    <h5 class="mb-0"><i class="ti ti-plus me-2 text-primary"></i>Ajouter un produit</h5>
  </div>
  <div class="card-body">
    <form method="POST" class="row g-3">
      <input type="hidden" name="_action" value="create">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Nom du produit <span class="text-danger">*</span></label>
        <input type="text" name="nom" class="form-control" placeholder="ex: Gants de Boxe Pro" required>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
        <select name="categorie" class="form-select" required>
          <option value="">— Choisir —</option>
          <?php foreach(['Boxe','Musculation','Cardio','Yoga','Force','Rééducation','Accessoires'] as $c): ?>
          <option value="<?= $c ?>"><?= $c ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Emoji</label>
        <input type="text" name="emoji" class="form-control" placeholder="🥊" maxlength="4" value="🏋️">
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Prix (DT) <span class="text-danger">*</span></label>
        <input type="number" name="prix" class="form-control" step="0.001" min="0" placeholder="149.000" required>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Ancien prix (DT)</label>
        <input type="number" name="ancien_prix" class="form-control" step="0.001" min="0" placeholder="249.000 (facultatif)">
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Badge promo</label>
        <input type="text" name="badge" class="form-control" placeholder="ex: Nouveau, Promo -20%">
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">
          <i class="ti ti-plus me-1"></i> Ajouter
        </button>
      </div>
      <div class="col-12">
        <label class="form-label fw-semibold">Description</label>
        <textarea name="description" class="form-control" rows="2" placeholder="Description détaillée du produit..."></textarea>
      </div>
    </form>
  </div>
</div>

<!-- Liste produits -->
<div class="card shadow-sm border-0">
  <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
    <h5 class="mb-0"><i class="ti ti-list me-2 text-primary"></i>Catalogue (<?= $total ?> produits)</h5>
    <input type="text" id="searchProd" class="form-control form-control-sm" placeholder="🔍 Rechercher..." style="width:200px">
  </div>
  <div class="card-body p-0">
    <?php if(empty($produits)): ?>
    <div class="text-center py-5 text-muted">
      <div style="font-size:3rem">🛒</div>
      <p class="mt-2">Aucun produit — ajoutez-en un ci-dessus.</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="produitsTable">
        <thead class="table-light">
          <tr>
            <th class="ps-4 py-3 text-muted fw-semibold small text-uppercase">Produit</th>
            <th class="py-3 text-muted fw-semibold small text-uppercase">Catégorie</th>
            <th class="py-3 text-muted fw-semibold small text-uppercase">Prix</th>
            <th class="py-3 text-muted fw-semibold small text-uppercase">Ancien prix</th>
            <th class="py-3 text-muted fw-semibold small text-uppercase">Badge</th>
            <th class="py-3 text-muted fw-semibold small text-uppercase">Statut</th>
            <th class="text-end pe-4 py-3 text-muted fw-semibold small text-uppercase">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($produits as $p): ?>
          <tr class="prod-row">
            <td class="ps-4">
              <span style="font-size:1.4rem"><?= htmlspecialchars($p['emoji']) ?></span>
              <strong class="ms-2 prod-name"><?= htmlspecialchars($p['nom']) ?></strong>
              <?php if($p['description']): ?>
              <br><small class="text-muted ms-5"><?= htmlspecialchars(mb_substr($p['description'],0,55)) ?>...</small>
              <?php endif; ?>
            </td>
            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1"><?= htmlspecialchars($p['categorie']) ?></span></td>
            <td><strong class="text-dark"><?= number_format($p['prix'],3,',','.') ?> DT</strong></td>
            <td><?= $p['ancien_prix'] ? '<s class="text-muted">'.number_format($p['ancien_prix'],3,',','.').' DT</s>' : '<span class="text-muted">—</span>' ?></td>
            <td><?= $p['badge'] ? '<span class="badge bg-warning text-dark">'.htmlspecialchars($p['badge']).'</span>' : '<span class="text-muted">—</span>' ?></td>
            <td>
              <?php if($p['actif']): ?>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">✅ Actif</span>
              <?php else: ?>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">🙈 Masqué</span>
              <?php endif; ?>
            </td>
            <td class="text-end pe-4">
              <a href="index.php?c=produit&action=toggle&id=<?= $p['id'] ?>"
                 class="btn btn-sm btn-light border <?= $p['actif']?'text-warning':'text-success' ?>"
                 title="<?= $p['actif']?'Masquer':'Activer' ?>">
                <i class="ti ti-<?= $p['actif']?'eye-off':'eye' ?>"></i>
              </a>
              <a href="index.php?c=produit&action=delete&id=<?= $p['id'] ?>"
                 class="btn btn-sm btn-light border text-danger"
                 onclick="return confirm('Supprimer «<?= addslashes($p['nom']) ?>» ?')"
                 title="Supprimer">
                <i class="ti ti-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.getElementById('searchProd').addEventListener('input', function(){
  var q = this.value.toLowerCase();
  document.querySelectorAll('.prod-row').forEach(function(row){
    var name = row.querySelector('.prod-name').textContent.toLowerCase();
    row.style.display = name.includes(q) ? '' : 'none';
  });
});
</script>

<?php
$content = ob_get_clean();
require 'views/layout_back.php';
?>
