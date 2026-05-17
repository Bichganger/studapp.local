<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['flash'] = ['message' => 'Заполните все поля', 'type' => 'danger'];
        header('Location: dashboard.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, name, email, role, group_name, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            $passwordValid = password_verify($password, $user['password_hash']);
            if (!$passwordValid && $password === $user['password_hash']) {
                $passwordValid = true;
            }

            if ($passwordValid) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['group_name'] = $user['group_name'];

                switch ($user['role']) {
                    case 'admin': header('Location: admin/dashboard.php'); exit;
                    case 'teacher': header('Location: teacher/panel.php'); exit;
                    case 'student': header('Location: student/panel.php'); exit;
                    default: header('Location: dashboard.php'); exit;
                }
            }
        }
        
        $_SESSION['flash'] = ['message' => 'Неверный логин или пароль', 'type' => 'danger'];
        header('Location: dashboard.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['flash'] = ['message' => 'Ошибка сервера', 'type' => 'danger'];
        header('Location: dashboard.php');
        exit;
    }
} else {
    header('Location: dashboard.php');
    exit;
}
