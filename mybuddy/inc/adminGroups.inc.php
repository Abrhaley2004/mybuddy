<?php

$userId = $_SESSION['userId'];


$Sql = "
SELECT DISTINCT g.groupsId, g.name, g.createdByUserId, g.createdAt, g.image
FROM groups g
LEFT JOIN group_members gm ON g.groupsId = gm.groupId
ORDER BY g.createdAt DESC
";
$stmt = $pdo->prepare($Sql);
$stmt->execute();
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<h1>Groepenlijst</h1>
<main class="main-section">
    <section class="group-list">
        <?php if (count($groups) > 0): ?>
            <?php foreach ($groups as $group): ?>
                <a href="index.php?page=groupchat&groupId=<?= htmlspecialchars($group['groupsId']) ?>" class="group-link">
                    <div class="group-card">
                        <img class="group-photo"
                             src="<?= htmlspecialchars($group['image'] ? 'uploads/' . $group['image'] : 'img/mybuddy.png') ?>"
                             onerror="this.onerror=null;this.src='img/default.png';"/>


                        <div class="group-info">
                            <span class="group-name"><?= htmlspecialchars($group['name']) ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Er zijn nog geen groepen aangemaakt.</p>
        <?php endif; ?>
    </section>
</main>
