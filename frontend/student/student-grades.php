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
    <title>Оценки — Кабинет студента — Учеба24</title>
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
        <a href="student-grades.php" class="active"><i class="bi bi-graph-up me-2"></i>Оценки</a>
        <a href="student-assignments.php"><i class="bi bi-journal-check me-2"></i>Мои работы</a>
        <a href="student-library.php"><i class="bi bi-book me-2"></i>Библиотека</a>
        <a href="student-notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <a href="student-dashboard.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Назад
    </a>

    <h2><i class="bi bi-graph-up"></i> Моя успеваемость</h2>
    <p class="text-muted">Группа ИТ-321</p>

    <!-- Статистика -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">4.5</h3>
                    <p class="mb-0 text-muted">Средний балл</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">95%</h3>
                    <p class="mb-0 text-muted">Посещаемость</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">15</h3>
                    <p class="mb-0 text-muted">Работ сдано</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">0</h3>
                    <p class="mb-0 text-muted">Долгов</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Оценки по предметам -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Оценки по предметам</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Предмет</th>
                            <th>Лекции</th>
                            <th>Практика</th>
                            <th>Лабораторные</th>
                            <th>Средний балл</th>
                            <th>Итоговая оценка</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Математический анализ</td>
                            <td>85</td>
                            <td>90</td>
                            <td>88</td>
                            <td><strong>87.7</strong></td>
                            <td><span class="badge bg-success">5</span></td>
                        </tr>
                        <tr>
                            <td>Программирование</td>
                            <td>92</td>
                            <td>95</td>
                            <td>90</td>
                            <td><strong>92.3</strong></td>
                            <td><span class="badge bg-success">5</span></td>
                        </tr>
                        <tr>
                            <td>Физика</td>
                            <td>78</td>
                            <td>82</td>
                            <td>80</td>
                            <td><strong>80.0</strong></td>
                            <td><span class="badge bg-info">4</span></td>
                        </tr>
                        <tr>
                            <td>Базы данных</td>
                            <td>88</td>
                            <td>92</td>
                            <td>95</td>
                            <td><strong>91.7</strong></td>
                            <td><span class="badge bg-success">5</span></td>
                        </tr>
                        <tr>
                            <td>ООП</td>
                            <td>90</td>
                            <td>88</td>
                            <td>92</td>
                            <td><strong>90.0</strong></td>
                            <td><span class="badge bg-success">5</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
