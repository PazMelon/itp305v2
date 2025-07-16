<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$pageTitle = "Register";
$pageDescription = "Create a new account";
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);

        if ($auth->registerUser($username, $password, $firstName, $lastName)) {
            $success = "Registration successful! You can now <a href='index.php'>login</a>.";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<div class="auth-form">
    <h2>Register</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php else: ?>
        <form id="registerForm" action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required minlength="4" maxlength="20">
                <small class="form-text">4-20 characters, letters, numbers, and underscores only</small>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8">
                <div class="password-strength">
                    <div class="password-strength-bar"></div>
                </div>
                <small class="form-text">Minimum 8 characters</small>
            </div>

            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-block">Register</button>
            </div>
        </form>

        <div class="text-center mt-20">
            <p>Already have an account? <a href="index.php">Login here</a></p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>