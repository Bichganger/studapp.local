<?php
session_start();
require_once 'protected/auth_guard.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: dashboard.php");
    exit;
}

$name = htmlspecialchars($_SESSION['full_name']);
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
        .sidebar { min-height: 100vh; background: #343a40; color: white; position: fixed; width: 240px; left: 0; top: 0; padding: 0; }
        .sidebar-header { background: #28a745; padding: 20px; text-align: center; }
        .sidebar a { color: #adb5bd; margin: 5px 10px; border-radius: 5px; display: block; padding: 10px 15px; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background: #28a745; color: white; }
        .main-content { margin-left: 240px; padding: 20px; }
        footer { margin-left: 240px; padding: 15px; text-align: center; font-size: 0.85rem; color: #6c757d; }
        .card { border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h5 class="mb-0"><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <small>Кабинет студента</small>
    </div>
    <nav class="mt-3">
        <a href="student-panel.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="student/schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="student/grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="student/assignments.php"><i class="bi bi-file-text me-2"></i>Задания</a>
        <a href="student/library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="student/settings.php"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr class="mx-2">
        <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <h3 class="mb-4">Кабинет студента</h3>
    <p class="text-muted">Добро пожаловать, <?= $name ?>!</p>
    
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-calendar display-4 text-primary"></i>
                    <h5 class="mt-2">Расписание</h5>
                    <p class="text-muted small mb-0">Ваше расписание занятий</p>
                    <a href="student/schedule.php" class="btn btn-sm btn-primary mt-2">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-star display-4 text-warning"></i>
                    <h5 class="mt-2">Оценки</h5>
                    <p class="text-muted small mb-0">Ваши текущие оценки</p>
                    <a href="student/grades.php" class="btn btn-sm btn-warning mt-2 text-white">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-file-text display-4 text-info"></i>
                    <h5 class="mt-2">Задания</h5>
                    <p class="text-muted small mb-0">Ваши домашние задания</p>
                    <a href="student/assignments.php" class="btn btn-sm btn-info mt-2 text-white">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-journal display-4 text-success"></i>
                    <h5 class="mt-2">Библиотека</h5>
                    <p class="text-muted small mb-0">Материалы и работы</p>
                    <a href="student/library.php" class="btn btn-sm btn-success mt-2">Открыть</a>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
