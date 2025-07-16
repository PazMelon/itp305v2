<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="<?php echo isset($pageDescription) ? $pageDescription : 'Authentication system with role-based access control'; ?>">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Auth System'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/auth.js" defer></script>
</head>

<body>
    <header>
        <nav>
            <div class="container">
                <div class="logo">
                    <i class="fas fa-lock"></i>
                    <span>Auth System</span>
                </div>
                <button class="mobile-menu-toggle" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="nav-links">
                    <?php if (Auth::isLoggedIn()): ?>
                        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <?php if (Auth::checkRole('admin')): ?>
                            <a href="admin.php"><i class="fas fa-cog"></i> Admin Panel</a>
                        <?php endif; ?>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="index.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
    <main class="container"></main>