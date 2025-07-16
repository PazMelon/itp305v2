<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'auth_system');

// Create connection with error handling
try {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Set charset
    $conn->set_charset("utf8mb4");

    // Start session with secure settings
    session_start([
        'cookie_lifetime' => 86400, // 1 day
        'cookie_secure' => true,   // Requires HTTPS
        'cookie_httponly' => true,   // Prevents JS access
        'use_strict_mode' => true    // Prevents session fixation
    ]);

    // Regenerate session ID to prevent fixation
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id();
        $_SESSION['initiated'] = true;
    }
} catch (Exception $e) {
    die("System error: " . $e->getMessage());
}
?>