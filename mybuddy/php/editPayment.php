<?php

session_start();
include '../../Private/ConnectionMyBuddy/connection.php';


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


?>