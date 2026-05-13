<?php
require_once 'config.php';
require_once 'models/Database.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer le jour actuel
    $joursMap = [0=>'Dimanche',1=>'Lundi',2=>'Mardi',3=>'Mercredi',4=>'Jeudi',5=>'Vendredi',6=>'Samedi'];
    $jourAujourdHui = $joursMap[(int)date('w')];
    $heureActuelle = date('H:i:s');
    
    // Récupérer toutes les séances d'aujourd'hui avec notification activée
    $stmt = $db->prepare("
        SELECT sp.id, sp.heure_debut, sp.statut, a.nom as activite_nom, a.categorie
        FROM seances_programmees sp
        JOIN activites_sportives a ON sp.activite_id = a.id
        WHERE sp.jour_semaine = :jour
        AND sp.statut = 'Programme'
        ORDER BY sp.heure_debut ASC
    ");
    
    $stmt->execute(['jour' => $jourAujourdHui]);
    $seances = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculer quelles séances doivent déclencher une notification maintenant
    // Notification 5 minutes avant (tolérance ±1 minute = entre 4 et 6 minutes avant)
    $notifications = [];
    $currentTime = strtotime($heureActuelle);
    $debug_seances = [];
    
    foreach ($seances as $seance) {
        $seanceTime = strtotime($seance['heure_debut']);
        $diffSeconds = $seanceTime - $currentTime;
        $diffMinutes = round($diffSeconds / 60);
        
        $debug_seances[] = [
            'activite' => $seance['activite_nom'],
            'heure' => substr($seance['heure_debut'], 0, 5),
            'diff_minutes' => $diffMinutes,
            'should_notify' => ($diffMinutes >= 4 && $diffMinutes <= 6)
        ];
        
        // Envoyer notification si la séance est dans 4 à 6 minutes
        if ($diffMinutes >= 4 && $diffMinutes <= 6) {
            $notifications[] = [
                'id' => $seance['id'],
                'activite' => $seance['activite_nom'],
                'heure' => substr($seance['heure_debut'], 0, 5),
                'message' => '⏰ Votre séance commence dans 5 minutes ! Heure : ' . substr($seance['heure_debut'], 0, 5),
                'jour' => $jourAujourdHui,
                'diff_minutes' => $diffMinutes
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'jour' => $jourAujourdHui,
        'heure' => substr($heureActuelle, 0, 5),
        'count' => count($notifications),
        'debug' => [
            'total_seances' => count($seances),
            'current_time' => $heureActuelle,
            'seances_detail' => $debug_seances
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
