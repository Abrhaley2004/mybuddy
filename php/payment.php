<?php
session_start();
include '../../Private/ConnectionMyBuddy/connection.php';

$groupMemberId = (int)($_POST['groupMemberId'] ?? 0);
$paymentId = (int)($_POST['paymentId'] ?? 0);
$groupId = (int)($_POST['groupId'] ?? 0);
$amount = $_POST['amount'] ?? '';
$reason = $_POST['reason'] ?? '';
$action = $_POST['action'] ?? '';

if ($groupMemberId === 0) {
    die("Ongeldige groep lid ID.");
}

$sqlDebts = "
    SELECT d.paymentId, d.groupMemberId, d.amount, d.status, u.name AS debtorName
    FROM debts d
    INNER JOIN group_members gm ON d.groupMemberId = gm.groupMemberId
    INNER JOIN users u ON gm.userId = u.userId
    WHERE d.groupMemberId = :groupMemberId AND d.status = 0
    LIMIT 1
";
$stmtDebts = $pdo->prepare($sqlDebts);
$stmtDebts->bindValue(':groupMemberId', $groupMemberId, PDO::PARAM_INT);
$stmtDebts->execute();
$debt = $stmtDebts->fetch(PDO::FETCH_ASSOC);

if (!$debt) {
    die("Geen openstaande schulden gevonden voor dit groepslid.");
}

$paymentId = $debt['paymentId'];

$sql = "UPDATE debts SET status = 1 WHERE paymentId = :paymentId AND groupMemberId = :groupMemberId";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
$stmt->bindParam(':groupMemberId', $groupMemberId, PDO::PARAM_INT);
$isExecute = $stmt->execute();


if ($action === 'pay' || $action === 'markPaid') {
    try {
        header("Location: ../index.php?page=groupchat&groupId=" . urlencode($groupId));
        exit();
    } catch (Exception $e) {
        echo "Fout bij verwerken van betaling: " . htmlspecialchars($e->getMessage());
        exit();
    }
}
