<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] != 'student') {
    header("Location: ../dashboard.php");
    exit;
}

$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Кабинет — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/student-style.css">
    <link rel="stylesheet" href="../assets/css/accessibility.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <small>Кабинет студента</small>
    </div>
    <nav class="mt-3">
        <a href="panel.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php"><i class="bi bi-file-text me-2"></i>Мои работы</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card rounded shadow-sm mb-4">
        <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
        <p>Вы вошли как <strong>Студент</strong></p>
    </div>
    
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-calendar display-4 text-primary"></i>
                    <h5 class="mt-2">Расписание</h5>
                    <p class="text-muted small mb-0">Ваши занятия</p>
                    <a href="schedule.php" class="btn btn-sm btn-primary mt-2">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-star display-4 text-warning"></i>
                    <h5 class="mt-2">Оценки</h5>
                    <p class="text-muted small mb-0">Ваши оценки</p>
                    <a href="grades.php" class="btn btn-sm btn-warning mt-2 text-white">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-file-text display-4 text-info"></i>
                    <h5 class="mt-2">Задания</h5>
                    <p class="text-muted small mb-0">Домашка</p>
                    <a href="assignments.php" class="btn btn-sm btn-info mt-2 text-white">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-journal display-4 text-success"></i>
                    <h5 class="mt-2">Библиотека</h5>
                    <p class="text-muted small mb-0">Материалы</p>
                    <a href="library.php" class="btn btn-sm btn-success mt-2">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-bell display-4 text-danger"></i>
                    <h5 class="mt-2">Уведомления</h5>
                    <p class="text-muted small mb-0">Сообщения</p>
                    <a href="notifications.php" class="btn btn-sm btn-danger mt-2">Открыть</a>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sync.js"></script>
<script src="../assets/js/accessibility.js"></script>
</body>
</html>