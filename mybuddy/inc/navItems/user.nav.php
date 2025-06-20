<nav class="navbar">
    <div class="logo">
        <img src="images/logo.png" alt="MyBuddy Logo" />
    </div>
    <ul class="nav-links">
        <li><a href="index.php?page=groupsViews">Groepen</a></li>
        <li><a href="index.php?page=userProfile">Profiel</a></li>
        <?php if (!empty($_SESSION['userId'])): ?>
            <li><a href="PHP/logout.php">Uitloggen</a></li>
        <?php else: ?>
            <li><a href="index.php?page=loginRegister">Aan de slag</a></li>
        <?php endif; ?>
    </ul>
</nav>
