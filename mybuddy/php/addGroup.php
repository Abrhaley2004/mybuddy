<?php

include '../../Private/ConnectionMyBuddy/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $groupname = $_POST['name'] ?? null;
    $members = $_POST['groupmembers'] ?? [];
    $createdByUserId = $_POST['userId'];
    $createdAt = $_POST['createdAt'];
    $image = null;


    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid('group', true) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO groups (name, image, createdAt, createdByUserId)   
                               VALUES (:name, :image,  :createdAt,  :createdByUserId)");
        $stmt->bindParam(':name', $groupname);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':createdAt', $createdAt);
        $stmt->bindParam(':createdByUserId', $createdByUserId);
        $stmt->execute();

        $groupId = $pdo->lastInsertId();

        $stmtLink = $pdo->prepare("INSERT INTO group_members (groupId, userId) VALUES (:groupId, :userId)");
        foreach ($members as $userId) {
            $stmtLink->execute([':groupId' => $groupId, ':userId' => $userId]);
        }
        if (!in_array($createdByUserId, $members)) {
            $stmtLink->execute([':groupId' => $groupId, ':userId' => $createdByUserId]);
        }

        $stmtAdmin = $pdo->prepare("INSERT INTO group_admins (groupsId, adminsId) VALUES (:groupId, :userId)");
        $stmtAdmin->execute([':groupId' => $groupId, ':userId' => $createdByUserId]);

        header("Location: ../index.php?page=groupsViews");
        exit;
    } catch (PDOException $e) {
        echo "Fout bij aanmaken groep: " . $e->getMessage();
    }

} else {
    echo "Ongeldige aanvraag.";
}