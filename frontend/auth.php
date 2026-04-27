<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header("Location: login.php?error=Заполните все поля");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, full_name, role, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            switch ($user['role']) {
                case 'admin':
                    header("Location: admin-panel.php");
                    break;
                case 'teacher':
                    header("Location: teacher.php");
                    break;
                default:
                    header("Location: dashboard.php");
            }
            exit;
        } else {
            header("Location: login.php?error=Неверный логин или пароль");
            exit;
        }
    } catch (Exception $e) {
        header("Location: login.php?error=Ошибка сервера");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>