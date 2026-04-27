<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

$full_name = trim($_POST['full_name']);
$username = trim($_POST['username']);
$password = $_POST['password'];
$role = $_POST['role'] ?? 'student';

if (empty($full_name) || empty($username) || empty($password)) {
    header("Location: register.php?error=Заполните все поля");
    exit;
}

if ($role !== 'student') {
    header("Location: register.php?error=Регистрация доступна только студентам");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        header("Location: register.php?error=Логин уже занят");
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $hashed, $role, $full_name]);

    header("Location: register.php?success=Регистрация успешна! Войдите в систему");
    exit;

} catch (Exception $e) {
    header("Location: register.php?error=Ошибка сервера");
    exit;
}
?>