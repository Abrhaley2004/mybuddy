<?php
if (isset($_GET['transactionId'])) {
    $transactionId = $_GET['transactionId'];

    // gebruik $transactionId nu in je SQL
} else {
    echo "Geen transactie gekozen.";
    exit;
}


if ($transactionId) {

    $sql = "SELECT * FROM transactions WHERE transactionId = :transactionId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['transactionId' => $transactionId]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Check of er deelnemers zijn (iemand betaald?)
    $sql2 = "SELECT COUNT(*) FROM transactions_participants WHERE transactionId = :transactionId";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute(['transactionId' => $transactionId]);
    $hasPayments = $stmt2->fetchColumn() > 0;
}
if (!$transaction) {
    echo "<p style='color: red;'>⚠️ Deze betaling bestaat niet of is niet gevonden.</p>";
    exit;
}

?>


<div class="payment-card">
    <h2><?= htmlspecialchars($transaction['description']) ?></h2>
    <p><strong>Bedrag:</strong> €<?= number_format($transaction['amount'], 2, ',', '.') ?></p>
    <p><strong>Datum:</strong> <?= htmlspecialchars($transaction['date']) ?></p>

    <?php if (!$hasPayments): ?>
        <div class="payment-actions">
            <a class="btn btn-edit" href="editTransaction.php?transactionId=<?= $transactionId ?>">✏️ Aanpassen</a>
            <a class="btn btn-delete" href="deleteTransaction.php?transactionId=<?= $transactionId ?>">🗑️ Verwijderen</a>
        </div>
    <?php else: ?>
        <div class="payment-locked">
            <p>🔒 Deze betaling is al gestart. Je kunt hem niet meer aanpassen.</p>
        </div>
    <?php endif; ?>
</div>


<?php if (!$hasPayments): ?>
    <a href="editTransaction.php?transactionId=<?= $transactionId ?>">Aanpassen</a>
    <a href="deleteTransaction.php?transactionId=<?= $transactionId ?>">Verwijderen</a>
<?php else: ?>
    <p>Deze betaling is al gestart. Je kunt hem niet meer aanpassen.</p>
<?php endif; ?>
