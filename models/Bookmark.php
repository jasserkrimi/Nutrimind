<?php
require_once __DIR__ . '/../config/Database.php';

class Bookmark {
    private $conn;
    private $table = 'post_bookmark';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function toggleBookmark($post_id, $user_id) {
        // Check if bookmark exists
        $stmt = $this->conn->prepare("SELECT id_bookmark FROM {$this->table} WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Remove bookmark
            $stmtDel = $this->conn->prepare("DELETE FROM {$this->table} WHERE id_bookmark = :id");
            $stmtDel->bindParam(':id', $existing['id_bookmark']);
            $stmtDel->execute();
            return ['status' => 'removed'];
        } else {
            // Add bookmark
            $stmtIns = $this->conn->prepare("INSERT INTO {$this->table} (post_id, user_id) VALUES (:post_id, :user_id)");
            $stmtIns->bindParam(':post_id', $post_id);
            $stmtIns->bindParam(':user_id', $user_id);
            $stmtIns->execute();
            return ['status' => 'added'];
        }
    }

    public function isBookmarked($post_id, $user_id) {
        if (!$user_id) return false;
        $stmt = $this->conn->prepare("SELECT id_bookmark FROM {$this->table} WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchColumn() ? true : false;
    }

    public function getUserBookmarks($user_id) {
        $query = "SELECT p.*, u.nom AS auteur_nom,
                         (SELECT COUNT(*) FROM comment c WHERE c.post_id = p.id_post AND c.statut = 'approuve') AS nb_comments,
                         (SELECT COUNT(*) FROM post_reaction r WHERE r.post_id = p.id_post AND r.type = 'like') AS nb_likes,
                         b.date_creation AS bookmarked_at
                  FROM {$this->table} b
                  JOIN post p ON b.post_id = p.id_post
                  LEFT JOIN user u ON p.user_id = u.id
                  WHERE b.user_id = :user_id
                  ORDER BY b.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
