<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/CommentReaction.php';

class CommentReactionController {
    private $reactionModel;

    public function __construct() {
        $this->reactionModel = new CommentReaction();
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
        
        if (!isset($input['comment_id']) || !isset($input['type'])) {
            echo json_encode(['success' => false, 'error' => 'Paramètres manquants.']);
            exit;
        }

        $comment_id = (int)$input['comment_id'];
        $type = $input['type'];
        $user_id = $_SESSION['user_id'];

        if (!in_array($type, ['like', 'dislike'])) {
            echo json_encode(['success' => false, 'error' => 'Type de réaction invalide.']);
            exit;
        }

        $result = $this->reactionModel->toggleReaction($comment_id, $user_id, $type);

        if ($result) {
            $counts = $this->reactionModel->getCounts($comment_id);
            $user_reaction = $this->reactionModel->getUserReaction($comment_id, $user_id);
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
