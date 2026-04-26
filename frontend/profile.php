<?php
session_start();
require_once 'protected/auth_guard.php';

$fullName = htmlspecialchars($_SESSION['full_name']);
$username = htmlspecialchars($_SESSION['username']);
$role = htmlspecialchars($_SESSION['role']);
$roles = [
    'student' => 'Студент',
    'teacher' => 'Преподаватель',
    'admin' => 'Администратор'
];
$roleLabel = $roles[$role] ?? $role;
$currentDate = date("j F Y");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль — StudApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .profile-card { max-width: 600px; margin: 40px auto; }
        .avatar { width: 100px; height: 100px; border-radius: 50%; background: #0d6efd; color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 20px; }
    </style>
</head>
<body>
<div class="profile-card">
    <div class="card">
        <div class="card-body text-center">
            <div class="avatar">
                <?= strtoupper(substr($fullName, 0, 1)) ?>
            </div>
            <h3><?= $fullName ?></h3>
            <p class="text-muted"><?= $username ?></p>

            <div class="row g-3 mt-3">
                <div class="col-6">
                    <strong>Роль</strong><br>
                    <span class="badge bg-<?= $role === 'admin' ? 'danger' : ($role === 'teacher' ? 'success' : 'primary') ?>">
                        <?= $roleLabel ?>
                    </span>
                </div>
                <div class="col-6">
                    <strong>Дата входа</strong><br>
                    <?= $currentDate ?>
                </div>
            </div>

            <hr>

            <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                <a href="../frontend/settings.php" class="btn btn-outline-primary">
                    <i class="bi bi-gear"></i> Настройки
                </a>
                <a href="../frontend/dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>