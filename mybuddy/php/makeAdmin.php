<?php
session_start();
include __DIR__ . '/../../Private/ConnectionMyBuddy/connection.php';


// Check if user is logged in
if (empty($_SESSION['userId'])) {
    header('Location: index.php?page=loginRegister');
    exit();
}

$groupId = isset($_GET['groupId']) ? (int)$_GET['groupId'] : null;
$makeAdminUserId = isset($_GET['userId']) ? (int)$_GET['userId'] : null;

// Validate required parameters
if (!$groupId || !$makeAdminUserId) {
    $_SESSION['error'] = 'Ongeldige parameters.';
    header('Location: index.php?page=groupDetail&groupId=' . $groupId);
    exit();
}

try {
    // Add user as admin to group_admins table
    $sqlMakeAdmin = "
        INSERT INTO group_admins (groupsId, adminsId) 
        VALUES (:groupId, :makeAdminUserId)
    ";
    $stmtMakeAdmin = $pdo->prepare($sqlMakeAdmin);
    $stmtMakeAdmin->bindValue(':groupId', $groupId, PDO::PARAM_INT);
    $stmtMakeAdmin->bindValue(':makeAdminUserId', $makeAdminUserId, PDO::PARAM_INT);

    if ($stmtMakeAdmin->execute()) {
        $_SESSION['success'] = 'Gebruiker is succesvol tot groepsbeheerder gemaakt.';
    } else {
        $_SESSION['error'] = 'Er is een fout opgetreden bij het maken van de groepsbeheerder.';
    }
    header('Location: ../index.php?page=groupchat&groupId=' . $groupId);

} catch (PDOException $e) {
    $_SESSION['error'] = 'Databasefout: ' . $e->getMessage();
}

exit();
?>