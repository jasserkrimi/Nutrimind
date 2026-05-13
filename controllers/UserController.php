<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->user = new User($this->db);
    }

    /**
     * Handle Sign Up
     */
    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['mot_de_passe'] ?? '';
            $confirmPassword = $_POST['confirm_mot_de_passe'] ?? '';

            // Validate input
            $errors = User::validateRegistration($nom, $email, $password, $confirmPassword);

            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Check if email already exists
            $this->user->email = $email;
            if ($this->user->emailExists()) {
                return ['success' => false, 'errors' => ['Email already registered']];
            }

            // Register user
            $this->user->nom = $nom;
            $this->user->email = $email;
            $this->user->mot_de_passe = $password;
            $this->user->role = 'user'; // Default role

            if ($this->user->register()) {
                // Auto login after registration
                $_SESSION['user_id'] = $this->db->lastInsertId();
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'user';

                return ['success' => true, 'message' => 'Registration successful! Redirecting...'];
            } else {
                return ['success' => false, 'errors' => ['Error registering user']];
            }
        }
    }

    /**
     * Handle Sign In
     */
    public function signin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validate input
            $errors = User::validateLogin($email, $password);

            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Check credentials
            $this->user->email = $email;
            $this->user->mot_de_passe = $password;

            $userData = $this->user->login();

            // Check if account is blocked
            if (is_array($userData) && isset($userData['blocked']) && $userData['blocked'] === true) {
                return [
                    'success' => false, 
                    'blocked' => true,
                    'errors' => ['Votre compte a été bloqué par l\'administrateur. Veuillez contacter le service support pour résoudre ce problème.']
                ];
            }

            if ($userData) {
                // Set session
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['user_nom'] = $userData['nom'];
                $_SESSION['user_email'] = $userData['email'];
                $_SESSION['user_role'] = $userData['role'];
                $_SESSION['last_login'] = $userData['last_login'];
                $_SESSION['logged_in'] = true;

                return ['success' => true, 'message' => 'Login successful! Redirecting...', 'role' => $userData['role']];
            } else {
                return ['success' => false, 'errors' => ['Invalid email or password']];
            }
        }
    }

    /**
     * Handle Logout
     */
    public function logout() {
        $_SESSION = [];
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    /**
     * Handle Profile Update
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                return ['success' => false, 'errors' => ['Not logged in']];
            }

            // Get form data
            $nom = trim($_POST['nom'] ?? '');
            $age = trim($_POST['age'] ?? '');
            $poids = trim($_POST['poids'] ?? '');
            $taille = trim($_POST['taille'] ?? '');
            $allergique = isset($_POST['allergique']) ? 1 : 0;

            // Validate input
            $errors = User::validateProfile($nom, $age, $poids, $taille);

            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Update user with proper type conversion
            $this->user->id = $_SESSION['user_id'];
            $this->user->nom = $nom;
            $this->user->age = !empty($age) ? (int)$age : NULL;
            $this->user->poids = !empty($poids) ? (float)$poids : NULL;
            $this->user->taille = !empty($taille) ? (float)$taille : NULL;
            $this->user->allergique = (int)$allergique;

            if ($this->user->updateProfile()) {
                // Update session
                $_SESSION['user_nom'] = $nom;

                return ['success' => true, 'message' => 'Profil mis à jour avec succès'];
            } else {
                return ['success' => false, 'errors' => ['Failed to update profile. Please try again.']];
            }
        }
    }

    /**
     * Handle Password Change
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                return ['success' => false, 'errors' => ['Not logged in']];
            }

            // Get form data
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validation
            $errors = [];

            if (empty($currentPassword)) {
                $errors[] = 'Current password is required';
            }

            if (empty($newPassword)) {
                $errors[] = 'New password is required';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New passwords do not match';
            }

            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Verify current password
            $userData = $this->user->getUserById($_SESSION['user_id']);

            if (!password_verify($currentPassword, $userData['mot_de_passe'])) {
                return ['success' => false, 'errors' => ['Current password is incorrect']];
            }

            // Update password
            $this->user->id = $_SESSION['user_id'];
            $this->user->mot_de_passe = $newPassword;

            if ($this->user->updatePassword()) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            } else {
                return ['success' => false, 'errors' => ['Error changing password']];
            }
        }
    }

    /**
     * Get User Profile
     */
    public function getProfile() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return $this->user->getUserById($_SESSION['user_id']);
    }

    /**
     * Get all users (for admin use)
     */
    public function getAllUsers() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return [];
        }

        return $this->user->getAllUsers();
    }
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Get current user ID
     */
    public static function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Delete User Account
     */
    public function deleteAccount() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                return ['success' => false, 'errors' => ['Not logged in']];
            }

            // Get form data
            $password = $_POST['delete_password'] ?? '';

            // Validate password
            if (empty($password)) {
                return ['success' => false, 'errors' => ['Password is required']];
            }

            // Verify password
            $userData = $this->user->getUserById($_SESSION['user_id']);

            if (!password_verify($password, $userData['mot_de_passe'])) {
                return ['success' => false, 'errors' => ['Password is incorrect']];
            }

            // Delete user account
            if ($this->user->deleteAccount($_SESSION['user_id'])) {
                // Clear session
                $_SESSION = [];
                session_destroy();

                return ['success' => true, 'message' => 'Account deleted successfully'];
            } else {
                return ['success' => false, 'errors' => ['Error deleting account']];
            }
        }
    }

    /**
     * Admin Delete User
     */
    public function deleteUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in and is admin
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                return ['success' => false, 'errors' => ['Unauthorized access']];
            }

            // Get user ID to delete
            $userId = $_POST['user_id'] ?? '';

            if (empty($userId) || !is_numeric($userId)) {
                return ['success' => false, 'errors' => ['Invalid user ID']];
            }

            // Prevent admin from deleting themselves
            if ((int)$userId === (int)$_SESSION['user_id']) {
                return ['success' => false, 'errors' => ['Vous ne pouvez pas supprimer votre propre compte']];
            }

            // Check if user exists
            $userToDelete = $this->user->getUserById($userId);
            if (!$userToDelete) {
                return ['success' => false, 'errors' => ['Utilisateur non trouvé']];
            }

            // Delete user
            if ($this->user->deleteAccount($userId)) {
                return ['success' => true, 'message' => 'Utilisateur supprimé avec succès'];
            } else {
                return ['success' => false, 'errors' => ['Erreur lors de la suppression de l\'utilisateur']];
            }
        }
    }

    /**
     * Get users with pagination and search
     */
    public function getUsersPaginated() {
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        $users = $this->user->getUsersPaginated($page, $perPage, $search);
        $totalUsers = $this->user->getTotalUsersCount($search);
        $totalPages = ceil($totalUsers / $perPage);

        return [
            'success' => true,
            'users' => $users,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_users' => $totalUsers,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Get all users for export
     */
    public function getAllUsersForExport() {
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $users = $this->user->getAllUsersForExport($search);

        return [
            'success' => true,
            'users' => $users
        ];
    }

    /**
     * Handle Forgot Password Request
     */
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            // Validate email
            if (empty($email)) {
                return ['success' => false, 'errors' => ['L\'adresse e-mail est requise']];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'errors' => ['Format d\'e-mail invalide']];
            }

            // Check if user exists and get their name
            try {
                $userData = $this->user->getUserByEmail($email);
                
                if (!$userData) {
                    return ['success' => false, 'errors' => ['Aucun compte trouvé avec cette adresse e-mail']];
                }

                // Generate reset code
                $resetCode = $this->user->generateResetCode($email);

                if ($resetCode) {
                    // Try to send email with reset code
                    try {
                        require_once __DIR__ . '/../config/EmailSimple.php';
                        $emailService = new EmailSimple();
                        
                        $emailSent = @$emailService->sendPasswordResetCode($email, $resetCode, $userData['nom']);
                        
                        if ($emailSent) {
                            return [
                                'success' => true, 
                                'message' => 'Un code de réinitialisation a été envoyé à votre adresse e-mail. Veuillez vérifier votre boîte de réception (et les spams).'
                            ];
                        } else {
                            // If email fails, still show the code for development/testing
                            return [
                                'success' => true, 
                                'message' => 'Code de réinitialisation généré: <strong>' . $resetCode . '</strong><br><small>(L\'envoi d\'email a échoué. Utilisez ce code pour réinitialiser votre mot de passe. Valide pendant 1 heure.)</small><br><br><small style="color: #999;">💡 Pour activer l\'envoi d\'emails, configurez votre mot de passe Gmail dans config/email_config.php</small>',
                                'code' => $resetCode
                            ];
                        }
                    } catch (Exception $e) {
                        // If email sending throws an exception, still provide the code
                        return [
                            'success' => true, 
                            'message' => 'Code de réinitialisation généré: <strong>' . $resetCode . '</strong><br><small>(L\'envoi d\'email a échoué. Utilisez ce code pour réinitialiser votre mot de passe. Valide pendant 1 heure.)</small>',
                            'code' => $resetCode
                        ];
                    }
                } else {
                    return ['success' => false, 'errors' => ['Erreur lors de la génération du code']];
                }
            } catch (Exception $e) {
                return ['success' => false, 'errors' => ['Une erreur s\'est produite: ' . $e->getMessage()]];
            }
        }
    }

    /**
     * Handle Password Reset
     */
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $resetCode = trim($_POST['reset_code'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validate inputs
            $errors = [];

            if (empty($email)) {
                $errors[] = 'L\'adresse e-mail est requise';
            }

            if (empty($resetCode)) {
                $errors[] = 'Le code de réinitialisation est requis';
            }

            if (empty($newPassword)) {
                $errors[] = 'Le nouveau mot de passe est requis';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Les mots de passe ne correspondent pas';
            }

            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Reset password
            if ($this->user->resetPasswordWithCode($email, $resetCode, $newPassword)) {
                return ['success' => true, 'message' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.'];
            } else {
                return ['success' => false, 'errors' => ['Code de réinitialisation invalide ou expiré']];
            }
        }
    }

    /**
     * Handle Forgot Password with TOKEN (SMTP Email)
     */
    public function forgotPasswordToken() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            // Validate email
            if (empty($email)) {
                return ['success' => false, 'errors' => ['L\'adresse e-mail est requise']];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'errors' => ['Format d\'e-mail invalide']];
            }

            // Check if user exists
            try {
                $userData = $this->user->getUserByEmail($email);
                
                if (!$userData) {
                    // Pour des raisons de sécurité, ne pas révéler si l'email existe
                    return [
                        'success' => true, 
                        'message' => 'Si cette adresse e-mail existe dans notre système, vous recevrez un lien de réinitialisation.'
                    ];
                }

                // Generate secure token
                $token = bin2hex(random_bytes(32)); // 64 caractères
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Save token in database
                if ($this->user->saveResetToken($email, $token, $expires)) {
                    // Send email via SMTP
                    try {
                        require_once __DIR__ . '/../config/EmailSMTP.php';
                        $emailService = new EmailSMTP();
                        
                        $emailSent = $emailService->sendPasswordResetToken($email, $token, $userData['nom']);
                        
                        if ($emailSent) {
                            return [
                                'success' => true, 
                                'message' => 'Un lien de réinitialisation a été envoyé à votre adresse e-mail. Veuillez vérifier votre boîte de réception (et les spams).'
                            ];
                        } else {
                            // Si l'envoi échoue, afficher le lien pour le développement
                            $resetLink = "http://localhost/nutrimind/views/new_password.php?token=" . $token;
                            return [
                                'success' => true, 
                                'message' => 'Lien de réinitialisation généré:<br><a href="' . $resetLink . '" target="_blank" style="color: #1161ee;">Cliquez ici pour réinitialiser</a><br><small>(L\'envoi d\'email a échoué. Utilisez ce lien. Valide pendant 1 heure.)</small><br><br><small style="color: #999;">💡 Pour activer l\'envoi d\'emails SMTP, configurez config/smtp_config.php</small>',
                                'token' => $token
                            ];
                        }
                    } catch (Exception $e) {
                        // En cas d'erreur, fournir quand même le lien
                        $resetLink = "http://localhost/nutrimind/views/new_password.php?token=" . $token;
                        return [
                            'success' => true, 
                            'message' => 'Lien de réinitialisation généré:<br><a href="' . $resetLink . '" target="_blank" style="color: #1161ee;">Cliquez ici pour réinitialiser</a><br><small>(L\'envoi d\'email a échoué. Utilisez ce lien. Valide pendant 1 heure.)</small>',
                            'token' => $token
                        ];
                    }
                } else {
                    return ['success' => false, 'errors' => ['Erreur lors de la génération du lien']];
                }
            } catch (Exception $e) {
                error_log("Erreur forgotPasswordToken: " . $e->getMessage());
                return ['success' => false, 'errors' => ['Une erreur s\'est produite']];
            }
        }
    }

    /**
     * Handle Password Reset with TOKEN
     */
    public function resetPasswordToken() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = trim($_POST['token'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validate inputs
            $errors = [];

            if (empty($token)) {
                $errors[] = 'Token de réinitialisation manquant';
            }

            if (empty($newPassword)) {
                $errors[] = 'Le nouveau mot de passe est requis';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Les mots de passe ne correspondent pas';
            }

            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Verify token and reset password
            if ($this->user->resetPasswordWithToken($token, $newPassword)) {
                return ['success' => true, 'message' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.'];
            } else {
                return ['success' => false, 'errors' => ['Lien de réinitialisation invalide ou expiré']];
            }
        }
    }

    /**
     * Get user segments
     */
    public function getUserSegments() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        require_once __DIR__ . '/../config/EmailCampaignManager.php';
        $campaignManager = new EmailCampaignManager($this->db);
        
        $segments = $campaignManager->segmentUsers();
        
        // Add counts
        $segmentCounts = [];
        foreach ($segments as $key => $users) {
            $segmentCounts[$key] = count($users);
        }
        
        return [
            'success' => true,
            'segments' => $segments,
            'counts' => $segmentCounts
        ];
    }

    /**
     * Generate AI email for a user
     */
    public function generateAIEmail() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userId = $_POST['user_id'] ?? $_GET['user_id'] ?? null;
        $templateType = $_POST['template_type'] ?? $_GET['template_type'] ?? 'tips';
        $tone = $_POST['tone'] ?? $_GET['tone'] ?? 'friendly';

        if (empty($userId)) {
            return ['success' => false, 'errors' => ['User ID is required']];
        }

        require_once __DIR__ . '/../config/EmailCampaignManager.php';
        $campaignManager = new EmailCampaignManager($this->db);
        
        $result = $campaignManager->generateAIEmail($userId, $templateType, $tone);
        
        return $result;
    }

    /**
     * Generate bulk AI emails
     */
    public function generateBulkAIEmails() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        // Get user IDs from POST data
        $userIdsJson = $_POST['user_ids'] ?? '';
        $templateType = $_POST['template_type'] ?? 'tips';
        $tone = $_POST['tone'] ?? 'friendly';

        // Decode JSON string to array
        $userIds = json_decode($userIdsJson, true);

        if (empty($userIds) || !is_array($userIds)) {
            return ['success' => false, 'errors' => ['User IDs are required']];
        }

        require_once __DIR__ . '/../config/EmailCampaignManager.php';
        $campaignManager = new EmailCampaignManager($this->db);
        
        $results = $campaignManager->generateBulkAIEmails($userIds, $templateType, $tone);
        
        return [
            'success' => true,
            'emails' => $results
        ];
    }

    /**
     * Send email campaign
     */
    public function sendEmailCampaign() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userId = $_POST['user_id'] ?? null;
        $subject = $_POST['subject'] ?? '';
        $body = $_POST['body'] ?? '';
        $templateName = $_POST['template_name'] ?? 'custom';
        $aiGenerated = isset($_POST['ai_generated']) ? (bool)$_POST['ai_generated'] : false;

        if (empty($userId) || empty($subject) || empty($body)) {
            return ['success' => false, 'errors' => ['Missing required fields']];
        }

        require_once __DIR__ . '/../config/EmailCampaignManager.php';
        $campaignManager = new EmailCampaignManager($this->db);
        
        $result = $campaignManager->sendCampaign($userId, $subject, $body, $templateName, $aiGenerated);
        
        return $result;
    }

    /**
     * Send bulk email campaign
     */
    public function sendBulkEmailCampaign() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userIds = $_POST['user_ids'] ?? [];
        $subject = $_POST['subject'] ?? '';
        $body = $_POST['body'] ?? '';
        $templateName = $_POST['template_name'] ?? 'custom';
        $aiGenerated = isset($_POST['ai_generated']) ? (bool)$_POST['ai_generated'] : false;

        if (empty($userIds) || !is_array($userIds) || empty($subject) || empty($body)) {
            return ['success' => false, 'errors' => ['Missing required fields']];
        }

        require_once __DIR__ . '/../config/EmailCampaignManager.php';
        $campaignManager = new EmailCampaignManager($this->db);
        
        $results = $campaignManager->sendBulkCampaign($userIds, $subject, $body, $templateName, $aiGenerated);
        
        return [
            'success' => true,
            'results' => $results
        ];
    }

    /**
     * Send personalized bulk campaign (AI-generated, different for each user)
     */
    public function sendPersonalizedBulkCampaign() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $emailData = $_POST['email_data'] ?? [];

        if (empty($emailData) || !is_array($emailData)) {
            return ['success' => false, 'errors' => ['Email data is required']];
        }

        require_once __DIR__ . '/../config/EmailCampaignManager.php';
        $campaignManager = new EmailCampaignManager($this->db);
        
        $results = $campaignManager->sendPersonalizedBulkCampaign($emailData);
        
        return [
            'success' => true,
            'results' => $results
        ];
    }

    /**
     * Get campaign history
     */
    public function getCampaignHistory() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $limit = $_GET['limit'] ?? 50;

        require_once __DIR__ . '/../config/EmailCampaignManager.php';
        $campaignManager = new EmailCampaignManager($this->db);
        
        $history = $campaignManager->getCampaignHistory($limit);
        
        return [
            'success' => true,
            'history' => $history
        ];
    }

    /**
     * Get campaign statistics
     */
    public function getCampaignStats() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        require_once __DIR__ . '/../config/EmailCampaignManager.php';
        $campaignManager = new EmailCampaignManager($this->db);
        
        $stats = $campaignManager->getCampaignStats();
        
        return [
            'success' => true,
            'stats' => $stats
        ];
    }

    /**
     * Block user account
     */
    public function blockUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in and is admin
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                return ['success' => false, 'errors' => ['Unauthorized access']];
            }

            // Get user ID to block
            $userId = $_POST['user_id'] ?? '';

            if (empty($userId) || !is_numeric($userId)) {
                return ['success' => false, 'errors' => ['Invalid user ID']];
            }

            // Prevent admin from blocking themselves
            if ((int)$userId === (int)$_SESSION['user_id']) {
                return ['success' => false, 'errors' => ['Vous ne pouvez pas bloquer votre propre compte']];
            }

            // Check if user exists
            $userToBlock = $this->user->getUserById($userId);
            if (!$userToBlock) {
                return ['success' => false, 'errors' => ['Utilisateur non trouvé']];
            }

            // Block user
            if ($this->user->blockUser($userId)) {
                return ['success' => true, 'message' => 'Utilisateur bloqué avec succès'];
            } else {
                return ['success' => false, 'errors' => ['Erreur lors du blocage de l\'utilisateur']];
            }
        }
    }

    /**
     * Unblock user account
     */
    public function unblockUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in and is admin
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                return ['success' => false, 'errors' => ['Unauthorized access']];
            }

            // Get user ID to unblock
            $userId = $_POST['user_id'] ?? '';

            if (empty($userId) || !is_numeric($userId)) {
                return ['success' => false, 'errors' => ['Invalid user ID']];
            }

            // Check if user exists
            $userToUnblock = $this->user->getUserById($userId);
            if (!$userToUnblock) {
                return ['success' => false, 'errors' => ['Utilisateur non trouvé']];
            }

            // Unblock user
            if ($this->user->unblockUser($userId)) {
                return ['success' => true, 'message' => 'Utilisateur débloqué avec succès'];
            } else {
                return ['success' => false, 'errors' => ['Erreur lors du déblocage de l\'utilisateur']];
            }
        }
    }

    // ==================== USER PROFILE DASHBOARD ====================

    /**
     * Get user profile dashboard data
     */
    public function getUserProfileDashboard() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userId = $_GET['user_id'] ?? '';
        if (empty($userId)) {
            return ['success' => false, 'errors' => ['User ID required']];
        }

        require_once __DIR__ . '/../models/UserProfileDashboard.php';
        $dashboard = new UserProfileDashboard($this->db);

        $data = [
            'user' => $dashboard->getUserProfileData($userId),
            'notes' => $dashboard->getUserNotes($userId),
            'tags' => $dashboard->getUserTags($userId),
            'activity' => $dashboard->getUserActivity($userId, 30),
            'activity_stats' => $dashboard->getUserActivityStats($userId),
            'email_history' => $dashboard->getUserEmailHistory($userId, 10),
            'engagement_score' => $dashboard->calculateEngagementScore($userId)
        ];

        return ['success' => true, 'data' => $data];
    }

    /**
     * Add note to user
     */
    public function addUserNote() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'errors' => ['Invalid request']];
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userId = $_POST['user_id'] ?? '';
        $note = trim($_POST['note'] ?? '');

        if (empty($userId) || empty($note)) {
            return ['success' => false, 'errors' => ['User ID and note are required']];
        }

        require_once __DIR__ . '/../models/UserProfileDashboard.php';
        $dashboard = new UserProfileDashboard($this->db);

        if ($dashboard->addNote($userId, $_SESSION['user_id'], $note)) {
            return ['success' => true, 'message' => 'Note ajoutée avec succès'];
        }

        return ['success' => false, 'errors' => ['Erreur lors de l\'ajout de la note']];
    }

    /**
     * Delete user note
     */
    public function deleteUserNote() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'errors' => ['Invalid request']];
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $noteId = $_POST['note_id'] ?? '';

        if (empty($noteId)) {
            return ['success' => false, 'errors' => ['Note ID required']];
        }

        require_once __DIR__ . '/../models/UserProfileDashboard.php';
        $dashboard = new UserProfileDashboard($this->db);

        if ($dashboard->deleteNote($noteId)) {
            return ['success' => true, 'message' => 'Note supprimée avec succès'];
        }

        return ['success' => false, 'errors' => ['Erreur lors de la suppression de la note']];
    }

    /**
     * Add tag to user
     */
    public function addUserTag() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'errors' => ['Invalid request']];
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userId = $_POST['user_id'] ?? '';
        $tagName = trim($_POST['tag_name'] ?? '');
        $tagColor = $_POST['tag_color'] ?? 'blue';

        if (empty($userId) || empty($tagName)) {
            return ['success' => false, 'errors' => ['User ID and tag name are required']];
        }

        require_once __DIR__ . '/../models/UserProfileDashboard.php';
        $dashboard = new UserProfileDashboard($this->db);

        if ($dashboard->addTag($userId, $tagName, $tagColor)) {
            return ['success' => true, 'message' => 'Tag ajouté avec succès'];
        }

        return ['success' => false, 'errors' => ['Tag déjà existant ou erreur']];
    }

    /**
     * Delete user tag
     */
    public function deleteUserTag() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'errors' => ['Invalid request']];
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $tagId = $_POST['tag_id'] ?? '';

        if (empty($tagId)) {
            return ['success' => false, 'errors' => ['Tag ID required']];
        }

        require_once __DIR__ . '/../models/UserProfileDashboard.php';
        $dashboard = new UserProfileDashboard($this->db);

        if ($dashboard->deleteTag($tagId)) {
            return ['success' => true, 'message' => 'Tag supprimé avec succès'];
        }

        return ['success' => false, 'errors' => ['Erreur lors de la suppression du tag']];
    }

    // ==================== AI-POWERED FEATURES ====================

    /**
     * Generate AI insights for user
     */
    public function generateUserInsights() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? '';
        if (empty($userId)) {
            return ['success' => false, 'errors' => ['User ID required']];
        }

        require_once __DIR__ . '/../models/UserProfileDashboard.php';
        require_once __DIR__ . '/../config/ProfileDashboardAI.php';

        $dashboard = new UserProfileDashboard($this->db);
        $ai = new ProfileDashboardAI();

        $userData = $dashboard->getUserProfileData($userId);
        $activityStats = $dashboard->getUserActivityStats($userId);
        $engagementScore = $dashboard->calculateEngagementScore($userId);

        return $ai->generateUserInsights($userData, $activityStats, $engagementScore);
    }

    /**
     * Get AI note suggestions
     */
    public function getAINoteSuggestions() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? '';
        if (empty($userId)) {
            return ['success' => false, 'errors' => ['User ID required']];
        }

        require_once __DIR__ . '/../models/UserProfileDashboard.php';
        require_once __DIR__ . '/../config/ProfileDashboardAI.php';

        $dashboard = new UserProfileDashboard($this->db);
        $ai = new ProfileDashboardAI();

        $userData = $dashboard->getUserProfileData($userId);
        $recentActivity = $dashboard->getUserActivity($userId, 10);
        $engagementScore = $dashboard->calculateEngagementScore($userId);

        return $ai->suggestNotes($userData, $recentActivity, $engagementScore);
    }

    /**
     * Get AI tag recommendations
     */
    public function getAITagRecommendations() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? '';
        if (empty($userId)) {
            return ['success' => false, 'errors' => ['User ID required']];
        }

        require_once __DIR__ . '/../models/UserProfileDashboard.php';
        require_once __DIR__ . '/../config/ProfileDashboardAI.php';

        $dashboard = new UserProfileDashboard($this->db);
        $ai = new ProfileDashboardAI();

        $userData = $dashboard->getUserProfileData($userId);
        $activityStats = $dashboard->getUserActivityStats($userId);
        $engagementScore = $dashboard->calculateEngagementScore($userId);

        return $ai->recommendTags($userData, $activityStats, $engagementScore);
    }

    /**
     * Analyze user behavior patterns
     */
    public function analyzeUserBehavior() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? '';
        if (empty($userId)) {
            return ['success' => false, 'errors' => ['User ID required']];
        }

        require_once __DIR__ . '/../models/UserProfileDashboard.php';
        require_once __DIR__ . '/../config/ProfileDashboardAI.php';

        $dashboard = new UserProfileDashboard($this->db);
        $ai = new ProfileDashboardAI();

        $userData = $dashboard->getUserProfileData($userId);
        $activities = $dashboard->getUserActivity($userId, 30);

        return $ai->analyzeBehaviorPatterns($activities, $userData);
    }

    /**
     * Generate health recommendations
     */
    public function generateHealthRecommendations() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? '';
        if (empty($userId)) {
            return ['success' => false, 'errors' => ['User ID required']];
        }

        require_once __DIR__ . '/../models/UserProfileDashboard.php';
        require_once __DIR__ . '/../config/ProfileDashboardAI.php';

        $dashboard = new UserProfileDashboard($this->db);
        $ai = new ProfileDashboardAI();

        $userData = $dashboard->getUserProfileData($userId);

        return $ai->generateHealthRecommendations($userData);
    }

    /**
     * Generate profile summary
     */
    public function generateProfileSummary() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Unauthorized access']];
        }

        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? '';
        if (empty($userId)) {
            return ['success' => false, 'errors' => ['User ID required']];
        }

        require_once __DIR__ . '/../models/UserProfileDashboard.php';
        require_once __DIR__ . '/../config/ProfileDashboardAI.php';

        $dashboard = new UserProfileDashboard($this->db);
        $ai = new ProfileDashboardAI();

        $userData = $dashboard->getUserProfileData($userId);
        $notes = $dashboard->getUserNotes($userId);
        $tags = $dashboard->getUserTags($userId);
        $activityStats = $dashboard->getUserActivityStats($userId);

        return $ai->generateProfileSummary($userData, $notes, $tags, $activityStats);
    }
}

if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
    ob_start(); // Buffer any stray output (warnings, notices, connection errors)

    // Handle requests
    $method = $_POST['action'] ?? $_GET['action'] ?? null;

    $controller = new UserController();
    $response = ['success' => false, 'message' => 'Invalid action'];

    switch ($method) {
        case 'signup':
            $response = $controller->signup();
            break;

        case 'signin':
            $response = $controller->signin();
            break;

        case 'logout':
            $response = $controller->logout();
            break;

        case 'update_profile':
            $response = $controller->updateProfile();
            break;

        case 'change_password':
            $response = $controller->changePassword();
            break;

        case 'delete_account':
            $response = $controller->deleteAccount();
            break;

        case 'delete_user':
            $response = $controller->deleteUser();
            break;

        case 'get_users_paginated':
            $response = $controller->getUsersPaginated();
            break;

        case 'get_all_users_export':
            $response = $controller->getAllUsersForExport();
            break;

        case 'forgot_password':
            $response = $controller->forgotPassword();
            break;

        case 'reset_password':
            $response = $controller->resetPassword();
            break;

        case 'forgot_password_token':
            $response = $controller->forgotPasswordToken();
            break;

        case 'reset_password_token':
            $response = $controller->resetPasswordToken();
            break;

        case 'get_profile':
            $response = ['success' => true, 'data' => $controller->getProfile()];
            break;

        case 'get_user_segments':
            $response = $controller->getUserSegments();
            break;

        case 'generate_ai_email':
            $response = $controller->generateAIEmail();
            break;

        case 'generate_bulk_ai_emails':
            $response = $controller->generateBulkAIEmails();
            break;

        case 'send_email_campaign':
            $response = $controller->sendEmailCampaign();
            break;

        case 'send_bulk_email_campaign':
            $response = $controller->sendBulkEmailCampaign();
            break;

        case 'send_personalized_bulk_campaign':
            $response = $controller->sendPersonalizedBulkCampaign();
            break;

        case 'get_campaign_history':
            $response = $controller->getCampaignHistory();
            break;

        case 'get_campaign_stats':
            $response = $controller->getCampaignStats();
            break;

        case 'block_user':
            $response = $controller->blockUser();
            break;

        case 'unblock_user':
            $response = $controller->unblockUser();
            break;

        case 'get_user_profile_dashboard':
            $response = $controller->getUserProfileDashboard();
            break;

        case 'add_user_note':
            $response = $controller->addUserNote();
            break;

        case 'delete_user_note':
            $response = $controller->deleteUserNote();
            break;

        case 'add_user_tag':
            $response = $controller->addUserTag();
            break;

        case 'delete_user_tag':
            $response = $controller->deleteUserTag();
            break;

        case 'generate_user_insights':
            $response = $controller->generateUserInsights();
            break;

        case 'get_ai_note_suggestions':
            $response = $controller->getAINoteSuggestions();
            break;

        case 'get_ai_tag_recommendations':
            $response = $controller->getAITagRecommendations();
            break;

        case 'analyze_user_behavior':
            $response = $controller->analyzeUserBehavior();
            break;

        case 'generate_health_recommendations':
            $response = $controller->generateHealthRecommendations();
            break;

        case 'generate_profile_summary':
            $response = $controller->generateProfileSummary();
            break;

        default:
            // If it's a POST request but no action specified, try to determine from form
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Check for hidden field or form context
                if (isset($_POST['nom']) && isset($_POST['mot_de_passe']) && !isset($_POST['age'])) {
                    // Likely signup
                    $response = $controller->signup();
                } elseif (isset($_POST['email']) && isset($_POST['password']) && !isset($_POST['confirm_mot_de_passe'])) {
                    // Likely signin
                    $response = $controller->signin();
                }
            }
    }

    // Return JSON response
    ob_end_clean(); // Discard any stray output before sending JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
