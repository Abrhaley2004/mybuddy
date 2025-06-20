<?php
session_start();
include '../../Private/ConnectionMyBuddy/connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $groupId = $_POST['groupId'];
    $createdAt = date("Y-m-d");

    $check = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $check->execute(['email' => $email]);
    if ($check->rowCount() > 0) {
        $_SESSION['error'] = "Deze gebruiker heeft al een account.";
        header("Location: ../index.php?page=invitepage");
        exit;
    }

    $token = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("INSERT INTO invitations (email, groupId, token, status, createdAt)
                           VALUES (:email, :groupId, :token, 0, :createdAt)");
    $stmt->execute([
        'email' => $email,
        'groupId' => $groupId,
        'token' => $token,
        'createdAt' => $createdAt
    ]);

    $registerLink = "http://localhost/MyBuddy/index.php?page=loginRegister&token=" . $token;


    $bericht = "Hoi!\n\nJe bent uitgenodigd om je aan te melden bij een groep op MyBuddy.\n";
    $bericht .= "Registreer je via deze link: $registerLink\n\nGroetjes,\nMyBuddy Team";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'merhanbakboord@gmail.com';
        $mail->Password = 'irjl hese jbyo yhop ';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('noreply@mybuddy.nl', 'MyBuddy');
        $mail->addAddress($email);
        $mail->Subject = 'Jouw MyBuddy uitnodiging';
        $mail->Body = $bericht;

        $mail->send();

    } catch (Exception $e) {
        $_SESSION['error'] = "Mail verzenden mislukt: " . $mail->ErrorInfo;
    }

    $_SESSION['success'] = "Uitnodiging verstuurd naar $email";
    header("Location: ../index.php?page=invitePage");
    exit;

}
?>
