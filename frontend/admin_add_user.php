<?php
session_start();
require_once 'protected/auth_guard.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin-panel.php");
    exit;
}

$full_name = trim($_POST['full_name']);
$username = trim($_POST['username']);
$password = $_POST['password'];
$role = $_POST['role'];

// Валидация
if (empty($full_name) || empty($username) || empty($password)) {
    header("Location: admin-panel.php?error=Заполните все поля");
    exit;
}

if (!in_array($role, ['student', 'teacher'])) {
    header("Location: admin-panel.php?error=Недопустимая роль");
    exit;
}

try {
    // Проверка на уникальность логина
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        header("Location: admin-panel.php?error=Логин уже занят");
        exit;
    }

    // Хешируем и сохраняем
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $hashed, $role, $full_name]);

    header("Location: admin-panel.php?success=Пользователь добавлен");
    exit;

} catch (Exception $e) {
    header("Location: admin-panel.php?error=Ошибка сервера");
    exit;
}
?>