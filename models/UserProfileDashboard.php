<?php

class UserProfileDashboard {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // ==================== NOTES ====================
    
    /**
     * Add note to user
     */
    public function addNote($userId, $adminId, $note) {
        $query = "INSERT INTO user_notes (user_id, admin_id, note) VALUES (:user_id, :admin_id, :note)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':admin_id', $adminId);
        $stmt->bindParam(':note', $note);
        return $stmt->execute();
    }

    /**
     * Get all notes for a user
     */
    public function getUserNotes($userId) {
        $query = "SELECT un.*, u.nom as admin_name 
                  FROM user_notes un
                  JOIN user u ON un.admin_id = u.id
                  WHERE un.user_id = :user_id
                  ORDER BY un.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete note
     */
    public function deleteNote($noteId) {
        $query = "DELETE FROM user_notes WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $noteId);
        return $stmt->execute();
    }

    // ==================== TAGS ====================
    
    /**
     * Add tag to user
     */
    public function addTag($userId, $tagName, $tagColor = 'blue') {
        // Check if tag already exists
        $checkQuery = "SELECT id FROM user_tags WHERE user_id = :user_id AND tag_name = :tag_name";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':user_id', $userId);
        $checkStmt->bindParam(':tag_name', $tagName);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            return false; // Tag already exists
        }
        
        $query = "INSERT INTO user_tags (user_id, tag_name, tag_color) VALUES (:user_id, :tag_name, :tag_color)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':tag_name', $tagName);
        $stmt->bindParam(':tag_color', $tagColor);
        return $stmt->execute();
    }

    /**
     * Get all tags for a user
     */
    public function getUserTags($userId) {
        $query = "SELECT * FROM user_tags WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete tag
     */
    public function deleteTag($tagId) {
        $query = "DELETE FROM user_tags WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $tagId);
        return $stmt->execute();
    }

    /**
     * Get all unique tags
     */
    public function getAllTags() {
        $query = "SELECT DISTINCT tag_name, tag_color FROM user_tags ORDER BY tag_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================== ACTIVITY LOG ====================
    
    /**
     * Log user activity
     */
    public function logActivity($userId, $activityType, $description = '') {
        $query = "INSERT INTO user_activity_log (user_id, activity_type, activity_description) 
                  VALUES (:user_id, :activity_type, :description)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':activity_type', $activityType);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    /**
     * Get user activity timeline
     */
    public function getUserActivity($userId, $limit = 30) {
        $query = "SELECT * FROM user_activity_log 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get activity stats for user
     */
    public function getUserActivityStats($userId) {
        // Total activities
        $query = "SELECT COUNT(*) as total_activities FROM user_activity_log WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total_activities'];

        // Activities last 7 days
        $query = "SELECT COUNT(*) as recent_activities FROM user_activity_log 
                  WHERE user_id = :user_id AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $recent = $stmt->fetch(PDO::FETCH_ASSOC)['recent_activities'];

        // Most common activity
        $query = "SELECT activity_type, COUNT(*) as count FROM user_activity_log 
                  WHERE user_id = :user_id 
                  GROUP BY activity_type 
                  ORDER BY count DESC 
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $mostCommon = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total_activities' => $total,
            'recent_activities' => $recent,
            'most_common_activity' => $mostCommon ? $mostCommon['activity_type'] : 'None'
        ];
    }

    // ==================== USER PROFILE DATA ====================
    
    /**
     * Get complete user profile data
     */
    public function getUserProfileData($userId) {
        $query = "SELECT * FROM user WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user email history
     */
    public function getUserEmailHistory($userId, $limit = 10) {
        $query = "SELECT * FROM email_campaigns 
                  WHERE user_id = :user_id 
                  ORDER BY sent_at DESC 
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate user engagement score (0-100)
     */
    public function calculateEngagementScore($userId) {
        $user = $this->getUserProfileData($userId);
        $score = 0;

        // Profile completion (40 points)
        $profileFields = ['age', 'poids', 'taille', 'allergique'];
        $filledFields = 0;
        foreach ($profileFields as $field) {
            if (!empty($user[$field])) $filledFields++;
        }
        $score += ($filledFields / count($profileFields)) * 40;

        // Recent activity (30 points)
        if (!empty($user['last_login'])) {
            $daysSinceLogin = (time() - strtotime($user['last_login'])) / 86400;
            if ($daysSinceLogin <= 1) $score += 30;
            elseif ($daysSinceLogin <= 7) $score += 20;
            elseif ($daysSinceLogin <= 30) $score += 10;
        }

        // Activity count (30 points)
        $stats = $this->getUserActivityStats($userId);
        if ($stats['recent_activities'] >= 10) $score += 30;
        elseif ($stats['recent_activities'] >= 5) $score += 20;
        elseif ($stats['recent_activities'] >= 1) $score += 10;

        return round($score);
    }
}
?>
