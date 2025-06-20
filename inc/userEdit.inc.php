<?php
$userId = $_GET['userId'] ?? null;

if (!$userId) {
    $_SESSION['error'] = "Geen gebruiker geselecteerd.";
    header("Location: ../index.php?page=groupsViews");
    exit();
}

$sql = 'SELECT * FROM users WHERE userId = :userId';
$stmt = $pdo->prepare($sql);
$stmt->execute(['userId' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "Gebruiker niet gevonden.";
    header("Location: ../index.php?page=adminDashboard");
    exit();
}
?>


<div class="container">
    <div class="form-box login">
        <form action="php/userEdit.php" method="POST">
    <h1 Gebruikers Bewerken >
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="error-message">
                    <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <input type="hidden" name="userId" value="<?= htmlspecialchars($user['userId']) ?>">

            <div class="input-box">
                <input type="email" placeholder="Email" id="email" name="email"  value="<?= htmlspecialchars($user['email']) ?>" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="password" placeholder="Wachtwoord" id="password" name="password"  value="<?= htmlspecialchars($user['password']) ?>" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="text" placeholder="name" id="name" name="name"  value="<?= htmlspecialchars($user['name']) ?>" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="date" placeholder="Inschijf datum" id="date" name="date"  value="<?= htmlspecialchars($user['registrationDate']) ?>" required>
                <i class='bx bxs-envelope'></i>
            </div>

        <button type="submit" class="btn">Op</button>
        </form>
    </div>



