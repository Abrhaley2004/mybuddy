<?php
include '../../Private/ConnectionMyBuddy/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['userId'])) {
    $userId = $_GET['userId'];

    try {
        $delete = 'DELETE FROM users WHERE userid = :userid';
        $statement = $pdo->prepare($delete);
        $statement->execute(['userid' => $userId]);

        $_SESSION['success'] = "Account is verwijderd";
        header('Location: ../index.php?page=adminUsers');
        exit;

    } catch (PDOException $e) {
        echo "Fout bij verwijderen gebruiker: " . $e->getMessage();
    }
} else {
    echo "Geen gebruiker opgegeven.";
    exit;
}
?>
