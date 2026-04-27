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

$roleBadgeClass = match($role) {
    'admin' => 'bg-danger',
    'teacher' => 'bg-success',
    'student' => 'bg-primary',
};

$currentDate = date("j F Y");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабинет — Учёба.Онлайн</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .navbar-brand { font-weight: 600; }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }
        .card:hover { transform: translateY(-2px); }
        .card-icon { font-size: 2.2rem; margin-bottom: 8px; }
        .footer { margin-top: 60px; text-align: center; font-size: 0.9rem; color: #6c757d; padding: 20px 0; }
        .profile-img {
            width: 50px; height: 50px; border-radius: 50%;
            background: #0d6efd; color: white; display: flex;
            align-items: center; justify-content: center;
            font-size: 1.2rem; font-weight: bold;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php"><i class="bi bi-journal-code"></i> Учёба.Онлайн</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-house-door me-1"></i>Главная</a></li>
                <li class="nav-item"><a class="nav-link" href="schedule.php"><i class="bi bi-calendar-event me-1"></i>Расписание</a></li>
                <li class="nav-item"><a class="nav-link" href="distance-learning.php"><i class="bi bi-laptop me-1"></i>Дистанционка</a></li>
                <li class="nav-item"><a class="nav-link" href="grades.php"><i class="bi bi-graph-up me-1"></i>Успеваемость</a></li>
            </ul>
            <ul class="navbar-nav d-flex align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="profile-img me-2"><?= strtoupper(substr($name, 0, 1)) ?></div>
                        <span><?= $name ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Профиль</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Настройки</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Выход</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="p-4 bg-white rounded shadow-sm mb-4">
                <h3><i class="bi bi-person-circle text-primary"></i> Добро пожаловать, <?= $name ?>!</h3>
                <p class="text-muted">Вы вошли как <strong><?= $roleLabel ?></strong></p>
                <p class="small text-muted">Дата входа: <?= $currentDate ?></p>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <a href="schedule.php" class="card text-decoration-none text-reset h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-event card-icon text-primary"></i>
                            <h5>Расписание</h5>
                            <p class="text-muted">Посмотреть пары на неделю</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="grades.php" class="card text-decoration-none text-reset h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up card-icon text-success"></i>
                            <h5>Успеваемость</h5>
                            <p class="text-muted">Оценки и посещаемость</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Ваш профиль</h5>
                </div>
                <div class="card-body">
                    <p><strong>ФИО:</strong> <?= $fullName ?></p>
                    <p><strong>Логин:</strong> <?= $username ?></p>
                    <p><strong>Роль:</strong>
                        <span class="badge <?= $roleBadgeClass ?>"><?= $roleLabel ?></span>
                    </p>
                    <a href="profile.php" class="btn btn-outline-primary btn-sm w-100">Подробнее</a>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bell"></i> Уведомления</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Все работы сданы</li>
                        <li class="mb-2"><i class="bi bi-clock-history text-warning"></i> Завтра пара в 9:00</li>
                        <li><i class="bi bi-info-circle-fill text-info"></i> Расписание обновлено</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer">&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>