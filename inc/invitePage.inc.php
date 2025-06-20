<form action="php/sendInvite.php" method="POST">
    <!--    succes/fout melding-->
    <?php foreach (['error', 'success'] as $type): ?>
        <?php if (!empty($_SESSION[$type])): ?>
            <div class="<?= $type ?>-message">
                <?= htmlspecialchars($_SESSION[$type]); unset($_SESSION[$type]); ?>
            </div>
        <?php endif; ?>
    <?php endforeach;
    $groupId = $_GET['groupId'] ?? '';

    ?>

    <div class="invite-wrapper">
        <form action="php/sendInvite.php" method="POST" class="invite-form">
            <input type="hidden" name="groupId" value="<?= htmlspecialchars($groupId) ?>">
            <h2>Nieuwe gebruiker uitnodigen</h2>
            <input type="email" name="email" placeholder="E-mailadres" required>
<!--            <input type="hidden" name="group_id" value="5">-->
            <button type="submit">Verstuur uitnodiging</button>
        </form>
    </div>
</form>
