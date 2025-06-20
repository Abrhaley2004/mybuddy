<?php
if (!isset($_SESSION['adminId'])) {
    header('Location: index.php?page=loginRegister');
    exit;
}
?>

<main class="admin-main">
    <div class="admin-card">
        <h2>Gebruikersbeheer</h2>
        <p>Bekijk of verwijder accounts</p>
        <a class="btn" href="index.php?page=adminUsers">Ga naar gebruikers</a>
    </div>

    <div class="admin-card">
        <h2>Groepen beheren</h2>
        <p>Overzicht van alle groepen in het systeem</p>
        <a class="btn" href="index.php?page=adminGroups">Bekijk groepen</a>
    </div>

    <div class="admin-card">
        <h2>Transacties</h2>
        <p>Inzicht in betalingen tussen gebruikers</p>
        <a class="btn" href="index.php?page=adminTransactions">Transacties</a>
    </div>
</main>
</body>
</html>
