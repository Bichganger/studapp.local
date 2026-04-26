<?php
session_start();
require_once 'protected/auth_guard.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../dashboard.php");
    exit;
}

$currentDate = date("j F Y");
$name = explode(' ', $_SESSION['full_name'])[0] ?? $_SESSION['full_name'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Преподаватель — StudApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
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
    </style>
</head>
<body>

<!-- Боковое меню -->
<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-mortarboard"></i> Кабинет преподавателя</h5>
    </div>
    <nav>
        <a href="#" class="active" data-bs-toggle="tab" data-bs-target="#schedule"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="#" data-bs-toggle="tab" data-bs-target="#grades"><i class="bi bi-graph-up me-2"></i>Выставление оценок</a>
        <a href="#" data-bs-toggle="tab" data-bs-target="#assignments"><i class="bi bi-journal-check me-2"></i>Работы студентов</a>
        <a href="#" data-bs-toggle="tab" data-bs-target="#groups"><i class="bi bi-people-fill me-2"></i>Мои группы</a>
        <a href="#" data-bs-toggle="tab" data-bs-target="#notifications"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-3">
        <a href="../frontend/dashboard.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Назад</a>
    </nav>
</div>

<!-- Основной контент -->
<main class="main-content">
    <div class="tab-content">

        <!-- Расписание -->
        <div class="tab-pane fade show active" id="schedule">
            <h2><i class="bi bi-calendar"></i> Моё расписание</h2>
            <div class="alert alert-light">Здесь будет ваше расписание.</div>
        </div>

        <!-- Оценки -->
        <div class="tab-pane fade" id="grades">
            <h2><i class="bi bi-graph-up"></i> Выставление оценок</h2>
            <div class="alert alert-light">Выберите группу и предмет для выставления оценок.</div>
        </div>

        <!-- Работы -->
        <div class="tab-pane fade" id="assignments">
            <h2><i class="bi bi-journal-check"></i> Сданные работы</h2>
            <div class="alert alert-info">✅ Группа ИТ-321 сдала лабораторную работу по физике</div>
            <div class="alert alert-warning">⏳ Ожидает проверки: 12 работ по математике</div>
        </div>

        <!-- Группы -->
        <div class="tab-pane fade" id="groups">
            <h2><i class="bi bi-people-fill"></i> Мои учебные группы</h2>
            <ul class="list-group">
                <li class="list-group-item">ИТ-321 • Программирование (практика)</li>
                <li class="list-group-item">ИТ-312 • Алгоритмы и структуры данных</li>
            </ul>
        </div>

        <!-- Уведомления -->
        <div class="tab-pane fade" id="notifications">
            <h2><i class="bi bi-bell"></i> Уведомления</h2>
            <div class="list-group">
                <div class="list-group-item">
                    <strong>Новая сданная работа</strong>
                    <div class="text-muted small">Иван Петров — Лабораторная №4 по физике</div>
                </div>
                <div class="list-group-item">
                    <strong>Дедлайн завтра</strong>
                    <div class="text-muted small">Группа ИТ-321 должна сдать курсовую</div>
                </div>
                <div class="list-group-item">
                    <strong>Замечание от администратора</strong>
                    <div class="text-muted small">Обновите расписание до пятницы</div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Подвал -->
<footer>
    &copy; 2024 StudApp. Для преподавателей.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>