<?php

if (!empty($_SESSION['adminId'])) {
    include 'inc/navItems/admin.nav.php';
} elseif (!empty($_SESSION['userId'])) {
    include 'inc/navItems/user.nav.php';
} else {
    include 'inc/navItems/geust.nav.php';
}
?>