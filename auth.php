<?php
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
    // Проверка наличия колонки is_approved
    $hasIsApproved = false;
    try {
        $cols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
        $hasIsApproved = in_array('is_approved', $cols);
    } catch (Exception $e) {}
    
    $stmt = $pdo->prepare("SELECT id, username, full_name as name, email, role, group_name, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        $passwordValid = password_verify($password, $user['password']);
        if (!$passwordValid && $password === $user['password']) {
            $passwordValid = true;
        }

        if ($passwordValid) {
            // Проверка одобрения пользователя
            if ($hasIsApproved && empty($user['is_approved']) && $user['role'] === 'student') {
                $_SESSION['flash'] = ['message' => 'Ваш аккаунт ожидает одобрения администратора', 'type' => 'warning'];
                header('Location: dashboard.php');
                exit;
            }
            
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
    error_log($e->getMessage());
    $_SESSION['flash'] = ['message' => 'Ошибка сервера: ' . $e->getMessage(), 'type' => 'danger'];
    header('Location: dashboard.php');
    exit;
}
} else {
    header('Location: dashboard.php');
    exit;
}
