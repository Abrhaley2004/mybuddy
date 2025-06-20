<?php

if (empty($_SESSION['userId'])) {
    header('Location: index.php?page=loginRegister');
    exit();
}

$userId = (int)$_SESSION['userId'];

$Sql = "
SELECT DISTINCT g.groupsId, g.name, g.createdByUserId, g.createdAt, g.image
FROM groups g
LEFT JOIN group_members gm ON g.groupsId = gm.groupId
WHERE g.isDeleted = 0
AND (
    g.createdByUserId = :userId
    OR (gm.userId = :userId AND gm.isDeleted = 0)
)
ORDER BY g.createdAt DESC
";
$stmt = $pdo->prepare($Sql);
$stmt->execute(['userId' => $userId]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="main-section">
    <section class="group-list">
        <?php foreach ($groups as $group): ?>
            <div class="group-card">
                <a href="index.php?page=groupchat&groupId=<?= htmlspecialchars($group['groupsId'], ENT_QUOTES, 'UTF-8') ?>"
                   class="group-link">
                    <img class="group-photo"
                         src="<?= htmlspecialchars($group['image'] ? 'uploads/' . $group['image'] : 'img/mybuddy.png') ?>"
                         onerror="this.onerror=null;this.src='img/default.png';"/>

                    <div class="group-info">
                        <span class="group-name"><?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </a>

                <?php if ((int)$group['createdByUserId'] === $userId): ?>
                    <div class="three-dots" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false">
                        <span>⋮</span>
                        <div class="dropdown hidden" role="menu">
                            <button class="user-btn edit"
                                    onclick="location.href='index.php?page=editGroup&groupId=<?= htmlspecialchars($group['groupsId'], ENT_QUOTES, 'UTF-8') ?>'">
                                Aanpassen
                            </button>
                            <form method="POST" action="php/deleteGroups.php"
                                  onsubmit="return confirm('Weet je zeker dat je deze groep wilt verwijderen?');">
                                <input type="hidden" name="groupId" value="<?= (int)$group['groupsId'] ?>">
                                <button type="submit" class="user-btn delete">Verwijderen</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>

    <aside class="group-create">
        <button class="group-create-btn" onclick="location.href='index.php?page=addGroup'">
            Groep aanmaken
        </button>
    </aside>
</main>

<script>
    document.querySelectorAll('.three-dots').forEach(dot => {
        const dropdown = dot.querySelector('.dropdown');

        dot.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown').forEach(menu => menu.classList.add('hidden'));
    });
</script>