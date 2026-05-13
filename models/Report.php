<?php
require_once __DIR__ . '/../config/Database.php';

class Report {
    private $conn;
    private $table = 'report';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create($item_type, $item_id, $user_id, $motif) {
        // Prevent double reporting for same item by same user
        $stmtCheck = $this->conn->prepare("SELECT id_report FROM {$this->table} WHERE item_type = :item_type AND item_id = :item_id AND user_id = :user_id");
        $stmtCheck->bindParam(':item_type', $item_type);
        $stmtCheck->bindParam(':item_id', $item_id);
        $stmtCheck->bindParam(':user_id', $user_id);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn()) {
            return false; // Already reported
        }

        $query = "INSERT INTO {$this->table} (item_type, item_id, user_id, motif) VALUES (:item_type, :item_id, :user_id, :motif)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':item_type', $item_type);
        $stmt->bindParam(':item_id', $item_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':motif', $motif);
        return $stmt->execute();
    }

    public function getAllPending() {
        // Fetch reports with user info and item info
        $query = "SELECT r.*, u.nom AS reporter_nom, u.email AS reporter_email
                  FROM {$this->table} r
                  JOIN user u ON r.user_id = u.id
                  WHERE r.statut = 'en_attente'
                  ORDER BY r.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id_report, $statut) {
        $query = "UPDATE {$this->table} SET statut = :statut WHERE id_report = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':id', $id_report);
        return $stmt->execute();
    }

    public function delete($id_report) {
        $query = "DELETE FROM {$this->table} WHERE id_report = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_report);
        return $stmt->execute();
    }
}
