<?php
include '../../Private/ConnectionMyBuddy/connection.php';

$userId = $_GET['userId'] ?? null;

$sql = 'UPDATE users SET status = 1 WHERE userId = :userId';
$statement = $pdo->prepare($sql);
$statement->execute(['userId' => $userId]);

$_SESSION['success'] = "Account is gedeactiveerd.";
header('Location: ../index.php?page=adminUsers');
exit;
