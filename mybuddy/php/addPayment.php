<?php
session_start();
include '../../Private/ConnectionMyBuddy/connection.php';

try {
    $groupId = (int)$_POST['groupId'];
    $totalAmount = (float)$_POST['amount'];
    $paidByUserId = (int)$_POST['paidByUserId'];
    $paymentDate = $_POST['paymentDate'];
    $description = $_POST['description'] ?? '';
    $distributionMethod = $_POST['distributionMethod'] ?? '';
    $currencyId = (int)$_POST['currencyId'];
    $userShares = array_map('floatval', $_POST['userShares'] ?? []);
    $image = base64_encode(file_get_contents($_FILES['foto']['tmp_name']));

    if (!is_array($userShares) || empty($userShares)) {
        throw new Exception("Geen geldige verdeling ontvangen.");
    }

    $totalShares = array_sum($userShares);

    if ($distributionMethod === 'Exact bedrag' && abs($totalShares - $totalAmount) > 0.01) {
        throw new Exception("Het totaal van de verdeling ($totalShares) komt niet overeen met het opgegeven bedrag ($totalAmount).");
    }

    $currencyCheck = $pdo->prepare("SELECT currencyId FROM currencies WHERE currencyId = :currencyId");
    $currencyCheck->execute([':currencyId' => $currencyId]);
    if (!$currencyCheck->fetch()) {
        throw new Exception("Ongeldige valuta geselecteerd.");
    }

    $pdo->beginTransaction();

    $insertPaymentSql = "
        INSERT INTO payment (groupsId, amount, description, image, `date`, currencyId)
        VALUES (:groupsId, :amount, :description, :image, :date, :currencyId)
    ";
    $insertPaymentStmt = $pdo->prepare($insertPaymentSql);
    $insertPaymentStmt->execute([
        ':groupsId' => $groupId,
        ':amount' => $totalAmount,
        ':description' => $description,
        ':image' => $image,
        ':date' => $paymentDate,
        ':currencyId' => $currencyId
    ]);
    $paymentId = $pdo->lastInsertId();

    $insertDebtSql = "
        INSERT INTO debts (paymentId, groupMemberId, amount, status)
        VALUES (:paymentId, :groupMemberId, :amount, :status)
    ";
    $insertDebtStmt = $pdo->prepare($insertDebtSql);

    foreach ($userShares as $userId => $userAmount) {
        $userId = (int)$userId;
        $userAmount = (float)$userAmount;
        $status = ($userId === $paidByUserId) ? 1 : 0;
        $insertDebtStmt->execute([
            ':paymentId' => $paymentId,
            ':groupMemberId' => $userId,
            ':amount' => $userAmount,
            ':status' => $status
        ]);
    }

    $selectGroupSql = "SELECT totalAmount FROM groups WHERE groupsId = :groupId";
    $selectGroupStmt = $pdo->prepare($selectGroupSql);
    $selectGroupStmt->execute([':groupId' => $groupId]);

    $groupData = $selectGroupStmt->fetch(PDO::FETCH_ASSOC);
    if (!$groupData) {
        throw new Exception("Groep niet gevonden.");
    }

    $newTotalAmount = (float)$groupData['totalAmount'] + $totalAmount;
    $paidUserShare = $userShares[$paidByUserId] ?? 0;


    $updateGroupSql = "
        UPDATE groups 
        SET totalAmount = :totalAmount
        WHERE groupsId = :groupId
    ";
    $updateGroupStmt = $pdo->prepare($updateGroupSql);
    $updateGroupStmt->execute([
        ':totalAmount' => $newTotalAmount,
        ':groupId' => $groupId
    ]);

    $pdo->commit();

    header("Location: ../index.php?page=paymentSuccess&groupId=" . urlencode($groupId));
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Fout bij het verwerken van de betaling: " . htmlspecialchars($e->getMessage());
    exit();
}
?>
