<?php
session_start();
require_once 'protected/auth_guard.php';

$name = explode(' ', $_SESSION['full_name'])[0] ?? $_SESSION['full_name'];
$fullName = htmlspecialchars($_SESSION['full_name']);
$username = htmlspecialchars($_SESSION['username']);
$role = $_SESSION['role'];

$roleLabel = match($role) {
    'admin' => 'Администратор',
    'teacher' => 'Преподаватель',
    'student' => 'Студент',
    default => 'Пользователь'
};
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Настройки — Учёба.Онлайн</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: sans-serif; }
        .container { max-width: 600px; margin: 40px auto; }
        .footer { text-align: center; margin-top: 60px; font-size: 0.9rem; color: #6c757d; }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4"><i class="bi bi-gear"></i> Настройки аккаунта</h2>
    <div class="card">
        <div class="card-body">
            <form>
                <div class="mb-3">
                    <label class="form-label">ФИО</label>
                    <input type="text" class="form-control" value="<?= $fullName ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Логин</label>
                    <input type="text" class="form-control" value="<?= $username ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Роль</label>
                    <input type="text" class="form-control" value="<?= $roleLabel ?>" readonly>
                </div>
                <button type="submit" class="btn btn-primary" disabled>Сохранить</button>
            </form>
        </div>
    </div>
    <a href="dashboard.php" class="btn btn-link mt-3"><i class="bi bi-arrow-left"></i> Назад</a>
</div>

<footer class="footer">&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>