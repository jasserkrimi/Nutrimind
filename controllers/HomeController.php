<?php
// controllers/HomeController.php
require_once 'models/ActiviteSportive.php';
require_once 'models/Exercice.php';
require_once 'models/Database.php';

class HomeController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        $stmt = $this->db->query("SELECT * FROM activites_sportives ORDER BY nom ASC");
        $activites = $stmt->fetchAll();
        require_once 'views/front/home.php';
    }

    public function exercices() {
        $activite_id = $_GET['id'] ?? null;
        if (!$activite_id) {
            header('Location: index.php?c=home');
            exit();
        }

        $stmt = $this->db->prepare("SELECT * FROM activites_sportives WHERE id = :id");
        $stmt->execute(['id' => $activite_id]);
        $activite = $stmt->fetch();
        
        if (!$activite) {
            die("Activité introuvable.");
        }

        $stmtEx = $this->db->prepare("SELECT * FROM exercices WHERE activite_id = :activite_id");
        $stmtEx->execute(['activite_id' => $activite_id]);
        $exercices = $stmtEx->fetchAll();
        
        require_once 'views/front/exercices.php';
    }

    public function planning() {
        // Obtenir toutes les séances programmées
        $stmt = $this->db->query("
            SELECT sp.*, a.nom as activite_nom, a.categorie 
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

        require_once 'views/front/planning.php';
    }
}
