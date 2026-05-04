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
    <title>Библиотека — Кабинет студента — Учеба24</title>
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

<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <p class="text-white-50 small">Студент</p>
    </div>
    <nav>
        <a href="student-dashboard.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="student-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="student-grades.php"><i class="bi bi-graph-up me-2"></i>Оценки</a>
        <a href="student-assignments.php"><i class="bi bi-journal-check me-2"></i>Мои работы</a>
        <a href="student-library.php" class="active"><i class="bi bi-book me-2"></i>Библиотека</a>
        <a href="student-notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <a href="student-dashboard.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Назад
    </a>

    <h2><i class="bi bi-book"></i> Учебная библиотека</h2>
    <p class="text-muted">Здесь можно найти готовые лабораторные, отчёты и курсовые.</p>

    <!-- Поиск и фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Поиск по названию...">
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option selected>Все предметы</option>
                        <option>Физика</option>
                        <option>Математика</option>
                        <option>Программирование</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Найти</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Работы -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6>Лабораторная №4 по физике</h6>
                    <p class="text-muted small mb-2">Автор: Иван Петров • Группа: ИТ-321</p>
                    <span class="badge bg-success mb-2">Физика</span>
                    <span class="badge bg-info">Лабораторная</span>
                    <div class="mt-2">
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i>Скачать</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6>Курсовая по ООП</h6>
                    <p class="text-muted small mb-2">Автор: Мария Сидорова • Группа: ИТ-312</p>
                    <span class="badge bg-success mb-2">Программирование</span>
                    <span class="badge bg-info">Курсовая</span>
                    <div class="mt-2">
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i>Скачать</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6>Реферат по истории</h6>
                    <p class="text-muted small mb-2">Автор: Алексей Козлов • Группа: ЭК-214</p>
                    <span class="badge bg-warning mb-2">История</span>
                    <span class="badge bg-info">Реферат</span>
                    <div class="mt-2">
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i>Скачать</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6>Лабораторная по базам данных</h6>
                    <p class="text-muted small mb-2">Автор: Анна Сидорова • Группа: ИТ-321</p>
                    <span class="badge bg-success mb-2">Базы данных</span>
                    <span class="badge bg-info">Лабораторная</span>
                    <div class="mt-2">
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i>Скачать</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 p-3 bg-light rounded">
        <p class="mb-0"><i class="bi bi-info-circle-fill text-info"></i> Вы можете загрузить свою работу после проверки преподавателем.</p>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
