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
$password_confirm = $_POST['password_confirm'];
$group_name = trim($_POST['group_name']);
$course = (int)$_POST['course'];
$role = $_POST['role'] ?? 'student';

if (empty($full_name) || empty($username) || empty($password) || empty($group_name)) {
    header("Location: register.php?error=Заполните все поля");
    exit;
}

if ($password !== $password_confirm) {
    header("Location: register.php?error=Пароли не совпадают");
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

    $normalized_name = mb_strtolower(preg_replace('/\s+/', ' ', trim($full_name)), 'UTF-8');
    $normalized_group = mb_strtoupper(preg_replace('/\s+/', '-', trim($group_name)), 'UTF-8');

    $stmt = $pdo->prepare("
        SELECT id, full_name, group_name, course, status 
        FROM students 
        WHERE LOWER(REPLACE(full_name, ' ', '')) LIKE ?
        AND UPPER(REPLACE(group_name, ' ', '')) = ?
        AND course = ?
        AND status = 'active'
        LIMIT 1
    ");
    
    $search_name = '%' . str_replace(' ', '', $normalized_name) . '%';
    $search_group = str_replace(' ', '', $normalized_group);
    
    $stmt->execute([$search_name, $search_group, $course]);
    $student = $stmt->fetch();

    if (!$student) {
        header("Location: register.php?error=Студент с такими данными не найден в базе колледжа");
        exit;
    }

    $stmt = $pdo->prepare("SELECT user_id FROM student_accounts WHERE student_id = ?");
    $stmt->execute([$student['id']]);
    if ($stmt->fetch()) {
        header("Location: register.php?error=Этот студент уже зарегистрирован в системе");
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $hashed, $role, $full_name]);

    $user_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO student_accounts (user_id, student_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $student['id']]);

    header("Location: register.php?success=Регистрация успешна");
    exit;

} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    header("Location: register.php?error=Ошибка сервера: " . $e->getMessage());
    exit;
}
?>