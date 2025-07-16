<?php
require_once 'auth.php';
require_once 'config.php';
require_once __DIR__ . '/../functions/sys_log_func.php';

Auth::requireAdmin();

// Get user ID from URL or POST
$userId = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

// Fetch user data
$stmt = $conn->prepare("SELECT id, username, first_name, last_name, role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if user exists
if (!$user) {
    $_SESSION['error'] = "User not found";
    header("Location: admin.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $role = $_POST['role'];
    
    // Basic validation
    $errors = [];
    if (empty($firstName)) $errors[] = "First name is required";
    if (empty($lastName)) $errors[] = "Last name is required";
    if (!in_array($role, ['admin', 'editor', 'user'])) $errors[] = "Invalid role specified";
    
    if (empty($errors)) {
        // Build change description
        $changes = [];
        
        // Check for first name change
        if ($user['first_name'] !== $firstName) {
            $changes[] = "First name: " . $user['first_name'] . " → " . $firstName;
        }
        
        // Check for last name change
        if ($user['last_name'] !== $lastName) {
            $changes[] = "Last name: " . $user['last_name'] . " → " . $lastName;
        }
        
        // Check for role change
        if ($user['role'] !== $role) {
            $changes[] = "Role: " . $user['role'] . " → " . $role;
        }
        
        // Update user in database
        $updateStmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, role = ? WHERE id = ?");
        $updateStmt->bind_param("sssi", $firstName, $lastName, $role, $userId);
        
        if ($updateStmt->execute()) {
            $_SESSION['message'] = "User updated successfully";

            // Create detailed system log if there were changes
            if (!empty($changes)) {
                $description = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . 
                             ' updated User ID: ' . $userId . '. Changes: ' . implode(', ', $changes);
                $syslog->logSystemEvent('User Information Updated', $description, $_SESSION['user_id']);
            } else {
                $description = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . 
                             ' viewed but made no changes to User ID: ' . $userId;
                $syslog->logSystemEvent('User Information Viewed', $description, $_SESSION['user_id']);
            }

            header("Location: /../admin.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to update user: " . $conn->error;

            $description = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . 
                         ' attempted to update User ID: ' . $userId . ' but failed.';
            $syslog->logSystemEvent('User Update Failed', $description, $_SESSION['user_id']);

            header("Location: edit-user.php?id=" . $userId);
            exit();
        }
    } else {
        $_SESSION['errors'] = $errors;
        
        $description = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . 
                     ' attempted to update User ID: ' . $userId . ' but validation failed.';
        $syslog->logSystemEvent('User Update Validation Failed', $description, $_SESSION['user_id']);
        
        header("Location: edit-user.php?id=" . $userId);
        exit();
    }
} else {
    // Log view action for GET requests
    $description = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . 
                 ' viewed User ID: ' . $userId . ' for editing.';
    $syslog->logSystemEvent('User Edit Viewed', $description, $_SESSION['user_id']);
    
    // Not a POST request, redirect to edit page
    header("Location: edit-user.php?id=" . $userId);
    exit();
}
?>