<?php

include '../../Private/ConnectionMyBuddy/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $groupname = $_POST['name'] ?? null;
    $createdAt = $_POST['updatedAt'];
    $groupId = $_POST['groupId'];
    $image = null;


    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['image']['name'],  PATHINFO_EXTENSION);
        $image = uniqid('group', true) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
    }

    try {

        $sql = " update groups  set name= :name  , image = :image ,  createdAt = :updatedAt where groupsId = :groupsId  ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $groupname);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':updatedAt', $createdAt);
        $stmt->bindParam(':groupsId', $groupId);
        $stmt->execute();

        header("Location: ../index.php?page=groupchat&groupId=" .$groupId);
        exit;
    } catch (PDOException $e) {
        echo "Fout bij aanmaken groep: " . $e->getMessage();
    }
} else {
    echo "Ongeldige aanvraag.";
}