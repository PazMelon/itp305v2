<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$pageTitle = "Login";
$pageDescription = "Login to your account";
$error = '';

// Check for redirect - now just the filename
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard.php';

// Validate the redirect target
$allowed_pages = ['dashboard.php', 'profile.php', 'admin.php']; // Add other allowed pages

if (!in_array($redirect, $allowed_pages)) {
    $redirect = 'dashboard.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $redirect = trim($_POST['redirect']);

        if ($auth->loginUser($username, $password)) {

            // Final check for admin page access
            if ($redirect === 'admin.php') {
                if (Auth::checkRole('admin')) {
                    header("Location: admin.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            }
            
            header("Location: " . $redirect);
            exit();
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (Auth::checkRole('admin')) {
        header("Location: admin.php");
    }else{
        header("Location: dashboard.php");
    }
    exit();
}

require_once 'includes/header.php';
?>

<div class="auth-form">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form id="loginForm" action="index.php" method="POST">
    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars(basename($redirect)); ?>">

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <div class="password-strength">
                <div class="password-strength-bar"></div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-block">Login</button>
        </div>
    </form>

    <div class="text-center mt-20">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
        <p><a href="forgot-password.php">Forgot your password?</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>