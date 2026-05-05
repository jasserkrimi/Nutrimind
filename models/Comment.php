<?php

require_once __DIR__ . '/../config/Database.php';

class Comment {
    private $conn;
    private $table = 'comment';

    public $id_comment;
    public $post_id;
    public $user_id;
    public $contenu;
    public $statut;
    public $date_creation;
    public $date_mise_a_jour;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Create a new comment
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (post_id, user_id, contenu, statut)
                  VALUES
                  (:post_id, :user_id, :contenu, :statut)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':contenu', $this->contenu);
        $stmt->bindParam(':statut',  $this->statut);

        if ($stmt->execute()) {
            $this->id_comment = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Get comment by ID with author and post info
     */
    public function getById($id) {
        $query = "SELECT c.*, u.nom AS auteur_nom, u.email AS auteur_email,
                         p.titre AS post_titre
                  FROM " . $this->table . " c
                  LEFT JOIN user u ON c.user_id = u.id
                  LEFT JOIN post p  ON c.post_id = p.id_post
                  WHERE c.id_comment = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all approved comments for a given post (with author info)
     */
    public function getAllByPost($post_id, $only_approved = true) {
        $query = "SELECT c.*, u.nom AS auteur_nom
                  FROM " . $this->table . " c
                  LEFT JOIN user u ON c.user_id = u.id
                  WHERE c.post_id = :post_id";
        if ($only_approved) {
            $query .= " AND c.statut = 'approuve'";
        }
        $query .= " ORDER BY c.date_creation ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get ALL comments (admin) with post title and author info
     */
    public function getAll($statut_filter = '') {
        $query = "SELECT c.*, u.nom AS auteur_nom, u.email AS auteur_email,
                         p.titre AS post_titre
                  FROM " . $this->table . " c
                  LEFT JOIN user u ON c.user_id = u.id
                  LEFT JOIN post p  ON c.post_id = p.id_post
                  WHERE 1=1";

        $params = [];
        if (!empty($statut_filter)) {
            $query .= " AND c.statut = :statut";
            $params[':statut'] = $statut_filter;
        }
        $query .= " ORDER BY c.date_creation DESC";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update a comment
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET contenu = :contenu,
                      statut  = :statut
                  WHERE id_comment = :id_comment";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_comment', $this->id_comment);
        $stmt->bindParam(':contenu',    $this->contenu);
        $stmt->bindParam(':statut',     $this->statut);
        return $stmt->execute();
    }

    /**
     * Delete a comment
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_comment = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Update only the status of a comment (moderation)
     */
    public function updateStatut($id, $statut) {
        $allowed = ['en_attente', 'approuve', 'rejete'];
        if (!in_array($statut, $allowed)) return false;

        $query = "UPDATE " . $this->table . " SET statut = :statut WHERE id_comment = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Count comments by status
     */
    public function countByStatut($statut) {
        $query = "SELECT COUNT(*) AS total FROM " . $this->table . " WHERE statut = :statut";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
}
?>
