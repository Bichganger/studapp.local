<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

$user = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$flash = getFlashMessage();
$role = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Учеба24' ?> — Студенческий компас</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php if ($currentPage === 'map'): ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php endif; ?>
    <link rel="stylesheet" href="/assets/css/style.css?v=3">
</head>
<body class="role-<?= $role ?? 'guest' ?>">
<?php if ($flash): ?>
<div class="flash-alert alert alert-<?= $flash['type'] ?> alert-dismissible fade show m-0 rounded-0" role="alert">
    <div class="container">
        <i class="bi bi-info-circle me-2"></i><?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/index.php">
            <div class="logo-icon"><i class="bi bi-compass-fill"></i></div>
            <div>
                <span class="brand-title">Учеба24</span>
                <small class="brand-subtitle d-block">Студенческий компас</small>
            </div>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>" href="/index.php"><i class="bi bi-house-door me-1"></i>Главная</a></li>
                <?php if ($user && in_array($role, ['student', 'admin'])): ?>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'library' ? 'active' : '' ?>" href="/student/library.php"><i class="bi bi-journal-bookmark me-1"></i>Библиотека</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'map' ? 'active' : '' ?>" href="/student/map.php"><i class="bi bi-geo-alt me-1"></i>Карта</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'teachers' ? 'active' : '' ?>" href="/student/teachers.php"><i class="bi bi-people me-1"></i>Преподаватели</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'tips' ? 'active' : '' ?>" href="/student/tips.php"><i class="bi bi-lightbulb me-1"></i>Советы</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if ($user): ?>
                <li class="nav-item">
                    <a class="btn btn-accent btn-sm me-2" href="/dashboard.php">
                        <i class="bi bi-speedometer2 me-1"></i>Личный кабинет
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        <div class="user-avatar"><i class="bi bi-person-circle"></i></div>
                        <span><?= e($user['name']) ?></span>
                        <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;"><?= e($user['role']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Панель управления</a></li>
                        <?php if ($role === 'admin'): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-warning" href="/admin/dashboard.php"><i class="bi bi-shield-lock me-2"></i>Админ-панель</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'student'): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-success" href="/student/panel.php"><i class="bi bi-mortarboard me-2"></i>Кабинет студента</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Выйти</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item"><a class="btn btn-outline-light btn-sm me-2" href="/dashboard.php"><i class="bi bi-box-arrow-in-right me-1"></i>Вход</a></li>
                <li class="nav-item"><a class="btn btn-accent btn-sm" href="/register_new.php"><i class="bi bi-person-plus me-1"></i>Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main>
