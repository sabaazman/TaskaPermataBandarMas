<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$peranan_id = $_SESSION['peranan_id'];

switch ($peranan_id) {
    case 1:
        header("Location: pentadbir_dashboard.php");
        break;
    case 2:
        header("Location: ibubapa_dashboard.php");
        break;
    case 3:
        header("Location: pendidik_dashboard.php");
        break;
    default:
        echo "Unknown role. Please contact the system administrator.";
        exit();
}
?>
