<?php
session_start();
require_once 'protected/auth_guard.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin-panel.php");
    exit;
}

$id = (int)$_GET['id'];

require_once '../config/db.php';

try {
    // Нельзя удалить самого себя
    if ($id === $_SESSION['user_id']) {
        header("Location: admin-panel.php?error=Нельзя удалить свой аккаунт");
        exit;
    }

    // Проверим, существует ли пользователь
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        header("Location: admin-panel.php?error=Пользователь не найден");
        exit;
    }

    // Запрещаем удалять других админов
    if ($user['role'] === 'admin') {
        header("Location: admin-panel.php?error=Нельзя удалить другого администратора");
        exit;
    }

    // Удаляем
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin-panel.php?success=Пользователь удалён");
    exit;

} catch (Exception $e) {
    header("Location: admin-panel.php?error=Ошибка сервера");
    exit;
}
?>