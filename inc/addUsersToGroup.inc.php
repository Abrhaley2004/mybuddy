<?php
$groupId = (int)($_GET['groupId'] ?? 0);
$userId = (int)($_SESSION['userId'] ?? 0);

try {
    $sqlCurrentMembers = "SELECT userId FROM group_members WHERE groupId = :groupId AND isDeleted = 0";
    $stmtCurrent = $pdo->prepare($sqlCurrentMembers);
    $stmtCurrent->bindValue(':groupId', $groupId, PDO::PARAM_INT);
    $stmtCurrent->execute();
    $currentMembers = $stmtCurrent->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($currentMembers)) {
        $placeholders = implode(',', array_fill(0, count($currentMembers), '?'));
        $sqlUsers = "SELECT userId, name FROM users WHERE userId NOT IN ($placeholders) ORDER BY name";
        $stmtUsers = $pdo->prepare($sqlUsers);
        $stmtUsers->execute($currentMembers);
    } else {
        $sqlUsers = "SELECT userId, name FROM users ORDER BY name";
        $stmtUsers = $pdo->query($sqlUsers);
    }

    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Fout bij ophalen gebruikers: " . htmlspecialchars($e->getMessage());
}
?>


<div class="group-add-style">

    <?php foreach (['error', 'success'] as $type): ?>
        <?php if (!empty($_SESSION[$type])): ?>
            <div class="<?= $type ?>-message">
                <?= htmlspecialchars($_SESSION[$type]); unset($_SESSION[$type]); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>


    <h2>Gebruiker toevoegen</h2>

    <form enctype="multipart/form-data" method="post" action="php/addUsersToGroup.php">

        <div class="form-group-option">
            <label for="users">Selecteer gebruikers</label>
            <select id="users" name="users[]" multiple size="5">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= htmlspecialchars($user['userId']) ?>">
                            <?= htmlspecialchars($user['name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">Geen gebruikers beschikbaar</option>
                <?php endif; ?>
            </select>
        </div>

        <input type="hidden" name="groupId" value="<?= htmlspecialchars($groupId) ?>">

        <button type="submit" class="add-group-button">Gebruiker Toevoegen</button>

    </form>

</div>
