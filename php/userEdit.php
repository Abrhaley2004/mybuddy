<?php
session_start();
include '../../Private/ConnectionMyBuddy/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'] ?? null;
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $name = trim($_POST['name']);
    $date = $_POST['date'];

    // Validatie
    if (empty($userId) || empty($email) || empty($password) || empty($name) || empty($date)) {
        $_SESSION['error'] = "Vul alle velden in.";
        header("Location: ../index.php?page=userEdit&UserId=$userId");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $sql = "UPDATE users 
                SET email = :email, password = :password, name = :name, registrationDate = :registrationDate
                WHERE userId = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'password' => $hashedPassword,
            'name' => $name,
            'registrationDate' => $date,
            'userId' => $userId
        ]);

        $_SESSION['success'] = "Gebruiker succesvol bijgewerkt.";
        header("Location: ../index.php?page=adminUsers");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Fout bij bijwerken: " . $e->getMessage();
        header("Location: ../index.php?page=userEdit&UserId=$userId");
        exit();
    }

} else {
    $_SESSION['error'] = "Ongeldige verzoekmethode.";
    header("Location: ../index.php?page=adminUsers");
    exit();
}
?>
