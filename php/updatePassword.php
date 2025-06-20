<?php
session_start();
include '../../Private/ConnectionMyBuddy/connection.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['error'] = "Geen gebruiker ingelogd.";
    header("Location: ../index.php?page=loginRegister");
    exit;
}

$newPassword = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$veranderWw = !empty($newPassword) || !empty($confirmPassword);

if ($veranderWw) {
    if (empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['error'] = "Vul beide wachtwoordvelden in.";
        header("Location: ../index.php?page=userProfile");
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Wachtwoorden komen niet overeen.";
        header("Location: ../index.php?page=userProfile");
        exit;
    }

    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    if (!preg_match($pattern, $newPassword)) {
        $_SESSION['error'] = "Wachtwoord moet minimaal 8 tekens bevatten, met minstens één hoofdletter, één kleine letter, één cijfer en één speciaal teken.";
        header('Location: ../index.php?page=userProfile');
        exit;
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $adminSql = "UPDATE users SET password = :newPassword WHERE userId = :userId";
    $adminStmt = $pdo->prepare($adminSql);
    $adminStmt->execute([
        'newPassword' => $hashedPassword,
        'userId' => $_SESSION['userId']
    ]);

    $_SESSION['success'] = "Wachtwoord is succesvol aangepast.";
    header("Location: ../index.php?page=userProfile");
    exit;
}
?>
