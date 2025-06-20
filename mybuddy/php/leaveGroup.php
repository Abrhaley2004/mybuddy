<?php
session_start();
include '../../Private/ConnectionMyBuddy/connection.php';

if (!isset($_SESSION['userId'])) {
    header('Location: ../index.php?page=loginRegister');
    exit;
}

$userId = (int)$_SESSION['userId'];
$groupId = isset($_GET['groupId']) ? (int)$_GET['groupId'] : 0;


echo $groupId;

if ($groupId <= 0) {
    $_SESSION['error'] = "Ongeldige groep.";
    header('Location: ../index.php?page=groupchat&groupId=' . $groupId);
    exit;
}

try {
    $sqlGetMember = "SELECT groupMemberId FROM group_members WHERE groupId = :groupId AND userId = :userId AND isDeleted = 0";
    $stmtGet = $pdo->prepare($sqlGetMember);
    $stmtGet->bindParam(':groupId', $groupId, PDO::PARAM_INT);
    $stmtGet->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmtGet->execute();
    $member = $stmtGet->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
        $_SESSION['error'] = "Je bent geen actief lid van deze groep.";
        header('Location: ../index.php?page=groupsViews');
        exit;
    }

    $groupMemberId = (int)$member['groupMemberId'];


    $sqlCheckDebt = "SELECT debtId FROM debts WHERE groupMemberId = :groupMemberId AND status = 0";
    $stmtCheckDebt = $pdo->prepare($sqlCheckDebt);
    $stmtCheckDebt->bindParam(':groupMemberId', $groupMemberId, PDO::PARAM_INT);
    $stmtCheckDebt->execute();

    if ($stmtCheckDebt->rowCount() > 0) {
        $_SESSION['error'] = "Je moet eerst je schulden aflossen.";
        header('Location: ../index.php?page=groupchat&groupId=' . $groupId);
        exit;
    }

    $sqlSoftDelete = "UPDATE group_members SET isDeleted = 1 WHERE groupId = :groupId AND userId = :userId";
    $stmtSoftDelete = $pdo->prepare($sqlSoftDelete);
    $stmtSoftDelete->bindParam(':groupId', $groupId, PDO::PARAM_INT);
    $stmtSoftDelete->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmtSoftDelete->execute();

    $_SESSION['success'] = "Je bent succesvol uit de groep gestapt.";
    header('Location: ../index.php?page=groupsViews');
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Fout bij het verlaten van de groep.";
    header('Location: ../index.php?page=groupchat&groupId=' . $groupId . $e->getMessage());
    exit;
}
