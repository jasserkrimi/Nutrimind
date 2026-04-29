<?php
// controllers/SeanceController.php
require_once 'models/Database.php';

class SeanceController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        // Obtenir toutes les activités pour le formulaire d'ajout
        $stmtAct = $this->db->query("SELECT id, nom FROM activites_sportives");
        $activites = $stmtAct->fetchAll();

        // Obtenir toutes les séances programmées
        $stmt = $this->db->query("
            SELECT sp.*, a.nom as activite_nom 
            FROM seances_programmees sp
            JOIN activites_sportives a ON sp.activite_id = a.id
            ORDER BY FIELD(jour_semaine, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), heure_debut ASC
        ");
        $seances = $stmt->fetchAll();

        // Organiser les séances par jour pour le calendrier
        $calendrier = [
            'Lundi' => [], 'Mardi' => [], 'Mercredi' => [], 
            'Jeudi' => [], 'Vendredi' => [], 'Samedi' => [], 'Dimanche' => []
        ];

        foreach ($seances as $seance) {
            $calendrier[$seance['jour_semaine']][] = $seance;
        }

        require_once 'views/seances/calendar.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $activite_id = $_POST['activite_id'];
            $jour = $_POST['jour_semaine'];
            $heure = $_POST['heure_debut'];

            if (!empty($activite_id) && !empty($jour) && !empty($heure)) {
                $stmt = $this->db->prepare("INSERT INTO seances_programmees (activite_id, jour_semaine, heure_debut) VALUES (:act, :jour, :heure)");
                $stmt->execute(['act' => $activite_id, 'jour' => $jour, 'heure' => $heure]);
            }

            header('Location: index.php?c=seance&action=index');
            exit();
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM seances_programmees WHERE id = :id");
            $stmt->execute(['id' => $id]);
        }
        header('Location: index.php?c=seance&action=index');
        exit();
    }
}
?>
