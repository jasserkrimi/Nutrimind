<?php
require_once __DIR__ . '/../config/Database.php';

class CommentReaction {
    private $conn;
    private $table = 'comment_reaction';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function toggleReaction($comment_id, $user_id, $type) {
        // Check if reaction exists
        $stmt = $this->conn->prepare("SELECT id_reaction, type FROM {$this->table} WHERE comment_id = :comment_id AND user_id = :user_id");
        $stmt->bindParam(':comment_id', $comment_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            if ($existing['type'] === $type) {
                // User clicked the same reaction, remove it
                $stmtDel = $this->conn->prepare("DELETE FROM {$this->table} WHERE id_reaction = :id");
                $stmtDel->bindParam(':id', $existing['id_reaction']);
                $stmtDel->execute();
                return ['status' => 'removed', 'old_type' => $existing['type']];
            } else {
                // Change reaction
                $stmtUp = $this->conn->prepare("UPDATE {$this->table} SET type = :type, date_creation = NOW() WHERE id_reaction = :id");
                $stmtUp->bindParam(':type', $type);
                $stmtUp->bindParam(':id', $existing['id_reaction']);
                $stmtUp->execute();
                return ['status' => 'changed', 'old_type' => $existing['type']];
            }
        } else {
            // New reaction
            $stmtIns = $this->conn->prepare("INSERT INTO {$this->table} (comment_id, user_id, type) VALUES (:comment_id, :user_id, :type)");
            $stmtIns->bindParam(':comment_id', $comment_id);
            $stmtIns->bindParam(':user_id', $user_id);
            $stmtIns->bindParam(':type', $type);
            $stmtIns->execute();
            return ['status' => 'added', 'old_type' => null];
        }
    }

    public function getCounts($comment_id) {
        $stmt = $this->conn->prepare("SELECT 
            SUM(CASE WHEN type = 'like' THEN 1 ELSE 0 END) as likes,
            SUM(CASE WHEN type = 'dislike' THEN 1 ELSE 0 END) as dislikes
            FROM {$this->table} WHERE comment_id = :comment_id");
        $stmt->bindParam(':comment_id', $comment_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'likes' => (int)$row['likes'],
            'dislikes' => (int)$row['dislikes']
        ];
    }

    public function getUserReaction($comment_id, $user_id) {
        if (!$user_id) return null;
        $stmt = $this->conn->prepare("SELECT type FROM {$this->table} WHERE comment_id = :comment_id AND user_id = :user_id");
        $stmt->bindParam(':comment_id', $comment_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['type'] : null;
    }
}
