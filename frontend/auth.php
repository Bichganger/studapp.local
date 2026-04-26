<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    header("Location: login.php?error=Заполните все поля");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: login.php?error=Неверный логин или пароль");
        exit;
    }
} catch (Exception $e) {
    header("Location: login.php?error=Ошибка сервера");
    exit;
}
// После проверки логина/пароля
$stmt = $pdo->prepare("SELECT id, username, full_name, role FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user) {
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name']; // ← ВАЖНО!
    $_SESSION['role'] = $user['role'];

    // Редирект по роли
    switch ($user['role']) {
        case 'admin':
            header("Location: ../frontend/admin-panel.php");
            break;
        case 'teacher':
            header("Location: ../frontend/teacher.php");
            break;
        default:
            header("Location: ../frontend/dashboard.php");
    }
    exit;
}
?>