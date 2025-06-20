<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page = $_GET['page'] ?? 'homepage';

include __DIR__ . '/../Private/ConnectionMyBuddy/connection.php';
if (!empty($_SESSION['adminId'])) {
    include 'inc/navItems/admin.nav.php';
} elseif (!empty($_SESSION['userId'])) {
    include 'inc/navItems/user.nav.php';
} else {
    include 'inc/navItems/guest.nav.php';
}
?>

<html lang="nl">
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBuddy</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <link rel="icon" href="https://media.discordapp.net/attachments/1369606508907597829/1371433097085845614/ChatGPT_Image_12_mei_2025_12_25_07.png?ex=68231e1a&is=6821cc9a&hm=a696fc1b2b134f7aaeec2dafc1f2c0efae4f4a3a2915720a8c087c1823954c36&=&format=webp&quality=lossless&width=1050&height=1050">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<?php include 'inc/' . htmlspecialchars($page) . '.inc.php'; ?>
</body>
</html>