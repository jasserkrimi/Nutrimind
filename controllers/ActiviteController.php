<?php
// controllers/ActiviteController.php
require_once 'models/ActiviteSportive.php';
require_once 'models/Exercice.php';
require_once 'models/Database.php';

class ActiviteController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        $stmt = $this->db->query("SELECT * FROM activites_sportives ORDER BY nom ASC");
        $activites = $stmt->fetchAll();

        // Statistiques
        $total_activites = count($activites);
        
        $stmtStats = $this->db->query("SELECT count(*) as total FROM exercices");
        $total_exercices = $stmtStats->fetchColumn();

        $stmtCat = $this->db->query("SELECT categorie, COUNT(*) as count FROM activites_sportives GROUP BY categorie");
        $categories_stats = $stmtCat->fetchAll();

        require_once 'views/activites/index.php';
    }

    public function create() {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $categorie = trim($_POST['categorie'] ?? '');

            if (empty($nom)) {
                $errors[] = "Le nom de l'activité est requis.";
            }
            if (empty($categorie)) {
                $errors[] = "La catégorie est requise.";
            }

            if (empty($errors)) {
                $stmt = $this->db->prepare("INSERT INTO activites_sportives (nom, description, categorie) VALUES (:nom, :description, :categorie)");
                $stmt->execute([
                    'nom' => $nom,
                    'description' => $description,
                    'categorie' => $categorie
                ]);
                header('Location: index.php?c=activite&action=index');
                exit();
            }
        }
        require_once 'views/activites/create.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?c=activite&action=index');
            exit();
        }

        $stmt = $this->db->prepare("SELECT * FROM activites_sportives WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $activite = $stmt->fetch();
        
        if (!$activite) {
            die("Activité introuvable.");
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $categorie = trim($_POST['categorie'] ?? '');

            if (empty($nom)) {
                $errors[] = "Le nom de l'activité est requis.";
            }
            if (empty($categorie)) {
                $errors[] = "La catégorie est requise.";
            }

            if (empty($errors)) {
                $updateStmt = $this->db->prepare("UPDATE activites_sportives SET nom = :nom, description = :description, categorie = :categorie WHERE id = :id");
                $updateStmt->execute([
                    'id' => $id,
                    'nom' => $nom,
                    'description' => $description,
                    'categorie' => $categorie
                ]);
                header('Location: index.php?c=activite&action=index');
                exit();
            }
        }
        require_once 'views/activites/edit.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM activites_sportives WHERE id = :id");
            $stmt->execute(['id' => $id]);
        }
        header('Location: index.php?c=activite&action=index');
        exit();
    }

    // Afficher les exercices associés (uniquement lecteur maintenant)
    public function exercices() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?c=activite&action=index');
            exit();
        }

        $stmt = $this->db->prepare("SELECT * FROM activites_sportives WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $activite = $stmt->fetch();

        // Exercices liés avec la Foreign Key
        $stmtExercices = $this->db->prepare("SELECT * FROM exercices WHERE activite_id = :activite_id");
        $stmtExercices->execute(['activite_id' => $id]);
        $linkedExercices = $stmtExercices->fetchAll();

        require_once 'views/activites/exercices.php';
    }
}
