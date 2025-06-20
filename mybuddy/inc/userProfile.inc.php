<?php
$userId = $_SESSION['userId'] ?? null;

if (!$userId) {
    $_SESSION['error'] = "Geen gebruiker ingelogd.";
    header("Location: ../index.php?page=loginRegister");
    exit();
}

$sql = 'SELECT * FROM users WHERE userId = :userId';
$stmt = $pdo->prepare($sql);
$stmt->execute(['userId' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<div class="registration-container">
    <h1>Mijn Profiel</h1>
    <?php foreach (['error', 'success'] as $type): ?>
        <?php if (!empty($_SESSION[$type])): ?>
            <div class="<?= $type ?>-message">
                <?= htmlspecialchars($_SESSION[$type]); unset($_SESSION[$type]); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if (!empty($user['profile'])): ?>

    <?php endif; ?>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Naam:</strong> <?= htmlspecialchars($user['name']) ?></p>
    <p><strong>Password:</strong> ****************</p>
    <p><strong>Account aangemaakt op:</strong> <?= htmlspecialchars($user['registrationDate']) ?></p>

    <h2>Wachtwoord aanpassen</h2>
    <form action="php/updatePassword.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="password">Nieuw wachtwoord</label>
            <input type="password" name="password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Bevestig wachtwoord</label>
            <input type="password" name="confirm_password">
        </div>
        <hr>
        <button styletype="submit">Opslaan</button>
    </form>
</div>

