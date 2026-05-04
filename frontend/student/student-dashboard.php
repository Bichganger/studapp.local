<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: ../dashboard.php");
    exit;
}

$name = explode(' ', $_SESSION['full_name'])[0] ?? $_SESSION['full_name'];
$fullName = htmlspecialchars($_SESSION['full_name']);
$currentDate = date("j F Y");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Кабинет студента — Учеба24</title>
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
    </style>
</head>
<body>

<!-- Боковое меню -->
<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <p class="text-white-50 small">Студент</p>
    </div>
    <nav>
        <a href="student-dashboard.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="student-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="student-grades.php"><i class="bi bi-graph-up me-2"></i>Оценки</a>
        <a href="student-assignments.php"><i class="bi bi-journal-check me-2"></i>Мои работы</a>
        <a href="student-library.php"><i class="bi bi-book me-2"></i>Библиотека</a>
        <a href="student-notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<!-- Основной контент -->
<main class="main-content">
    <h2><i class="bi bi-house"></i> Добро пожаловать, <?= $name ?>!</h2>
    <p class="text-muted">Это ваш личный кабинет. Выберите раздел в меню слева.</p>

    <!-- Быстрые действия -->
    <h5 class="mt-4 mb-3">Быстрый доступ</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="student-schedule.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-event display-4 text-primary"></i>
                        <h5 class="mt-2">Расписание</h5>
                        <p class="text-muted small mb-0">Посмотреть пары на неделю</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="student-grades.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up display-4 text-success"></i>
                        <h5 class="mt-2">Оценки</h5>
                        <p class="text-muted small mb-0">Просмотр успеваемости</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="student-assignments.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-journal-check display-4 text-warning"></i>
                        <h5 class="mt-2">Мои работы</h5>
                        <p class="text-muted small mb-0">Сданные работы и статусы</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="student-library.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-book display-4 text-info"></i>
                        <h5 class="mt-2">Библиотека</h5>
                        <p class="text-muted small mb-0">Учебные материалы</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="student-notifications.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-bell display-4 text-danger"></i>
                        <h5 class="mt-2">Уведомления</h5>
                        <p class="text-muted small mb-0">Просмотр уведомлений</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Статистика -->
    <h5 class="mt-5 mb-3">Моя статистика</h5>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary">4.5</h3>
                    <p class="mb-0 text-muted">Средний балл</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">95%</h3>
                    <p class="mb-0 text-muted">Посещаемость</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">12</h3>
                    <p class="mb-0 text-muted">Работ сдано</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">2</h3>
                    <p class="mb-0 text-muted">Новых уведомления</p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Подвал -->
<footer>&copy; 2026 Учеба24</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
