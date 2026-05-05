<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth.php');
    exit;
}

require_once '../../models/Report.php';
$reportModel = new Report();

// Handle status updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    
    if ($action === 'traiter') {
        $reportModel->updateStatus($id, 'traite');
        $_SESSION['success_message'] = "Le signalement a été marqué comme traité.";
    } elseif ($action === 'supprimer') {
        $reportModel->delete($id);
        $_SESSION['success_message'] = "Le signalement a été supprimé.";
    }
    header('Location: report_list.php');
    exit;
}

$reports = $reportModel->getAllPending();
?>

<?php include 'header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0"><i class="fas fa-flag text-danger"></i> Modération : Signalements en attente</h2>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Type</th>
                            <th>ID Contenu</th>
                            <th>Motif</th>
                            <th>Signalé par</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                                    Aucun signalement en attente. Tout est propre !
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reports as $r): ?>
                                <tr>
                                    <td>
                                        <?php if($r['item_type'] == 'post'): ?>
                                            <span class="badge badge-primary">Post</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">Commentaire</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($r['item_type'] == 'post'): ?>
                                            <a href="../post_detail.php?id=<?= $r['item_id'] ?>" target="_blank">Voir Post #<?= $r['item_id'] ?></a>
                                        <?php else: ?>
                                            ID: <?= $r['item_id'] ?> (Commentaire)
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-danger fw-bold"><?= htmlspecialchars($r['motif']) ?></td>
                                    <td><?= htmlspecialchars($r['reporter_nom']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($r['date_creation'])) ?></td>
                                    <td>
                                        <a href="report_list.php?action=traiter&id=<?= $r['id_report'] ?>" class="btn btn-sm btn-success" title="Marquer comme traité">
                                            <i class="fas fa-check"></i> Traité
                                        </a>
                                        <a href="report_list.php?action=supprimer&id=<?= $r['id_report'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce signalement ?');" title="Supprimer le signalement">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
