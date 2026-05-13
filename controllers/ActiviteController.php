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

    // Chatbot API pour recommander des activités
    public function chatbot() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $userMessage = trim($data['message'] ?? '');

            if (empty($userMessage)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Message vide'
                ]);
                exit();
            }

            // Récupérer toutes les activités
            $stmt = $this->db->query("SELECT * FROM activites_sportives ORDER BY nom ASC");
            $activites = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Analyser le message et recommander des activités
            $recommendations = $this->analyzeAndRecommend($userMessage, $activites);

            echo json_encode([
                'success' => true,
                'recommendations' => $recommendations,
                'message' => $this->generateBotResponse($recommendations)
            ]);
        } else {
            // Afficher l'interface du chatbot
            require_once 'views/activites/chat.php';
        }
    }

    // Analyser le message utilisateur et recommander des activités
    private function analyzeAndRecommend($userMessage, $activites) {
        $message = strtolower($userMessage);
        
        // Mots-clés pour chaque type d'activité
        $keywords = [
            'cardio' => ['cœur', 'perte de poids', 'maigrir', 'calories', 'endurance', 'courir', 'course', 'velo', 'vélo', 'natation', 'nager', 'aérobic', 'énergie'],
            'musculation' => ['muscle', 'force', 'muscler', 'poids', 'haltère', 'fitness', 'renforcement', 'tonifier'],
            'flexibility' => ['souplesse', 'étirement', 'flexibilité', 'yoga', 'mobile', 'mobilité'],
            'relaxation' => ['stress', 'détente', 'relaxation', 'calme', 'méditation', 'respiration', 'zen', 'anxiété'],
            'sport' => ['ballon', 'foot', 'tennis', 'badminton', 'volley', 'équipe', 'compétition', 'jeu'],
            'sports d\'eau' => ['eau', 'piscine', 'plage', 'mer', 'natation', 'surf', 'plongée']
        ];

        $recommendations = [];
        $scores = [];

        // Analyser chaque activité
        foreach ($activites as $activite) {
            $score = 0;
            $name = strtolower($activite['nom']);
            $description = strtolower($activite['description'] ?? '');
            $categorie = strtolower($activite['categorie'] ?? '');

            // Vérifier les correspondances directes
            if (strpos($name, $message) !== false || strpos($description, $message) !== false) {
                $score += 10;
            }

            // Vérifier les mots-clés
            foreach ($keywords as $category => $words) {
                foreach ($words as $keyword) {
                    if (strpos($message, $keyword) !== false) {
                        // Augmenter le score si le mot-clé correspond à la catégorie
                        if (strpos($categorie, $category) !== false || strpos($name, $category) !== false) {
                            $score += 5;
                        } else {
                            $score += 2;
                        }
                    }
                }
            }

            if ($score > 0) {
                $scores[$activite['id']] = [
                    'score' => $score,
                    'activite' => $activite
                ];
            }
        }

        // Trier par score décroissant
        usort($scores, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        // Retourner les 3 meilleures recommandations
        foreach (array_slice($scores, 0, 3) as $item) {
            $recommendations[] = $item['activite'];
        }

        return $recommendations;
    }

    // Générer une réponse personnalisée du chatbot
    private function generateBotResponse($recommendations) {
        if (empty($recommendations)) {
            return "Je n'ai pas trouvé d'activité correspondant exactement à votre description. N'hésitez pas à essayer une autre description ou à consulter toutes nos activités disponibles.";
        }

        $response = "Excellent ! Voici les activités sportives qui pourraient vous convenir :\n\n";
        
        foreach ($recommendations as $index => $activite) {
            $response .= ($index + 1) . ". <strong>" . htmlspecialchars($activite['nom']) . "</strong>";
            if (!empty($activite['description'])) {
                $response .= " - " . htmlspecialchars($activite['description']);
            }
            $response .= "\n";
        }

        return $response;
    }
}
