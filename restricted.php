<?php
require_once 'includes/auth.php';
$pageTitle = "Access Restricted";
$pageDescription = "You don't have permission to access this page";
require_once 'includes/header.php';
?>

<div class="dashboard-card">
    <h2>Access Restricted</h2>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i> You don't have permission to access this page.
    </div>
    <p>This page requires administrative privileges. If you believe this is an error, please contact the system
        administrator.</p>

    <div class="mt-20">
        <a href="dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Return to Dashboard</a>
        <?php if (!Auth::isLoggedIn()): ?>
            <a href="index.php" class="btn"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>