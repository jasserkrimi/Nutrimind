<?php

require_once __DIR__ . '/EmailSMTP.php';
require_once __DIR__ . '/EmailAPI.php';
require_once __DIR__ . '/AIEmailGenerator.php';

class EmailCampaignManager {
    private $db;
    private $emailSender;
    private $emailAPI;
    private $aiGenerator;

    public function __construct($db) {
        $this->db = $db;
        $this->emailSender = new EmailSMTP(); // Changed from EmailSimple to EmailSMTP
        $this->emailAPI = new EmailAPI();
        $this->aiGenerator = new AIEmailGenerator();
    }

    /**
     * Segment users based on their activity and profile
     */
    public function segmentUsers() {
        $query = "SELECT id, nom, email, role, date_creation, last_login, age, poids, taille, allergique 
                  FROM user 
                  ORDER BY date_creation DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $segments = [
            'super_active' => [],
            'vip' => [],
            'new_users' => [],
            'dormant' => [],
            'at_risk' => [],
            'incomplete_profile' => [],
            'allergy_alert' => []
        ];

        foreach ($users as $user) {
            $daysSinceRegistration = $this->getDaysSince($user['date_creation']);
            $daysSinceLastLogin = $user['last_login'] ? $this->getDaysSince($user['last_login']) : null;
            $profileCompletion = $this->calculateProfileCompletion($user);

            // Super Active: logged in last 7 days + complete profile
            if ($daysSinceLastLogin !== null && $daysSinceLastLogin <= 7 && $profileCompletion >= 80) {
                $segments['super_active'][] = $user;
            }

            // VIP: Admins
            if ($user['role'] === 'admin') {
                $segments['vip'][] = $user;
            }

            // New Users: registered less than 7 days ago
            if ($daysSinceRegistration <= 7) {
                $segments['new_users'][] = $user;
            }

            // Dormant: no login for 14-30 days
            if ($daysSinceLastLogin !== null && $daysSinceLastLogin > 14 && $daysSinceLastLogin <= 30) {
                $segments['dormant'][] = $user;
            }

            // At Risk: no login for 30+ days
            if ($daysSinceLastLogin !== null && $daysSinceLastLogin > 30) {
                $segments['at_risk'][] = $user;
            }

            // Incomplete Profile: less than 70% complete
            if ($profileCompletion < 70) {
                $segments['incomplete_profile'][] = $user;
            }

            // Allergy Alert: users with allergies
            if (!empty($user['allergique']) && $user['allergique'] == 1) {
                $segments['allergy_alert'][] = $user;
            }
        }

        return $segments;
    }

    /**
     * Get users by segment type
     */
    public function getUsersBySegment($segmentType) {
        $segments = $this->segmentUsers();
        return $segments[$segmentType] ?? [];
    }

    /**
     * Generate AI email for a user
     */
    public function generateAIEmail($userId, $templateType, $tone = 'friendly') {
        // Get user data
        $user = $this->getUserData($userId);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }

        // Generate email using AI
        $result = $this->aiGenerator->generateEmail($user, $templateType, $tone);
        
        return $result;
    }

    /**
     * Generate AI emails for multiple users
     */
    public function generateBulkAIEmails($userIds, $templateType, $tone = 'friendly') {
        $users = [];
        foreach ($userIds as $userId) {
            $user = $this->getUserData($userId);
            if ($user) {
                $users[] = $user;
            }
        }

        return $this->aiGenerator->generateBulkEmails($users, $templateType, $tone);
    }

    /**
     * Send email campaign
     */
    public function sendCampaign($userId, $subject, $body, $templateName = 'custom', $aiGenerated = false) {
        // Get user data
        $user = $this->getUserData($userId);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }

        // Try SMTP first (primary method)
        $emailSent = $this->emailSender->sendEmail(
            $user['email'],
            $user['nom'],
            $subject,
            $body
        );

        // If SMTP fails, try API as fallback
        if (!$emailSent) {
            $emailSent = $this->emailAPI->sendEmail(
                $user['email'],
                $user['nom'],
                $subject,
                $body
            );
        }

        // Log campaign
        $status = $emailSent ? 'sent' : 'failed';
        $this->logCampaign($userId, $templateName, $subject, $body, $status, $aiGenerated);

        return [
            'success' => $emailSent,
            'message' => $emailSent ? 'Email envoyé avec succès!' : 'Échec de l\'envoi de l\'email',
            'user_email' => $user['email'],
            'user_name' => $user['nom']
        ];
    }

    /**
     * Send bulk campaign
     */
    public function sendBulkCampaign($userIds, $subject, $body, $templateName = 'custom', $aiGenerated = false) {
        $results = [];
        
        foreach ($userIds as $userId) {
            $result = $this->sendCampaign($userId, $subject, $body, $templateName, $aiGenerated);
            $results[] = $result;
            
            // Small delay between emails
            usleep(100000); // 0.1 second
        }

        return $results;
    }

    /**
     * Send personalized bulk campaign (different email for each user)
     */
    public function sendPersonalizedBulkCampaign($emailData) {
        $results = [];
        
        foreach ($emailData as $data) {
            if (isset($data['user_id']) && isset($data['email_data'])) {
                $emailResult = $data['email_data'];
                
                if ($emailResult['success']) {
                    $result = $this->sendCampaign(
                        $data['user_id'],
                        $emailResult['subject'],
                        $emailResult['body'],
                        'ai_generated',
                        true
                    );
                    $results[] = $result;
                } else {
                    $results[] = [
                        'success' => false,
                        'user_id' => $data['user_id'],
                        'error' => 'AI generation failed'
                    ];
                }
            }
            
            // Small delay between emails
            usleep(100000); // 0.1 second
        }

        return $results;
    }

    /**
     * Get campaign history
     */
    public function getCampaignHistory($limit = 50) {
        $query = "SELECT ec.*, u.nom, u.email 
                  FROM email_campaigns ec
                  JOIN user u ON ec.user_id = u.id
                  ORDER BY ec.sent_at DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get campaign statistics
     */
    public function getCampaignStats() {
        $query = "SELECT 
                    COUNT(*) as total_sent,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as successful,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN ai_generated = 1 THEN 1 ELSE 0 END) as ai_generated
                  FROM email_campaigns";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Log campaign to database
     */
    private function logCampaign($userId, $templateName, $subject, $body, $status, $aiGenerated = false) {
        $query = "INSERT INTO email_campaigns 
                  (user_id, template_name, subject, content, status, ai_generated, sent_at) 
                  VALUES 
                  (:user_id, :template_name, :subject, :content, :status, :ai_generated, NOW())";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':template_name', $templateName);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':content', $body);
        $stmt->bindParam(':status', $status);
        $stmt->bindValue(':ai_generated', $aiGenerated ? 1 : 0, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Get user data
     */
    private function getUserData($userId) {
        $query = "SELECT * FROM user WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate days since a date
     */
    private function getDaysSince($date) {
        if (empty($date)) return null;
        
        $dateObj = new DateTime($date);
        $now = new DateTime();
        return $now->diff($dateObj)->days;
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($user) {
        $totalFields = 7;
        $filledFields = 2; // nom and email always filled
        
        if (!empty($user['age'])) $filledFields++;
        if (!empty($user['poids'])) $filledFields++;
        if (!empty($user['taille'])) $filledFields++;
        if (isset($user['allergique'])) $filledFields++;
        if (!empty($user['role'])) $filledFields++;
        
        return round(($filledFields / $totalFields) * 100);
    }
}
?>
