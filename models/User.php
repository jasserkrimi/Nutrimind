<?php

class User {
    private $db;
    private $table = 'user';

    public $id;
    public $nom;
    public $email;
    public $mot_de_passe;
    public $role;
    public $date_creation;
    public $last_login;
    public $age;
    public $poids;
    public $taille;
    public $allergique;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        }
    }

    /**
     * Register a new user
     */
    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                  (nom, email, mot_de_passe, role, date_creation) 
                  VALUES 
                  (:nom, :email, :mot_de_passe, :role, NOW())";

        $stmt = $this->db->prepare($query);

        // Hash password
        $this->mot_de_passe = password_hash($this->mot_de_passe, PASSWORD_BCRYPT);

        // Bind values
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':mot_de_passe', $this->mot_de_passe);
        $stmt->bindParam(':role', $this->role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Login user - returns user data if credentials valid
     */
    public function login() {
        $query = "SELECT id, nom, email, mot_de_passe, role, age, poids, taille, allergique, last_login 
                  FROM " . $this->table . " 
                  WHERE email = :email";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($this->mot_de_passe, $result['mot_de_passe'])) {
            
            // Update last_login
            $updateQuery = "UPDATE " . $this->table . " SET last_login = NOW() WHERE id = :id";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bindParam(':id', $result['id']);
            $updateStmt->execute();
            
            return $result;
        }

        return false;
    }

    /**
     * Check if email exists
     */
    public function emailExists() {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all users
     */
    public function getAllUsers() {
        $query = "SELECT id, nom, email, role, date_creation, age, poids, taille, allergique 
                  FROM " . $this->table . " 
                  ORDER BY nom ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateProfile() {
        $query = "UPDATE " . $this->table . " 
                  SET nom = :nom, 
                      age = :age, 
                      poids = :poids, 
                      taille = :taille, 
                      allergique = :allergique 
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        // Bind values with proper type handling for NULL values
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':nom', $this->nom, PDO::PARAM_STR);
        $stmt->bindValue(':age', $this->age, $this->age !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':poids', $this->poids, $this->poids !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':taille', $this->taille, $this->taille !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':allergique', $this->allergique, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Update password
     */
    public function updatePassword() {
        $query = "UPDATE " . $this->table . " 
                  SET mot_de_passe = :mot_de_passe 
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        // Hash new password
        $hashedPassword = password_hash($this->mot_de_passe, PASSWORD_BCRYPT);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':mot_de_passe', $hashedPassword);

        return $stmt->execute();
    }

    /**
     * Validate registration inputs
     */
    public static function validateRegistration($nom, $email, $password, $confirmPassword) {
        $errors = [];

        if (empty($nom)) {
            $errors[] = "Full name is required";
        } elseif (strlen($nom) < 2) {
            $errors[] = "Full name must be at least 2 characters";
        }

        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }

        if (empty($password)) {
            $errors[] = "Password is required";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters";
        }

        if ($password !== $confirmPassword) {
            $errors[] = "Passwords do not match";
        }

        return $errors;
    }

    /**
     * Validate login inputs
     */
    public static function validateLogin($email, $password) {
        $errors = [];

        if (empty($email)) {
            $errors[] = "Email is required";
        }

        if (empty($password)) {
            $errors[] = "Password is required";
        }

        return $errors;
    }

    /**
     * Validate profile update
     */
    public static function validateProfile($nom, $age, $poids, $taille) {
        $errors = [];

        if (empty($nom)) {
            $errors[] = "Full name is required";
        } elseif (strlen($nom) < 2) {
            $errors[] = "Full name must be at least 2 characters";
        }

        if (!empty($age) && (!is_numeric($age) || $age < 1 || $age > 120)) {
            $errors[] = "Age must be a number between 1 and 120";
        }

        if (!empty($poids) && (!is_numeric($poids) || $poids < 1 || $poids > 500)) {
            $errors[] = "Weight must be a number between 1 and 500 kg";
        }

        if (!empty($taille) && (!is_numeric($taille) || $taille < 30 || $taille > 300)) {
            $errors[] = "Height must be a number between 30 and 300 cm";
        }

        return $errors;
    }

    /**
     * Delete user account
     */
    public function deleteAccount($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Get users with pagination and search
     */
    public function getUsersPaginated($page = 1, $perPage = 10, $search = '') {
        $offset = ($page - 1) * $perPage;
        
        $query = "SELECT id, nom, email, role, status, date_creation 
                  FROM " . $this->table;
        
        if (!empty($search)) {
            $query .= " WHERE nom LIKE :search OR email LIKE :search";
        }
        
        $query .= " ORDER BY date_creation DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($search)) {
            $searchParam = '%' . $search . '%';
            $stmt->bindParam(':search', $searchParam);
        }
        
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total count of users with optional search
     */
    public function getTotalUsersCount($search = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        if (!empty($search)) {
            $query .= " WHERE nom LIKE :search OR email LIKE :search";
        }
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($search)) {
            $searchParam = '%' . $search . '%';
            $stmt->bindParam(':search', $searchParam);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int)$result['total'];
    }

    /**
     * Get all users for export (no pagination)
     */
    public function getAllUsersForExport($search = '') {
        $query = "SELECT id, nom, email, role, status, date_creation 
                  FROM " . $this->table;
        
        if (!empty($search)) {
            $query .= " WHERE nom LIKE :search OR email LIKE :search";
        }
        
        $query .= " ORDER BY date_creation DESC";
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($search)) {
            $searchParam = '%' . $search . '%';
            $stmt->bindParam(':search', $searchParam);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate and save password reset code
     */
    public function generateResetCode($email) {
        // Generate 6-digit code
        $resetCode = sprintf("%06d", mt_rand(1, 999999));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Check if user exists
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            return false;
        }
        
        // Save reset code
        $query = "UPDATE " . $this->table . " 
                  SET reset_code = :reset_code, 
                      reset_code_expires = :expires_at 
                  WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':reset_code', $resetCode);
        $stmt->bindParam(':expires_at', $expiresAt);
        $stmt->bindParam(':email', $email);
        
        if ($stmt->execute()) {
            return $resetCode;
        }
        
        return false;
    }

    /**
     * Verify reset code
     */
    public function verifyResetCode($email, $code) {
        $query = "SELECT id, reset_code, reset_code_expires 
                  FROM " . $this->table . " 
                  WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return false;
        }
        
        // Check if code matches
        if ($result['reset_code'] !== $code) {
            return false;
        }
        
        // Check if code is expired
        if (strtotime($result['reset_code_expires']) < time()) {
            return false;
        }
        
        return true;
    }

    /**
     * Reset password with code
     */
    public function resetPasswordWithCode($email, $code, $newPassword) {
        // Verify code first
        if (!$this->verifyResetCode($email, $code)) {
            return false;
        }
        
        // Update password and clear reset code
        $query = "UPDATE " . $this->table . " 
                  SET mot_de_passe = :mot_de_passe, 
                      reset_code = NULL, 
                      reset_code_expires = NULL 
                  WHERE email = :email";
        $stmt = $this->db->prepare($query);
        
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt->bindParam(':mot_de_passe', $hashedPassword);
        $stmt->bindParam(':email', $email);
        
        return $stmt->execute();
    }

    /**
     * Save reset token (for email link method)
     * 
     * @param string $email User email
     * @param string $token Secure token (64 characters)
     * @param string $expires Expiration datetime
     * @return bool
     */
    public function saveResetToken($email, $token, $expires) {
        $query = "UPDATE " . $this->table . " 
                  SET reset_token = :token, 
                      reset_token_expires = :expires 
                  WHERE email = :email";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires', $expires);
        $stmt->bindParam(':email', $email);
        
        return $stmt->execute();
    }

    /**
     * Verify reset token
     * 
     * @param string $token Reset token
     * @return bool
     */
    public function verifyResetToken($token) {
        $query = "SELECT reset_token_expires FROM " . $this->table . " 
                  WHERE reset_token = :token";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return false;
        }
        
        // Check if token has expired
        if (strtotime($result['reset_token_expires']) < time()) {
            return false;
        }
        
        return true;
    }

    /**
     * Reset password with token
     * 
     * @param string $token Reset token
     * @param string $newPassword New password
     * @return bool
     */
    public function resetPasswordWithToken($token, $newPassword) {
        // Verify token first
        if (!$this->verifyResetToken($token)) {
            return false;
        }
        
        // Update password and clear reset token
        $query = "UPDATE " . $this->table . " 
                  SET mot_de_passe = :mot_de_passe, 
                      reset_token = NULL, 
                      reset_token_expires = NULL 
                  WHERE reset_token = :token";
        $stmt = $this->db->prepare($query);
        
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt->bindParam(':mot_de_passe', $hashedPassword);
        $stmt->bindParam(':token', $token);
        
        return $stmt->execute();
    }

    /**
     * Block user account
     */
    public function blockUser($userId) {
        $query = "UPDATE " . $this->table . " SET status = 'blocked' WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    /**
     * Unblock user account
     */
    public function unblockUser($userId) {
        $query = "UPDATE " . $this->table . " SET status = 'active' WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    /**
     * Check if user is blocked
     */
    public function isBlocked($userId) {
        $query = "SELECT status FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && isset($result['status']) && $result['status'] === 'blocked';
    }
}
?>
