<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/PostReaction.php';

class ReactionController {
    private $reactionModel;

    public function __construct() {
        $this->reactionModel = new PostReaction();
    }

    public function handleRequest() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Vous devez être connecté.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        if (!isset($input['post_id']) || !isset($input['type'])) {
            echo json_encode(['success' => false, 'error' => 'Paramètres manquants.']);
            exit;
        }

        $post_id = (int)$input['post_id'];
        $type = $input['type'];
        $user_id = $_SESSION['user_id'];

        if (!in_array($type, ['like', 'dislike'])) {
            echo json_encode(['success' => false, 'error' => 'Type de réaction invalide.']);
            exit;
        }

        $result = $this->reactionModel->toggleReaction($post_id, $user_id, $type);

        if ($result) {
            $counts = $this->reactionModel->getCounts($post_id);
            $user_reaction = $this->reactionModel->getUserReaction($post_id, $user_id);
            echo json_encode([
                'success' => true, 
                'counts' => $counts,
                'user_reaction' => $user_reaction
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'enregistrement de la réaction.']);
        }
        exit;
    }
}
