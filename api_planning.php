<?php
require_once 'config.php';
require_once 'models/Database.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');
try {
    $db = Database::getInstance()->getConnection();
    $joursMap = [0=>'Dimanche',1=>'Lundi',2=>'Mardi',3=>'Mercredi',4=>'Jeudi',5=>'Vendredi',6=>'Samedi'];
    $jourAujourdHui = $joursMap[(int)date('w')];
    $stmt = $db->prepare("
        SELECT sp.heure_debut, sp.statut,
               a.nom AS activite_nom, a.categorie
        FROM seances_programmees sp
        JOIN activites_sportives a ON sp.activite_id = a.id
        WHERE sp.jour_semaine = :jour
        ORDER BY sp.heure_debut ASC
    ");
    $stmt->execute(['jour' => $jourAujourdHui]);
    $seances = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = [];
    foreach ($seances as $s) {
        $result[] = [
            'activite'  => $s['activite_nom'],
            'categorie' => $s['categorie'],
            'heure'     => substr($s['heure_debut'], 0, 5),
            'statut'    => $s['statut'] ?? 'Programme',
            'jour'      => $jourAujourdHui,
        ];
    }
    echo json_encode(['success'=>true, 'seances'=>$result, 'jour'=>$jourAujourdHui]);
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
?>
