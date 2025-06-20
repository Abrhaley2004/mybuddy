<?php
session_start();
include '../../Private/ConnectionMyBuddy/connection.php';

if (!isset($_POST['users']) || !isset($_POST['groupId'])) {
    $_SESSION['error'] = "Geen gebruikers of groep geselecteerd.";
    header('Location: ../index.php?page=addUsersToGroup');
    exit;
}

$groupId = (int)$_POST['groupId'];
$userIds = $_POST['users'];

try {
    foreach ($userIds as $userId) {
        $sqlCheckActive = "SELECT 1 FROM group_members WHERE groupId = :groupId AND userId = :userId AND isDeleted = 0";
        $stmtCheckActive = $pdo->prepare($sqlCheckActive);
        $stmtCheckActive->bindParam(":groupId", $groupId, PDO::PARAM_INT);
        $stmtCheckActive->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmtCheckActive->execute();

        if ($stmtCheckActive->fetch()) {
            continue; // Gebruiker is al actief lid
        }

        $sqlCheckDeleted = "SELECT 1 FROM group_members WHERE groupId = :groupId AND userId = :userId AND isDeleted = 1";
        $stmtCheckDeleted = $pdo->prepare($sqlCheckDeleted);
        $stmtCheckDeleted->bindParam(":groupId", $groupId, PDO::PARAM_INT);
        $stmtCheckDeleted->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmtCheckDeleted->execute();

        if ($stmtCheckDeleted->fetch()) {
            $sqlUpdate = "UPDATE group_members SET isDeleted = 0 WHERE groupId = :groupId AND userId = :userId";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(":groupId", $groupId, PDO::PARAM_INT);
            $stmtUpdate->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmtUpdate->execute();
        } else {
            $sqlInsert = "INSERT INTO group_members (groupId, userId, isDeleted) VALUES (:groupId, :userId, 0)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->bindParam(":groupId", $groupId, PDO::PARAM_INT);
            $stmtInsert->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmtInsert->execute();

            // het kan ook zo
            // $stmtUpdate->bindParam(":userId", $userId, PDO::PARAM_INT);
        }
    }

    $_SESSION['success'] = "Gebruikers succesvol toegevoegd aan de groep.";
    header('Location: ../index.php?page=groupchat&groupId=' . $groupId);
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Fout bij toevoegen gebruikers: " . htmlspecialchars($e->getMessage());
    header('Location: ../index.php?page=addUsersToGroup&groupId=' . $groupId);
    exit;
}
?>
