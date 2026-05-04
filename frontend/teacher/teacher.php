<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'teacher') {
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
    <title>Кабинет преподавателя — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: sans-serif; }
        .sidebar {
            min-height: 100vh;
            background: #343a40;
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
            background: #495057;
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
        <p class="text-white-50 small">Преподаватель</p>
    </div>
    <nav>
        <a href="teacher.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="teacher-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="teacher-journal.php"><i class="bi bi-journal-text me-2"></i>Журнал</a>
        <a href="teacher-grades.php"><i class="bi bi-graph-up me-2"></i>Выставление оценок</a>
        <a href="teacher-assignments.php"><i class="bi bi-journal-check me-2"></i>Работы студентов</a>
        <a href="teacher-groups.php"><i class="bi bi-people-fill me-2"></i>Мои группы</a>
        <a href="teacher-notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<!-- Основной контент -->
<main class="main-content">
    <h2><i class="bi bi-house"></i> Добро пожаловать, <?= $name ?>!</h2>
    <p class="text-muted">Это ваша главная панель управления. Выберите раздел в меню слева.</p>

    <!-- Быстрые действия -->
    <h5 class="mt-4 mb-3">Быстрый доступ</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="teacher-schedule.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar display-4 text-primary"></i>
                        <h5 class="mt-2">Расписание</h5>
                        <p class="text-muted small mb-0">Просмотреть расписание занятий</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="teacher-grades.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up display-4 text-success"></i>
                        <h5 class="mt-2">Оценки</h5>
                        <p class="text-muted small mb-0">Выставить оценки студентам</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="teacher-assignments.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-journal-check display-4 text-warning"></i>
                        <h5 class="mt-2">Работы</h5>
                        <p class="text-muted small mb-0">Проверить сданные работы</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="teacher-journal.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-journal-text display-4 text-success"></i>
                        <h5 class="mt-2">Журнал</h5>
                        <p class="text-muted small mb-0">Электронный журнал пар</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="teacher-groups.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill display-4 text-info"></i>
                        <h5 class="mt-2">Группы</h5>
                        <p class="text-muted small mb-0">Управление учебными группами</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="teacher-notifications.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-bell display-4 text-danger"></i>
                        <h5 class="mt-2">Уведомления</h5>
                        <p class="text-muted small mb-0">Просмотреть уведомления</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Статистика -->
    <h5 class="mt-5 mb-3">Статистика</h5>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary">2</h3>
                    <p class="mb-0 text-muted">Мои группы</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">12</h3>
                    <p class="mb-0 text-muted">Работ на проверку</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">46</h3>
                    <p class="mb-0 text-muted">Студентов всего</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">5</h3>
                    <p class="mb-0 text-muted">Новых уведомлений</p>
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


