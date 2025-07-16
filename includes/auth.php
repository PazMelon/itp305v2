<?php
require_once 'config.php';
require_once __DIR__ . '/../functions/sys_log_func.php';

class Auth
{
    private $conn;
    private $syslog;

    public function __construct($conn, $syslog)
    {
        $this->conn = $conn;
        $this->syslog = $syslog;
    }

    // Register new user with validation
    public function registerUser($username, $password, $firstName, $lastName)
    {
        // Validate input
        if (empty($username) || empty($password) || empty($firstName) || empty($lastName)) {
            throw new Exception("All fields are required.");
        }

        if (strlen($username) < 4 || strlen($username) > 20) {
            throw new Exception("Username must be 4-20 characters long.");
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new Exception("Username can only contain letters, numbers, and underscores.");
        }

        if (strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }

        // Check if username exists
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            throw new Exception("Username already taken.");
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Insert user
        $stmt = $this->conn->prepare("INSERT INTO users (username, password, first_name, last_name, role) VALUES (?, ?, ?, ?, 'user')");
        $stmt->bind_param("ssss", $username, $hashedPassword, $firstName, $lastName);

        if (!$stmt->execute()) {
            throw new Exception("Registration failed. Please try again.");
        }

        // Log a new registration event
        $description = $firstName . ' ' . $lastName . ' successfully registered!';
        $this->syslog->logSystemEvent('New Registration', $description, NULL);

        return true;
    }

    // Login user with brute force protection
    public function loginUser($username, $password)
    {
        // Check for too many failed attempts
        if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5) {
            if (time() - $_SESSION['last_attempt_time'] < 300) { // 5 minutes
                throw new Exception("Too many failed attempts. Please try again later.");
            } else {
                unset($_SESSION['login_attempts']);
                unset($_SESSION['last_attempt_time']);
            }
        }

        // Get user
        $stmt = $this->conn->prepare("SELECT id, username, first_name, last_name, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {
            $this->recordFailedAttempt();
            throw new Exception("Invalid username or password.");
        }

        $user = $result->fetch_assoc();

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $this->recordFailedAttempt();
            throw new Exception("Invalid username or password.");
        }

        // Check if password needs rehashing
        if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => 12])) {
            $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $this->conn->query("UPDATE users SET password = '$newHash' WHERE id = {$user['id']}");
        }

        // Set session
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();

        $description = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . ' has logged in.';
        $this->syslog->logSystemEvent('System Login', $description, $_SESSION['user_id']);
        // Reset failed attempts
        if (isset($_SESSION['login_attempts'])) {
            unset($_SESSION['login_attempts']);
            unset($_SESSION['last_attempt_time']);
        }

        return true;
    }

    private function recordFailedAttempt()
    {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 1;
            $_SESSION['last_attempt_time'] = time();
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();
        }
    }

    // Check if user is logged in with timeout
    public static function isLoggedIn()
    {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            // Check for inactivity timeout (30 minutes)
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
                self::logout();
                return false;
            }
            $_SESSION['last_activity'] = time();
            return true;
        }
        return false;
    }

    // Logout
    public static function logout()
    {
        global $conn;

        // Log the logout event
        $syslog = new SysLogFunction($conn); // Create a new instance
        $description = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . ' has logged out.';
        $syslog->logSystemEvent('System Logout', $description, $_SESSION['user_id']);

        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    // Check role
    public static function checkRole($requiredRole)
    {
        return self::isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === $requiredRole;
    }

    // Redirect if not logged in
    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            // Extract just the filename from the request URI
            $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $filename = basename($request_uri);
            
            header("Location: index.php?redirect=" . urlencode($filename));
            exit();
        }
    }

    // Redirect if not admin
    public static function requireAdmin()
    {
        self::requireLogin();
        if (!self::checkRole('admin')) {
            header("Location: restricted.php");
            exit();
        }
    }
}

// Initialize
$syslog = new SysLogFunction($conn);
$auth = new Auth($conn, $syslog);
?>