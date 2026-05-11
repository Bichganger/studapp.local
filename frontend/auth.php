<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header("Location: dashboard.php?error=Заполните все поля");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, full_name, role, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            // Проверяем пароль (поддержка и хэшированных и простых паролей)
            $passwordValid = password_verify($password, $user['password']);
            
            // Fallback для простых паролей (если хэш не подошел)
            if (!$passwordValid && $password === $user['password']) {
                $passwordValid = true;
            }

            if ($passwordValid) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['group_name'] = $user['group_name'];

                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin/dashboard.php");
                        break;
                    case 'teacher':
                        header("Location: teacher/panel.php");
                        break;
                    case 'student':
                        header("Location: student/panel.php");
                        break;
                    default:
                        header("Location: dashboard.php");
                }
                exit;
            }
        }
        
        header("Location: dashboard.php?error=Неверный логин или пароль");
        exit;
    } catch (Exception $e) {
        header("Location: dashboard.php?error=Ошибка сервера");
        exit;
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>