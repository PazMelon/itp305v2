</main>
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About</h3>
                <p>Secure authentication system with role-based access control.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <?php if (Auth::isLoggedIn()): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="index.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?php echo date('Y'); ?> Auth System. All rights reserved.</p>
        </div>
    </div>
</footer>
<script>
    // Mobile menu toggle
    document.querySelector('.mobile-menu-toggle').addEventListener('click', function () {
        document.querySelector('.nav-links').classList.toggle('active');
    });
</script>
</body>

</html>