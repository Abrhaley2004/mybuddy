<?php
include '../../Private/ConnectionMyBuddy/connection.php';

$groupId = $_POST['groupId'] ?? $_POST['groupId'] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["groupId"])) {
        $groupId = (int)$_POST["groupId"];

        $sql = "UPDATE groups SET isDeleted = 1 WHERE groupsId = :groupsId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':groupsId', $groupId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header('Location: ../index.php?page=groupsViews');
            exit();
        } else {
            echo "Er is een fout opgetreden bij het verwijderen van de groep.";
        }
    } else {
        echo "groupId ontbreekt.";
    }
}

?>
