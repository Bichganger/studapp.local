<?php
session_start();
require_once 'protected/auth_guard.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Перенаправление на новую админ-панель
header("Location: admin/admin-dashboard.php");
exit;
?>
