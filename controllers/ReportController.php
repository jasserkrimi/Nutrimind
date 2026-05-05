<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Report.php';

class ReportController {
    private $reportModel;

    public function __construct() {
        $this->reportModel = new Report();
    }

    public function handleRequest() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Vous devez être connecté pour signaler un contenu.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        if (!isset($input['item_type']) || !isset($input['item_id']) || !isset($input['motif'])) {
            echo json_encode(['success' => false, 'error' => 'Paramètres manquants.']);
            exit;
        }

        $item_type = $input['item_type'];
        $item_id = (int)$input['item_id'];
        $motif = trim($input['motif']);
        $user_id = $_SESSION['user_id'];

        if (!in_array($item_type, ['post', 'comment'])) {
            echo json_encode(['success' => false, 'error' => 'Type de contenu invalide.']);
            exit;
        }
        
        if (empty($motif)) {
            echo json_encode(['success' => false, 'error' => 'Veuillez spécifier un motif.']);
            exit;
        }

        try {
            $result = $this->reportModel->create($item_type, $item_id, $user_id, $motif);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Signalement envoyé avec succès. Merci de votre vigilance.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Vous avez déjà signalé ce contenu.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur lors du signalement.']);
        }
        exit;
    }
}
