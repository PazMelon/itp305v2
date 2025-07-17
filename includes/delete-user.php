<?php
require_once 'auth.php';
require_once 'config.php';
require_once __DIR__ . '/../functions/sys_log_func.php';

Auth::requireAdmin();

// Get user ID from URL
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if trying to delete self
if ($userId == $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account";
    header("Location: /../admin.php");
    exit();
}

// Fetch user data for logging
$stmt = $conn->prepare("SELECT username, first_name, last_name FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['error'] = "User not found";
    header("Location: /../admin.php");
    exit();
}

// Perform deletion
$deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$deleteStmt->bind_param("i", $userId);

if ($deleteStmt->execute()) {
    $_SESSION['message'] = "User deleted successfully";
    
    // Log the deletion
    $description = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . 
                 ' deleted user: ' . $user['first_name'] . ' ' . $user['last_name'] . 
                 ' (ID: ' . $userId . ', Username: ' . $user['username'] . ')';
    $syslog->logSystemEvent('User Deleted', $description, $_SESSION['user_id']);
} else {
    $_SESSION['error'] = "Failed to delete user: " . $conn->error;
    
    // Log the failed attempt
    $description = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . 
                 ' attempted to delete user: ' . $user['first_name'] . ' ' . $user['last_name'] . 
                 ' (ID: ' . $userId . ') but failed';
    $syslog->logSystemEvent('User Deletion Failed', $description, $_SESSION['user_id']);
}

header("Location: /../admin.php");
exit();
?>