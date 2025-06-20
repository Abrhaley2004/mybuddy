<?php
session_start();
unset($_SESSION['userId']);
unset($_SESSION['adminId']);
session_unset();
header('location: ../index.php?page=loginRegister');
exit();
?>