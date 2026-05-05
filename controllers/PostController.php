<?php

require_once __DIR__ . '/../models/Post.php';

class PostController {
    private $post;

    public function __construct() {
        $this->post = new Post();
    }

    // ─── Read ────────────────────────────────────────────────────────────

    public function getAll($search = '', $categorie = '', $statut = '') {
        return $this->post->getAll($search, $categorie, $statut);
    }

    public function getAllPublished($search = '', $categorie = '') {
        return $this->post->getAllPublished($search, $categorie);
    }

    public function getById($id) {
        return $this->post->getById($id);
    }

    public function getByUserId($user_id) {
        return $this->post->getByUserId($user_id);
    }

    // ─── Create ──────────────────────────────────────────────────────────

    private function handleImageUpload($file) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'error' => 'Paramètres invalides.'];
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Erreur lors de l\'upload.'];
        }
        if ($file['size'] > 5000000) { // 5MB limit
            return ['success' => false, 'error' => 'Le fichier dépasse la limite de 5 Mo.'];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $ext = array_search(
            $finfo->file($file['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ),
            true
        );

        if (false === $ext) {
            return ['success' => false, 'error' => 'Format de fichier non autorisé (JPG, PNG, GIF).'];
        }

        $filename = sprintf('%s.%s', sha1_file($file['tmp_name']) . '_' . time(), $ext);
        $upload_dir = __DIR__ . '/../views/assets/uploads/posts/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $filepath = $upload_dir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'error' => 'Erreur lors de la sauvegarde du fichier.'];
        }

        return ['success' => true, 'path' => 'assets/uploads/posts/' . $filename];
    }

    public function create($data, $user_id, $file = null) {
        $errors = [];

        // Validate user_id
        if (empty($user_id) || !is_numeric($user_id) || $user_id <= 0) {
            $errors['general'] = 'Utilisateur invalide. Veuillez vous reconnecter.';
            return ['success' => false, 'errors' => $errors];
        }

        // Verify user actually exists in DB (prevents FK constraint error on stale sessions)
        try {
            $database = new Database();
            $conn     = $database->connect();
            $chk      = $conn->prepare('SELECT id FROM user WHERE id = :id');
            $chk->bindParam(':id', $user_id, PDO::PARAM_INT);
            $chk->execute();
            if (!$chk->fetch()) {
                // Stale session - destroy it
                session_unset();
                session_destroy();
                $errors['general'] = 'Votre session est expirée ou invalide. Veuillez vous reconnecter.';
                return ['success' => false, 'errors' => $errors];
            }
        } catch (Exception $e) {
            $errors['general'] = 'Erreur de connexion à la base de données.';
            return ['success' => false, 'errors' => $errors];
        }

        // Validate titre
        if (empty($data['titre'])) {
            $errors['titre'] = 'Le titre est requis.';
        } elseif (strlen(trim($data['titre'])) < 3) {
            $errors['titre'] = 'Le titre doit contenir au moins 3 caractères.';
        } elseif (strlen(trim($data['titre'])) > 200) {
            $errors['titre'] = 'Le titre ne peut pas dépasser 200 caractères.';
        } else {
            $this->post->titre = trim($data['titre']);
        }

        // Validate contenu
        if (empty($data['contenu'])) {
            $errors['contenu'] = 'Le contenu est requis.';
        } elseif (strlen(trim($data['contenu'])) < 10) {
            $errors['contenu'] = 'Le contenu doit contenir au moins 10 caractères.';
        } elseif (strlen(trim($data['contenu'])) > 5000) {
            $errors['contenu'] = 'Le contenu ne peut pas dépasser 5000 caractères.';
        } else {
            $this->post->contenu = trim($data['contenu']);
        }

        // Validate categorie
        $allowed_cats = Post::getCategories();
        if (empty($data['categorie'])) {
            $errors['categorie'] = 'La catégorie est requise.';
        } elseif (!in_array($data['categorie'], $allowed_cats)) {
            $errors['categorie'] = 'Catégorie invalide.';
        } else {
            $this->post->categorie = $data['categorie'];
        }

        // Validate statut
        $allowed_statuts = Post::getStatuts();
        if (!empty($data['statut']) && !in_array($data['statut'], $allowed_statuts)) {
            $errors['statut'] = 'Statut invalide.';
        } else {
            $this->post->statut = isset($data['statut']) && in_array($data['statut'], $allowed_statuts)
                ? $data['statut'] : 'brouillon';
        }

        // Image Upload
        if ($file && isset($file['error']) && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->handleImageUpload($file);
            if (!$uploadResult['success']) {
                $errors['image_url'] = $uploadResult['error'];
            } else {
                $this->post->image_url = $uploadResult['path'];
            }
        } else {
            $this->post->image_url = null;
        }

        $this->post->user_id = $user_id;

        if (empty($errors)) {
            if ($this->post->create()) {
                // Gamification: Add 10 points for creating a post
                require_once __DIR__ . '/../models/User.php';
                $userModel = new User();
                $userModel->addPoints($user_id, 10);
                
                return ['success' => true, 'errors' => [], 'id' => $this->post->id_post];
            }
            return ['success' => false, 'errors' => ['general' => 'Erreur lors de la création du post.']];
        }
        return ['success' => false, 'errors' => $errors];
    }

    // ─── Update ──────────────────────────────────────────────────────────

    public function update($id, $data, $file = null) {
        $errors = [];

        if (empty($id) || !is_numeric($id) || $id <= 0) {
            return ['success' => false, 'errors' => ['general' => 'ID de post invalide.']];
        }

        $existing = $this->post->getById($id);
        if (!$existing) {
            return ['success' => false, 'errors' => ['general' => 'Post non trouvé.']];
        }

        // Validate titre
        if (empty($data['titre'])) {
            $errors['titre'] = 'Le titre est requis.';
        } elseif (strlen(trim($data['titre'])) < 3) {
            $errors['titre'] = 'Le titre doit contenir au moins 3 caractères.';
        } elseif (strlen(trim($data['titre'])) > 200) {
            $errors['titre'] = 'Le titre ne peut pas dépasser 200 caractères.';
        } else {
            $this->post->titre = trim($data['titre']);
        }

        // Validate contenu
        if (empty($data['contenu'])) {
            $errors['contenu'] = 'Le contenu est requis.';
        } elseif (strlen(trim($data['contenu'])) < 10) {
            $errors['contenu'] = 'Le contenu doit contenir au moins 10 caractères.';
        } elseif (strlen(trim($data['contenu'])) > 5000) {
            $errors['contenu'] = 'Le contenu ne peut pas dépasser 5000 caractères.';
        } else {
            $this->post->contenu = trim($data['contenu']);
        }

        // Validate categorie
        $allowed_cats = Post::getCategories();
        if (empty($data['categorie'])) {
            $errors['categorie'] = 'La catégorie est requise.';
        } elseif (!in_array($data['categorie'], $allowed_cats)) {
            $errors['categorie'] = 'Catégorie invalide.';
        } else {
            $this->post->categorie = $data['categorie'];
        }

        // Validate statut
        $allowed_statuts = Post::getStatuts();
        if (!empty($data['statut']) && !in_array($data['statut'], $allowed_statuts)) {
            $errors['statut'] = 'Statut invalide.';
        } else {
            $this->post->statut = isset($data['statut']) && in_array($data['statut'], $allowed_statuts)
                ? $data['statut'] : $existing['statut'];
        }

        // Image Upload
        if ($file && isset($file['error']) && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->handleImageUpload($file);
            if (!$uploadResult['success']) {
                $errors['image_url'] = $uploadResult['error'];
            } else {
                $this->post->image_url = $uploadResult['path'];
            }
        } else {
            $this->post->image_url = $existing['image_url'];
        }

        $this->post->id_post = $id;

        if (empty($errors)) {
            if ($this->post->update()) {
                return ['success' => true, 'errors' => []];
            }
            return ['success' => false, 'errors' => ['general' => 'Erreur lors de la mise à jour du post.']];
        }
        return ['success' => false, 'errors' => $errors];
    }

    // ─── Delete ──────────────────────────────────────────────────────────

    public function delete($id) {
        return $this->post->delete($id);
    }

    // ─── Stats ───────────────────────────────────────────────────────────

    public function countByStatut($statut) {
        return $this->post->countByStatut($statut);
    }
}
?>
