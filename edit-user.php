<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
Auth::requireAdmin();

$pageTitle = "Edit User";
$pageDescription = "Edit user details";
require_once 'includes/header.php';

// Get user ID from URL
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch user data
$stmt = $conn->prepare("SELECT id, username, first_name, last_name, role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if user exists
if (!$user) {
    header("Location: admin.php");
    exit();
}

// Get any errors from session
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<div class="dashboard-card">
    <h2>Edit User: <?php echo htmlspecialchars($user['username']); ?></h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="post" action="includes/edit-user-process.php" class="user-form">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
        </div>
        
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required 
                   value="<?php echo htmlspecialchars($user['first_name']); ?>">
        </div>
        
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required 
                   value="<?php echo htmlspecialchars($user['last_name']); ?>">
        </div>
        
        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
            </select>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="admin.php" class="btn">Cancel</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>