<?php
// LoginService.php
class LoginService {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function handleLogin($post, $ip) {
        $errors = [];
        
        // Validate CSRF token
        if (!isset($post['csrf_token']) || !validateCSRFToken($post['csrf_token'])) {
            logActivity($this->pdo, 0, 'Unknown', 'Guest', 'staff_login.php', 'CSRF validation failed');
            return ['success' => false, 'errors' => ['Security validation failed. Please refresh the page and try again.']];
        }
        
        // Check rate limiting
        if (hasTooManyLoginAttempts($this->pdo, $ip)) {
            logActivity($this->pdo, 0, 'Unknown', 'Guest', 'staff_login.php', "Too many login attempts from $ip");
            return ['success' => false, 'errors' => ['Too many failed attempts. Please try again later.']];
        }
        
        // Validate required fields
        $email = isset($post['email']) ? trim($post['email']) : '';
        $password = isset($post['password']) ? $post['password'] : '';
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'errors' => ['Please enter a valid email address.']];
        }
        
        if (empty($password)) {
            return ['success' => false, 'errors' => ['Please enter your password.']];
        }
        
        // Get user by email
        try {
            $user = $this->getUserByEmail($email);
            
            // Check if user exists and verify password
            if (!$user || !password_verify($password, $user['Password'])) {
                $this->recordLoginAttempt($ip);
                logActivity($this->pdo, 0, 'Unknown', 'Guest', 'staff_login.php', "Failed login for $email");
                return ['success' => false, 'errors' => ['Incorrect email or password.']];
            }
            
            // Check if email is verified
            if (!$user['EmailVerified']) {
                return ['success' => false, 'errors' => ['Please verify your email before logging in.']];
            }
            
            // Initialize session with user data
            $this->initializeSession($user);
            
            // Log successful login
            logActivity($this->pdo, $user['UserId'], $user['FirstName'] . ' ' . $user['LastName'], $user['RoleName'], 'staff_login.php', 'Successful login');
            
            // Determine redirect based on onboarding status
            if (!$user['OnboardingComplete']) {
                $redirect = $user['RoleName'] === 'Super Admin' 
                    ? '../public/onboarding_role_creation.php' 
                    : '../public/onboarding_new_password.php';
            } else {
                $redirect = '../public/dashboard.php';
            }
            
            return ['success' => true, 'redirect' => $redirect];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['An error occurred. Please try again later.']];
        }
    }
    
    private function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("
            SELECT 
                u.UserId, u.FirstName, u.LastName, u.Email, u.Password, 
                u.EmailVerified, u.OnboardingComplete, u.RoleId,
                r.RoleName
            FROM Users u
            JOIN Roles r ON u.RoleId = r.RoleId
            WHERE u.Email = :email
        ");
        
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function recordLoginAttempt($ip) {
        $stmt = $this->pdo->prepare("
            INSERT INTO LoginAttempts (IpAddress, AttemptTime)
            VALUES (:ip, NOW())
        ");
        
        $stmt->execute([':ip' => $ip]);
    }
    
    private function initializeSession($user) {
        // Generate a new session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Store user information in session
        $_SESSION['UserId'] = $user['UserId'];
        $_SESSION['FirstName'] = $user['FirstName'];
        $_SESSION['LastName'] = $user['LastName'];
        $_SESSION['Email'] = $user['Email']; // Ensure consistent casing
        $_SESSION['RoleId'] = $user['RoleId'];
        $_SESSION['Role'] = $user['RoleName'];
        $_SESSION['LoggedIn'] = true;
        $_SESSION['OnboardingComplete'] = $user['OnboardingComplete'];
        
        // Get user permissions
        $stmt = $this->pdo->prepare("
            SELECT LOWER(p.PermissionName)
            FROM Permissions p
            JOIN RolePermissions rp ON p.PermissionId = rp.PermissionId
            WHERE rp.RoleId = :roleId
        ");
        
        $stmt->execute([':roleId' => $user['RoleId']]);
        $_SESSION['Permissions'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Update last login time
        $this->updateLastLogin($user['UserId']);
    }
    
    private function updateLastLogin($userId) {
        $stmt = $this->pdo->prepare("
            UPDATE Users 
            SET LastLoginAt = NOW() 
            WHERE UserId = :userId
        ");
        
        $stmt->execute([':userId' => $userId]);
    }
}