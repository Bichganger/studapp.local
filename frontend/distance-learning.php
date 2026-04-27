<?php
session_start();
require_once 'protected/auth_guard.php';

$name = explode(' ', $_SESSION['full_name'])[0] ?? $_SESSION['full_name'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Дистанционка — Учёба.Онлайн</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: sans-serif; }
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        footer { text-align: center; margin-top: 60px; font-size: 0.9rem; color: #6c757d; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php"><i class="bi bi-journal-code"></i> Учёба.Онлайн</a>
        <div class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <?= htmlspecialchars($name) ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Профиль</a></li>
                    <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Настройки</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Выход</a></li>
                </ul>
            </li>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2><i class="bi bi-laptop"></i> Дистанционное обучение</h2>
    <p class="text-muted">Онлайн-занятия и материалы для самостоятельного изучения</p>

    <div class="list-group mb-4">
        <a href="#" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">Математический анализ — Лекция 3</h5>
                <span class="badge bg-primary">Видео</span>
            </div>
            <p class="mb-1">Интегралы и их свойства</p>
            <small class="text-muted">Доступно до: 30 ноября</small>
        </a>
        <a href="#" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">Физика — Лабораторная работа</h5>
                <span class="badge bg-success">PDF</span>
            </div>
            <p class="mb-1">Определение ускорения свободного падения</p>
            <small class="text-muted">Сдать до: 15 декабря</small>
        </a>
    </div>

    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill"></i> Проверьте интернет-соединение перед началом занятия.
    </div>
</div>

<footer>&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>