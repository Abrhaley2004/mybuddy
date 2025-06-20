<?php

if (empty($_SESSION['userId'])) {
    header('Location: index.php?page=loginRegister');
    exit();
}

$groupId = $_GET['groupId'] ?? null;
$groupMemberId = $_GET['groupMemberId'] ?? null;

$debts = [];

if ($groupMemberId && $groupId) {
    $sqlDebts = "
            SELECT d.paymentId, d.groupMemberId, d.amount, d.status, u.name AS debtorName
            FROM debts d
            INNER JOIN group_members gm ON d.groupMemberId = gm.groupMemberId
            INNER JOIN users u ON gm.userId = u.userId
            WHERE d.groupMemberId = :groupMemberId AND d.status = 0
        ";
    $stmtDebts = $pdo->prepare($sqlDebts);
    $stmtDebts->bindParam(':groupMemberId', $groupMemberId, PDO::PARAM_INT);
    $stmtDebts->execute();
    $debts = $stmtDebts->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($debts)) {
        $debtname = $debts[0]['debtorName'] ?? 'Onbekend';
        $paymentId = $debts[0]['paymentId'] ?? '';
    }

    $sqlPayment = "
       SELECT  p.paymentId, p.amount, p.description, p.image, p.date, g.createdByUserId, g.groupsId, u.name AS payerName
        FROM payment p
        INNER JOIN groups g ON p.groupsId = g.groupsId
       INNER JOIN users u ON g.createdByUserId = u.userId
        WHERE p.paymentId = :paymentId;
    ";
    $stmt = $pdo->prepare($sqlPayment);
    $stmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
    $stmt->execute();
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!empty($member)){
        $paymentname = $member['payerName'] ?? 'Onbekend';
    }
}
?>

<div class="main-wrapper">
    <div class="participants">
        <h1><?= htmlspecialchars($debtname) ?></h1>
        <p>→</p>
        <h1><?= htmlspecialchars($paymentname) ?></h1>
    </div>

    <div class="payment-form">
        <form action="php/payment.php" method="post">
            <input type="hidden" name="groupId" value="<?= htmlspecialchars($groupId) ?>">
            <input type="hidden" name="groupMemberId" value="<?= htmlspecialchars($groupMemberId) ?>">
            <input type="hidden" name="paymentId" value="<?= htmlspecialchars($member['paymentId'] ?? '') ?>">

            <label for="amount">Bedrag (€):</label>
            <input type="text" id="amount" name="amount"
                   value="<?= !empty($debts) ? number_format($debts[0]['amount'], 2, '.', '') : '' ?>">


<!--            <label for="reason">Waarvoor is het?</label>-->
<!--            <input type="text" id="reason" name="reason" required>-->

            <div class="button-group">
                <div class="button-group">
                    <button type="submit" name="action" value="pay">Betalen</button>

<!--                    <button type="submit" name="action" value="markPaid">Merk als betaald</button>-->
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelector('button[value="markPaid"]')?.addEventListener('click', function () {
        alert("Deze betaling wordt gemarkeerd als betaald.");
    });
</script>