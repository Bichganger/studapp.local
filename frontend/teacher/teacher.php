<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'teacher') {
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
    <link rel="stylesheet" href="../css/teacher-style.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <small>Кабинет преподавателя</small>
    </div>
    <nav class="mt-3">
        <a href="teacher.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="teacher-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="teacher-journal.php"><i class="bi bi-journal-text me-2"></i>Журнал</a>
        <a href="teacher-grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="teacher-assignments.php"><i class="bi bi-file-text me-2"></i>Работы</a>
        <a href="teacher-groups.php"><i class="bi bi-people me-2"></i>Группы</a>
        <a href="teacher-notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-2">
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card rounded shadow-sm mb-4">
        <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
        <p>Вы вошли как <strong>Преподаватель</strong></p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-calendar display-4 text-primary"></i>
                    <h5 class="mt-2">Расписание</h5>
                    <a href="teacher-schedule.php" class="btn btn-sm btn-primary mt-2">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-star display-4 text-warning"></i>
                    <h5 class="mt-2">Оценки</h5>
                    <a href="teacher-grades.php" class="btn btn-sm btn-warning mt-2 text-white">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-file-text display-4 text-info"></i>
                    <h5 class="mt-2">Работы</h5>
                    <a href="teacher-assignments.php" class="btn btn-sm btn-info mt-2 text-white">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-journal-text display-4 text-success"></i>
                    <h5 class="mt-2">Журнал</h5>
                    <a href="teacher-journal.php" class="btn btn-sm btn-success mt-2">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-people display-4 text-info"></i>
                    <h5 class="mt-2">Группы</h5>
                    <a href="teacher-groups.php" class="btn btn-sm btn-info mt-2 text-white">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-bell display-4 text-danger"></i>
                    <h5 class="mt-2">Уведомления</h5>
                    <a href="teacher-notifications.php" class="btn btn-sm btn-danger mt-2">Открыть</a>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/teacher.js"></script>
</body>
</html>


