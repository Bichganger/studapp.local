<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

$user = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$flash = getFlashMessage();
$role = $_SESSION['role'] ?? '';
$isAdmin = $role === 'admin';
$isStudent = $role === 'student';
$isTeacher = $role === 'teacher';
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
    <link rel="stylesheet" href="/assets/css/style.css?v=8">
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
                
                <?php if ($user && ($isStudent || $isAdmin)): ?>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'library' ? 'active' : '' ?>" href="/student/library.php"><i class="bi bi-journal-bookmark me-1"></i>Библиотека</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'map' && !$isAdmin ? 'active' : '' ?>" href="/student/map.php"><i class="bi bi-geo-alt me-1"></i>Карта</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'teachers' ? 'active' : '' ?>" href="/student/teachers.php"><i class="bi bi-people me-1"></i>Преподаватели</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'tips' ? 'active' : '' ?>" href="/student/tips.php"><i class="bi bi-lightbulb me-1"></i>Советы</a></li>
                <?php endif; ?>
                
                <?php if ($isTeacher): ?>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'journal' ? 'active' : '' ?>" href="/teacher/journal.php"><i class="bi bi-journal-text me-1"></i>Журнал</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'grades' ? 'active' : '' ?>" href="/teacher/grades.php"><i class="bi bi-star me-1"></i>Оценки</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'notifications' ? 'active' : '' ?>" href="/teacher/notifications.php"><i class="bi bi-bell me-1"></i>Рассылка</a></li>
                <?php endif; ?>
                
                <?php if ($isAdmin): ?>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'map' && $isAdmin ? 'active' : '' ?>" href="/admin/map.php"><i class="bi bi-map me-1"></i>Карта (админ)</a></li>
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
                        <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;"><?= e($role) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Панель управления</a></li>
                        
                        <?php if ($isAdmin): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-warning" href="/admin/dashboard.php"><i class="bi bi-shield-lock me-2"></i>Админ-панель</a></li>
                        <li><a class="dropdown-item" href="/admin/users.php"><i class="bi bi-people me-2"></i>Пользователи</a></li>
                        <li><a class="dropdown-item" href="/admin/groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a></li>
                        <li><a class="dropdown-item" href="/admin/teachers.php"><i class="bi bi-person-badge me-2"></i>Преподаватели</a></li>
                        <li><a class="dropdown-item" href="/admin/schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a></li>
                        <li><a class="dropdown-item" href="/admin/library.php"><i class="bi bi-journal me-2"></i>Библиотека</a></li>
                        <li><a class="dropdown-item" href="/admin/notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a></li>
                        <li><a class="dropdown-item" href="/admin/tips.php"><i class="bi bi-lightbulb me-2"></i>Советы</a></li>
                        <li><hr class="dropdown-divider"></li>
                        
                        <?php elseif ($isStudent): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-success" href="/student/panel.php"><i class="bi bi-mortarboard me-2"></i>Кабинет студента</a></li>
                        <li><a class="dropdown-item" href="/student/schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a></li>
                        <li><a class="dropdown-item" href="/student/grades.php"><i class="bi bi-star me-2"></i>Оценки</a></li>
                        <li><a class="dropdown-item" href="/student/assignments.php"><i class="bi bi-file-text me-2"></i>Задания</a></li>
                        <li><a class="dropdown-item" href="/student/notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a></li>
                        <li><hr class="dropdown-divider"></li>
                        
                        <?php elseif ($isTeacher): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-info" href="/teacher/panel.php"><i class="bi bi-person-badge me-2"></i>Кабинет преподавателя</a></li>
                        <li><a class="dropdown-item" href="/teacher/journal.php"><i class="bi bi-journal-text me-2"></i>Журнал</a></li>
                        <li><a class="dropdown-item" href="/teacher/grades.php"><i class="bi bi-star me-2"></i>Выставить оценку</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        
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
