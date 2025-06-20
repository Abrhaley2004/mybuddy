<nav class="navbar">
    <div class="logo">
        <img src="img/logo.png" alt="MyBuddy Logo" />
    </div>
    <ul class="nav-links">
        <li><a href="index.php?page=adminDashboard">Dashboard</a></li>
        <?php if (empty($_SESSION['adminId'])): ?>
            <li><a href="index.php?page=loginRegister">Aan de slag</a></li>
        <?php else: ?>
            <li><a href="PHP/logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>
