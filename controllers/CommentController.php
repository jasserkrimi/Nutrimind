<?php

require_once __DIR__ . '/../models/Comment.php';

class CommentController {
    private $comment;

    public function __construct() {
        $this->comment = new Comment();
    }

    // ─── Read ────────────────────────────────────────────────────────────

    public function getAllByPost($post_id, $only_approved = true) {
        return $this->comment->getAllByPost($post_id, $only_approved);
    }

    public function getAll($statut_filter = '', $post_id_filter = null) {
        return $this->comment->getAll($statut_filter, $post_id_filter);
    }

    public function getById($id) {
        return $this->comment->getById($id);
    }

    // ─── Create ──────────────────────────────────────────────────────────

    public function create($data, $post_id, $user_id) {
        $errors = [];

        // Validate post_id
        if (empty($post_id) || !is_numeric($post_id) || $post_id <= 0) {
            $errors['general'] = 'Post invalide.';
            return ['success' => false, 'errors' => $errors];
        }

        // Validate user_id
        if (empty($user_id) || !is_numeric($user_id) || $user_id <= 0) {
            $errors['general'] = 'Utilisateur invalide.';
            return ['success' => false, 'errors' => $errors];
        }

        // Validate contenu
        if (empty($data['contenu'])) {
            $errors['contenu'] = 'Le commentaire ne peut pas être vide.';
        } elseif (strlen(trim($data['contenu'])) < 2) {
            $errors['contenu'] = 'Le commentaire doit contenir au moins 2 caractères.';
        } elseif (strlen(trim($data['contenu'])) > 1000) {
            $errors['contenu'] = 'Le commentaire ne peut pas dépasser 1000 caractères.';
        } else {
            $this->comment->contenu = trim($data['contenu']);
        }

        $this->comment->post_id = $post_id;
        $this->comment->user_id = $user_id;
        $this->comment->statut  = 'approuve'; // auto-approve for front

        if (empty($errors)) {
            if ($this->comment->create()) {
                return ['success' => true, 'errors' => [], 'id' => $this->comment->id_comment];
            }
            return ['success' => false, 'errors' => ['general' => 'Erreur lors de l\'ajout du commentaire.']];
        }
        return ['success' => false, 'errors' => $errors];
    }

    // ─── Update ──────────────────────────────────────────────────────────

    public function update($id, $data) {
        $errors = [];

        if (empty($id) || !is_numeric($id) || $id <= 0) {
            return ['success' => false, 'errors' => ['general' => 'ID invalide.']];
        }

        $existing = $this->comment->getById($id);
        if (!$existing) {
            return ['success' => false, 'errors' => ['general' => 'Commentaire non trouvé.']];
        }

        // Validate contenu
        if (empty($data['contenu'])) {
            $errors['contenu'] = 'Le commentaire ne peut pas être vide.';
        } elseif (strlen(trim($data['contenu'])) < 2) {
            $errors['contenu'] = 'Le commentaire doit contenir au moins 2 caractères.';
        } elseif (strlen(trim($data['contenu'])) > 1000) {
            $errors['contenu'] = 'Le commentaire ne peut pas dépasser 1000 caractères.';
        } else {
            $this->comment->contenu = trim($data['contenu']);
        }

        // Validate statut (admin only)
        $allowed_statuts = ['en_attente', 'approuve', 'rejete'];
        if (!empty($data['statut']) && !in_array($data['statut'], $allowed_statuts)) {
            $errors['statut'] = 'Statut invalide.';
        } else {
            $this->comment->statut = isset($data['statut']) && in_array($data['statut'], $allowed_statuts)
                ? $data['statut'] : $existing['statut'];
        }

        $this->comment->id_comment = $id;

        if (empty($errors)) {
            if ($this->comment->update()) {
                return ['success' => true, 'errors' => []];
            }
            return ['success' => false, 'errors' => ['general' => 'Erreur lors de la mise à jour.']];
        }
        return ['success' => false, 'errors' => $errors];
    }

    // ─── Moderation ──────────────────────────────────────────────────────

    public function moderate($id, $statut) {
        $allowed = ['en_attente', 'approuve', 'rejete'];
        if (!in_array($statut, $allowed)) {
            return false;
        }
        return $this->comment->updateStatut($id, $statut);
    }

    // ─── Delete ──────────────────────────────────────────────────────────

    public function delete($id) {
        return $this->comment->delete($id);
    }

    // ─── Stats ───────────────────────────────────────────────────────────

    public function countByStatut($statut) {
        return $this->comment->countByStatut($statut);
    }
}
?>
