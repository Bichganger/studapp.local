<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register_new.php');
    exit;
}

$full_name = trim($_POST['full_name']);
$username = trim($_POST['username']);
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];
$group_name = trim($_POST['group_name']);
$course = intval($_POST['course']);
$role = 'student';

if (empty($full_name) || empty($username) || empty($password)) {
    header('Location: register_new.php?error=Заполните все поля');
    exit;
}
if ($password !== $password_confirm) {
    header('Location: register_new.php?error=Пароли не совпадают');
    exit;
}
if (strlen($password) < 6) {
    header('Location: register_new.php?error=Пароль должен быть не менее 6 символов');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        header('Location: register_new.php?error=Пользователь уже существует');
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, username, password_hash, role, group_name, course) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$full_name, $username, $hashed_password, $role, $group_name, $course]);

    $user_id = $pdo->lastInsertId();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['full_name'] = $full_name;
    $_SESSION['role'] = $role;
    $_SESSION['group_name'] = $group_name;

    header('Location: student/panel.php');
    exit;
} catch (PDOException $e) {
    header('Location: register_new.php?error=Ошибка регистрации');
    exit;
}
