<?php

include '../../Private/ConnectionMyBuddy/connection.php';




$groupId = isset($_GET['groupId']) ? (int)$_GET['groupId'] : null;
$userId = isset($_GET['userId']) ? (int)$_GET['userId'] : null;

if ($groupId && $userId) {
    try {
        $sql = "DELETE FROM `group_admins` WHERE groupsId = :groupId AND adminsId = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success'] = 'Groepsbeheerder succesvol verwijderd.';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Fout bij het verwijderen van groepsbeheerder.';
    }
}

header('Location: ../index.php?page=groupchat&groupId=' . $groupId);
exit();
?>