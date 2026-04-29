<?php
// controllers/ExerciceController.php
require_once 'models/Exercice.php';
require_once 'models/ActiviteSportive.php';
require_once 'models/Database.php';

class ExerciceController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        $activite_id = $_GET['activite_id'] ?? null;
        
        if ($activite_id) {
            $stmt = $this->db->prepare("SELECT * FROM exercices WHERE activite_id = :activite_id");
            $stmt->execute(['activite_id' => $activite_id]);
            $exercices = $stmt->fetchAll();
            
            $stmtAct = $this->db->prepare("SELECT * FROM activites_sportives WHERE id = :id");
            $stmtAct->execute(['id' => $activite_id]);
            $activite = $stmtAct->fetch();
        } else {
            $stmt = $this->db->query("
                SELECT e.*, a.nom AS activite_nom 
                FROM exercices e 
                LEFT JOIN activites_sportives a ON e.activite_id = a.id 
                ORDER BY e.nom ASC
            ");
            $exercices = $stmt->fetchAll();
            $activite = ['id' => '', 'nom' => 'Tous les exercices confondus'];
        }
        require_once 'views/exercices/index.php';
    }

    public function create() {
        $stmtAct = $this->db->query("SELECT * FROM activites_sportives ORDER BY nom ASC");
        $activites = $stmtAct->fetchAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $url_video = trim($_POST['url_video'] ?? '');
            $difficulte = trim($_POST['difficulte'] ?? '');
            $activite_id = $_POST['activite_id'] ?? null;

            if (empty($nom)) {
                $errors[] = "Le nom de l'exercice est requis.";
            }

            if (!empty($url_video) && !filter_var($url_video, FILTER_VALIDATE_URL)) {
                $errors[] = "Le lien vidéo n'est pas une URL valide.";
            }

            if (empty($errors)) {
                $stmt = $this->db->prepare("INSERT INTO exercices (nom, description, url_video, difficulte, activite_id) VALUES (:nom, :description, :url_video, :difficulte, :activite_id)");
                $stmt->execute([
                    'nom' => $nom,
                    'description' => $description,
                    'url_video' => $url_video,
                    'difficulte' => $difficulte,
                    'activite_id' => $activite_id ?: null
                ]);
                header('Location: index.php?c=exercice&action=index');
                exit();
            }
        }
        require_once 'views/exercices/create.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?c=exercice&action=index');
            exit();
        }

        $stmtEx = $this->db->prepare("SELECT * FROM exercices WHERE id = :id");
        $stmtEx->execute(['id' => $id]);
        $exercice = $stmtEx->fetch();
        
        if (!$exercice) {
            die("Exercice introuvable.");
        }

        $stmtAct = $this->db->query("SELECT * FROM activites_sportives ORDER BY nom ASC");
        $activites = $stmtAct->fetchAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $url_video = trim($_POST['url_video'] ?? '');
            $difficulte = trim($_POST['difficulte'] ?? '');
            $activite_id = $_POST['activite_id'] ?? null;

            if (empty($nom)) {
                $errors[] = "Le nom de l'exercice est requis.";
            }

            if (!empty($url_video) && !filter_var($url_video, FILTER_VALIDATE_URL)) {
                $errors[] = "Le lien vidéo n'est pas une URL valide.";
            }

            if (empty($errors)) {
                $stmt = $this->db->prepare("UPDATE exercices SET nom = :nom, description = :description, url_video = :url_video, difficulte = :difficulte, activite_id = :activite_id WHERE id = :id");
                $stmt->execute([
                    'id' => $id,
                    'nom' => $nom,
                    'description' => $description,
                    'url_video' => $url_video,
                    'difficulte' => $difficulte,
                    'activite_id' => $activite_id ?: null
                ]);
                header('Location: index.php?c=exercice&action=index');
                exit();
            }
        }
        require_once 'views/exercices/edit.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM exercices WHERE id = :id");
            $stmt->execute(['id' => $id]);
        }
        header('Location: index.php?c=exercice&action=index');
        exit();
    }
}
