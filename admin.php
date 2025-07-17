<?php
require_once 'includes/auth.php';
Auth::requireAdmin();

$pageTitle = "Admin Panel";
$pageDescription = "Administrator dashboard";
require_once 'includes/header.php';

// Get all users
$users = $conn->query("SELECT id, username, first_name, last_name, role, created_at FROM users ORDER BY created_at DESC");
?>

<div class="dashboard-card">
    <h2>Admin Panel</h2>
    <p>Welcome to the admin panel. This area is restricted to administrators only <?php echo $_SESSION['user_id'] ?>.</p>

    <div class="admin-info">
        <h3>User Management</h3>

        <div class="table-responsive">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-small">Edit</a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="includes/delete-user.php?id=<?php echo $user['id']; ?>"
                                       class="btn btn-small btn-danger"
                                       data-user-id="<?php echo $user['id']; ?>"
                                       data-user-name="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>">
                                        Delete
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-actions mt-20">
            <a href="add-user.php" class="btn">Add New User</a>
            <a href="system-settings.php" class="btn">System Settings</a>
        </div>
    </div>
</div>

<!-- Include SweetAlert and our delete confirmation script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/assets/js/delete-confim.js"></script>

<?php require_once 'includes/footer.php'; ?>