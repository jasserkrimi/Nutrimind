<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Bookmark.php';

class BookmarkController {
    private $bookmarkModel;

    public function __construct() {
        $this->bookmarkModel = new Bookmark();
    }

    public function handleRequest() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Vous devez être connecté pour sauvegarder un article.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        if (!isset($input['post_id'])) {
            echo json_encode(['success' => false, 'error' => 'Paramètres manquants.']);
            exit;
        }

        $post_id = (int)$input['post_id'];
        $user_id = $_SESSION['user_id'];

        try {
            $result = $this->bookmarkModel->toggleBookmark($post_id, $user_id);
            echo json_encode([
                'success' => true, 
                'status' => $result['status']
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la sauvegarde.']);
        }
        exit;
    }
}
