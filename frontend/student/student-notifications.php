<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: ../dashboard.php");
    exit;
}

$name = explode(' ', $_SESSION['full_name'])[0] ?? $_SESSION['full_name'];
$fullName = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Уведомления — Кабинет студента — Учёба.Онлайн</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: sans-serif; }
        .sidebar {
            min-height: 100vh;
            background: #0d6efd;
            color: white;
            position: fixed;
            width: 260px;
            left: 0;
            top: 0;
            padding: 20px 0;
        }
        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            margin: 5px 10px;
            border-radius: 5px;
            display: block;
            padding: 8px 15px;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #0b5ed7;
            color: white;
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        footer { margin-left: 260px; padding: 20px; text-align: center; font-size: 0.9rem; color: #6c757d; }
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        .notification-item:hover { background: #f8f9fa; }
        .notification-item:last-child { border-bottom: none; }
        .notification-item.unread { background: #e7f3ff; }
        .notification-icon {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .icon-info { background: #e7f3ff; color: #0d6efd; }
        .icon-warning { background: #fff3cd; color: #ffc107; }
        .icon-success { background: #d4edda; color: #198754; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-mortarboard"></i> Учёба.Онлайн</h5>
        <p class="text-white-50 small">Студент</p>
    </div>
    <nav>
        <a href="student-dashboard.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="student-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="student-grades.php"><i class="bi bi-graph-up me-2"></i>Оценки</a>
        <a href="student-assignments.php"><i class="bi bi-journal-check me-2"></i>Мои работы</a>
        <a href="student-library.php"><i class="bi bi-book me-2"></i>Библиотека</a>
        <a href="student-notifications.php" class="active"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <a href="student-dashboard.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Назад
    </a>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-bell"></i> Уведомления</h2>
        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-check-all me-1"></i>Отметить все прочитанными</button>
    </div>

    <!-- Статистика -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">2</h3>
                    <p class="mb-0 text-muted">Новых</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">1</h3>
                    <p class="mb-0 text-muted">Важных</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">15</h3>
                    <p class="mb-0 text-muted">Прочитано</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Уведомления -->
    <div class="card">
        <div class="list-group list-group-flush">
            <div class="notification-item unread">
                <div class="d-flex">
                    <div class="notification-icon icon-success me-3"><i class="bi bi-check-lg"></i></div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Работа одобрена</h6>
                        <p class="mb-1 text-muted small">Ваша курсовая работа по ООП одобрена преподавателем</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>2 часа назад</small>
                    </div>
                </div>
            </div>
            <div class="notification-item unread">
                <div class="d-flex">
                    <div class="notification-icon icon-info me-3"><i class="bi bi-info-lg"></i></div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Изменение расписания</h6>
                        <p class="mb-1 text-muted small">Завтра в 14:00 состоится дополнительная консультация</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>5 часов назад</small>
                    </div>
                </div>
            </div>
            <div class="notification-item">
                <div class="d-flex">
                    <div class="notification-icon icon-warning me-3"><i class="bi bi-exclamation-triangle"></i></div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Напоминание о дедлайне</h6>
                        <p class="mb-1 text-muted small">Завтра последний срок сдачи лабораторной работы по физике</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>Вчера</small>
                    </div>
                </div>
            </div>
            <div class="notification-item">
                <div class="d-flex">
                    <div class="notification-icon icon-success me-3"><i class="bi bi-upload"></i></div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Работа принята</h6>
                        <p class="mb-1 text-muted small">Ваша работа успешно загружена в систему</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>2 дня назад</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
