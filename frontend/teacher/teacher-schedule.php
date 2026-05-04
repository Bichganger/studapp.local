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
    <title>Расписание — Кабинет преподавателя — Учёба.Онлайн</title>
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
        .day-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .day-header {
            background: #f1f3f5;
            padding: 12px 16px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .lesson-item {
            padding: 14px 16px;
            border-bottom: 1px dashed #dee2e6;
        }
        .lesson-item:last-child {
            border-bottom: none;
        }
        .lesson-time {
            font-weight: 600;
            color: #495057;
        }
        .lesson-type {
            font-size: 0.8rem;
            padding: 2px 8px;
            border-radius: 12px;
            background: #e9ecef;
            color: #495057;
        }
        .lecture { background: #d1ecf1; color: #0c5460; }
        .practice { background: #d4edda; color: #155724; }
        .lab { background: #fff3cd; color: #856404; }
        .seminar { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<!-- Боковое меню -->
<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-mortarboard"></i> Учёба.Онлайн</h5>
        <p class="text-white-50 small">Преподаватель</p>
    </div>
    <nav>
        <a href="teacher.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="teacher-schedule.php" class="active"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="teacher-journal.php"><i class="bi bi-journal-text me-2"></i>Журнал</a>
        <a href="teacher-grades.php"><i class="bi bi-graph-up me-2"></i>����������� ������</a>`n        <a href="teacher-journal.php"><i class="bi bi-journal-text me-2"></i>������</a>`n        <a href="teacher-assignments.php"><i class="bi bi-journal-check me-2"></i>������ ���������</a>`n        <a href="teacher-groups.php"><i class="bi bi-people-fill me-2"></i>Мои группы</a>
        <a href="teacher-notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<!-- Основной контент -->
<main class="main-content">
    <a href="teacher.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Назад
    </a>

    <h2><i class="bi bi-calendar"></i> Моё расписание</h2>
    <p class="text-muted">Текущая неделя: 18–24 марта 2026</p>

    <div class="row g-4">
        <!-- Понедельник -->
        <div class="col-md-6 col-lg-4">
            <div class="day-card">
                <div class="day-header">
                    <div>
                        <h5 class="mb-0">Понедельник</h5>
                        <small>18 марта</small>
                    </div>
                    <span class="badge bg-secondary">3 пары</span>
                </div>
                <div class="day-body">
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">09:00 – 10:30</span>
                            <span class="lesson-type lecture">Лекция</span>
                        </div>
                        <h6 class="mb-1">Математический анализ</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Ауд. 201</p>
                    </div>
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">10:45 – 12:15</span>
                            <span class="lesson-type practice">Практика</span>
                        </div>
                        <h6 class="mb-1">Программирование</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Комп. класс 3</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Вторник -->
        <div class="col-md-6 col-lg-4">
            <div class="day-card">
                <div class="day-header">
                    <div>
                        <h5 class="mb-0">Вторник</h5>
                        <small>19 марта</small>
                    </div>
                    <span class="badge bg-secondary">2 пары</span>
                </div>
                <div class="day-body">
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">10:45 – 12:15</span>
                            <span class="lesson-type lab">Лабораторная</span>
                        </div>
                        <h6 class="mb-1">Физика</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Лаб. 204</p>
                    </div>
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">14:00 – 15:30</span>
                            <span class="lesson-type practice">Практика</span>
                        </div>
                        <h6 class="mb-1">Базы данных</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Комп. класс 2</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Среда -->
        <div class="col-md-6 col-lg-4">
            <div class="day-card">
                <div class="day-header">
                    <div>
                        <h5 class="mb-0">Среда</h5>
                        <small>20 марта</small>
                    </div>
                    <span class="badge bg-secondary">2 пары</span>
                </div>
                <div class="day-body">
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">09:00 – 10:30</span>
                            <span class="lesson-type lecture">Лекция</span>
                        </div>
                        <h6 class="mb-1">ООП</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Ауд. 205</p>
                    </div>
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">11:00 – 12:30</span>
                            <span class="lesson-type practice">Практика</span>
                        </div>
                        <h6 class="mb-1">Web-разработка</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Комп. класс 4</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Подвал -->
<footer>&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



