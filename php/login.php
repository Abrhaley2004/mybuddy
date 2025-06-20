<?php
session_start();
include '../../Private/ConnectionMyBuddy/connection.php';

$email = ($_POST['email']);
$password = $_POST['password'];

try {
    $sql = 'SELECT userId, email, password FROM users WHERE email = :email';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['userId'] = $user['userId'];

        $adminSql = "SELECT adminId, userId FROM admin WHERE userId = :userId";
        $adminStmt = $pdo->prepare($adminSql);
        $adminStmt->execute(['userId' => $user['userId']]);
        $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($admin['adminId'])) {
            $_SESSION['adminId'] = $admin['adminId'];
            header("Location: ../index.php?page=adminDashboard");
            exit();
        } else {
            header("Location: ../index.php?page=groupsViews");
            exit();
        }

    } else {
        $_SESSION['error'] = "Ongeldig e-mailadres of wachtwoord.";
        header('Location: ../index.php?page=loginRegister');
        exit;
    }

} catch (PDOException $e) {
    $_SESSION['error'] = "Er is een fout opgetreden. Probeer het later opnieuw.";
    header('Location: ../index.php?page=loginRegister');
    exit;
}

?>
