<?php
$sql = "
    SELECT 
        p.paymentId,
        p.amount,
        p.description,
        p.date,
        u.name AS userName,
        g.name AS groupName
    FROM payment p
    JOIN group_members gm ON p.groupMemberId = gm.groupMemberId
    JOIN users u ON gm.userId = u.userId
    JOIN groups g ON gm.groupId = g.groupsId
    ORDER BY p.date DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Alle betalingen (Admin overzicht)</h1>

<table>
    <thead>
    <tr>
        <th>Gebruiker</th>
        <th>Groep</th>
        <th>Omschrijving</th>
        <th>Bedrag</th>
        <th>Datum</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($payments as $payment): ?>
        <tr>
            <td><?= htmlspecialchars($payment['userName']) ?></td>
            <td><?= htmlspecialchars($payment['groupName']) ?></td>
            <td><?= htmlspecialchars($payment['description']) ?></td>
            <td>€<?= number_format($payment['amount'], 2, ',', '.') ?></td>
            <td><?= htmlspecialchars($payment['date']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
