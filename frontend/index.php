<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Учёба.Онлайн — современная образовательная платформа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: sans-serif; }
        .hero { padding: 80px 0; text-align: center; }
        .features { padding: 60px 0; background: white; }
        .feature-icon { font-size: 2.5rem; color: #0d6efd; margin-bottom: 16px; }
        .btn-custom { background: #0d6efd; color: white; padding: 12px 30px; font-size: 1.1rem; }
        footer { text-align: center; padding: 40px 0; font-size: 0.9rem; color: #6c757d; }
    </style>
</head>
<body>

<!-- Шапка -->
<header class="bg-primary text-white">
    <div class="container hero">
        <h1><i class="bi bi-journal-code"></i> Учёба.Онлайн</h1>
        <p class="lead">Современная платформа для студентов и преподавателей</p>
        <div class="mt-4">
            <a href="login.php" class="btn btn-light btn-lg me-3">Войти</a>
            <a href="register.php" class="btn btn-custom btn-lg">Регистрация</a>
        </div>
    </div>
</header>

<!-- Возможности -->
<section class="features">
    <div class="container">
        <h2 class="text-center mb-5">Возможности платформы</h2>
        <div class="row text-center">
            <div class="col-md-4">
                <i class="bi bi-calendar-event feature-icon"></i>
                <h4>Расписание</h4>
                <p>Удобное расписание пар с уведомлениями и напоминаниями.</p>
            </div>
            <div class="col-md-4">
                <i class="bi bi-graph-up feature-icon"></i>
                <h4>Успеваемость</h4>
                <p>Контроль оценок и посещаемости в реальном времени.</p>
            </div>
            <div class="col-md-4">
                <i class="bi bi-mortarboard feature-icon"></i>
                <h4>Для преподавателей</h4>
                <p>Выставление оценок, проверка работ, рассылки.</p>
            </div>
        </div>
    </div>
</section>

<!-- Подвал -->
<footer>
    &copy; 2026 Учёба.Онлайн. Образование будущего.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>