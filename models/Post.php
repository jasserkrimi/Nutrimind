<?php

require_once __DIR__ . '/../config/Database.php';

class Post {
    private $conn;
    private $table = 'post';

    public $id_post;
    public $user_id;
    public $titre;
    public $contenu;
    public $categorie;
    public $image_url;
    public $statut;
    public $date_creation;
    public $date_mise_a_jour;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Create a new post
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (user_id, titre, contenu, categorie, image_url, statut)
                  VALUES
                  (:user_id, :titre, :contenu, :categorie, :image_url, :statut)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id',   $this->user_id);
        $stmt->bindParam(':titre',     $this->titre);
        $stmt->bindParam(':contenu',   $this->contenu);
        $stmt->bindParam(':categorie', $this->categorie);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':statut',    $this->statut);

        try {
            if ($stmt->execute()) {
                $this->id_post = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log('Post::create() PDO error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get post by ID with author info and comment count
     */
    public function getById($id) {
        $query = "SELECT p.*, u.nom AS auteur_nom, u.email AS auteur_email,
                         (SELECT COUNT(*) FROM comment c WHERE c.post_id = p.id_post AND c.statut = 'approuve') AS nb_comments
                  FROM " . $this->table . " p
                  LEFT JOIN user u ON p.user_id = u.id
                  WHERE p.id_post = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all published posts with author info and comment count
     */
    public function getAllPublished($search = '', $categorie = '') {
        $query = "SELECT p.*, u.nom AS auteur_nom,
                         (SELECT COUNT(*) FROM comment c WHERE c.post_id = p.id_post AND c.statut = 'approuve') AS nb_comments
                  FROM " . $this->table . " p
                  LEFT JOIN user u ON p.user_id = u.id
                  WHERE p.statut = 'publie'";

        $params = [];
        if (!empty($search)) {
            $query .= " AND (p.titre LIKE :search OR p.contenu LIKE :search2)";
            $params[':search']  = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        if (!empty($categorie)) {
            $query .= " AND p.categorie = :categorie";
            $params[':categorie'] = $categorie;
        }
        $query .= " ORDER BY p.date_creation DESC";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all posts by a specific user
     */
    public function getByUserId($user_id) {
        $query = "SELECT p.*,
                         (SELECT COUNT(*) FROM comment c WHERE c.post_id = p.id_post) AS nb_comments
                  FROM " . $this->table . " p
                  WHERE p.user_id = :user_id
                  ORDER BY p.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get ALL posts (admin) with author info and comment count
     */
    public function getAll($search = '', $categorie = '', $statut = '') {
        $query = "SELECT p.*, u.nom AS auteur_nom,
                         (SELECT COUNT(*) FROM comment c WHERE c.post_id = p.id_post) AS nb_comments
                  FROM " . $this->table . " p
                  LEFT JOIN user u ON p.user_id = u.id
                  WHERE 1=1";

        $params = [];
        if (!empty($search)) {
            $query .= " AND (p.titre LIKE :search OR p.contenu LIKE :search2)";
            $params[':search']  = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        if (!empty($categorie)) {
            $query .= " AND p.categorie = :categorie";
            $params[':categorie'] = $categorie;
        }
        if (!empty($statut)) {
            $query .= " AND p.statut = :statut";
            $params[':statut'] = $statut;
        }
        $query .= " ORDER BY p.date_creation DESC";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update a post
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET titre     = :titre,
                      contenu   = :contenu,
                      categorie = :categorie,
                      image_url = :image_url,
                      statut    = :statut
                  WHERE id_post = :id_post";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_post',   $this->id_post);
        $stmt->bindParam(':titre',     $this->titre);
        $stmt->bindParam(':contenu',   $this->contenu);
        $stmt->bindParam(':categorie', $this->categorie);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':statut',    $this->statut);
        return $stmt->execute();
    }

    /**
     * Delete a post (cascades to comments)
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_post = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Count posts by status (for dashboard stats)
     */
    public function countByStatut($statut) {
        $query = "SELECT COUNT(*) AS total FROM " . $this->table . " WHERE statut = :statut";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    /**
     * Get distinct categories used
     */
    public static function getCategories() {
        return ['Nutrition', 'Recettes', 'Sport', 'Santé', 'Autre'];
    }

    /**
     * Get distinct statuts
     */
    public static function getStatuts() {
        return ['brouillon', 'publie', 'archive'];
    }
}
?>
