<?php
require_once 'includes/auth.php';
Auth::requireLogin();

$pageTitle = "Dashboard";
$pageDescription = "User dashboard";
require_once 'includes/header.php';

// Get user details
$stmt = $conn->prepare("SELECT first_name, last_name, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<div class="dashboard-card">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>This is your user dashboard. Here you can view and manage your account information.</p>

    <div class="user-info">
        <h3>Your Information</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
        <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>

        <div class="mt-20">
            <h4>Quick Actions</h4>
            <div class="action-buttons">
                <a href="edit-profile.php" class="btn btn-secondary">Edit Profile</a>
                <a href="change-password.php" class="btn btn-secondary">Change Password</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>