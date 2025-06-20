<?php
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

$where = "WHERE userId NOT IN (SELECT userId FROM admin)";
if ($statusFilter === 'active') {
    $where .= " AND status = 0";
} elseif ($statusFilter === 'inactive') {
    $where .= " AND status = 1";
}

$allowedSort = ['userId', 'name', 'email', 'registrationDate', 'status'];
$sort = in_array($_GET['sort'] ?? '', $allowedSort) ? $_GET['sort'] : 'userId';
$order = ($_GET['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

$sql = "SELECT * FROM users $where ORDER BY $sort $order";

$statement = $pdo->prepare($sql);
$statement->execute();

?>


    <h1>Klantenlijst</h1>
<?php foreach (['error', 'success'] as $type): ?>
    <?php if (!empty($_SESSION[$type])): ?>
        <div class="<?= $type ?>-message">
            <?= htmlspecialchars($_SESSION[$type]); unset($_SESSION[$type]); ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

    <div class="filter-buttons" style="margin-bottom: 1rem;">
        <a href="index.php?page=adminUsers&status=all" class="btn">Alle accounts</a>
        <a href="index.php?page=adminUsers&status=active" class="btn">Actieve accounts</a>
        <a href="index.php?page=adminUsers&status=inactive" class="btn">Inactieve accounts</a>
    </div>

    <table>
        <tr>
            <th><a href="?page=adminUsers&status=<?= $statusFilter ?>&sort=email&order=<?= $sort === 'email' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Email</a></th>
            <th><a href="?page=adminUsers&status=<?= $statusFilter ?>&sort=name&order=<?= $sort === 'name' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Naam</a></th>
            <th><a href="?page=adminUsers&status=<?= $statusFilter ?>&sort=registrationDate&order=<?= $sort === 'registrationDate' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Inschrijfdatum</a></th>
            <th><a href="?page=adminUsers&status=<?= $statusFilter ?>&sort=status&order=<?= $sort === 'status' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">Status</a></th>
            <th>Aanpassen</th>
            <th>Verwijderen</th>
            <th>Deactiveren</th>
        </tr>


        <?php while ($row = $statement->fetch(PDO::FETCH_ASSOC)) { ?>
            <tr>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['registrationDate']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><a href="index.php?page=userEdit&userId=<?= $row['userId'] ?>">Aanpassen</a></td>
                    <td><a href="php/userDelete.php?userId=<?= $row['userId'] ?>">Verwijderen</a></td>
                    <td><a href="php/userDeactivate.php?userId=<?= $row['userId'] ?>">Deactiveren</a></td>
            </tr>
        <?php } ?>
    </table>
<button></button>
<?php
