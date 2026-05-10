<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-family: sans-serif; }
        .hero { padding: 80px 0; text-align: center; color: white; }
        .hero h1 { font-size: 3rem; font-weight: 700; margin-bottom: 10px; }
        .hero-logo { font-size: 4rem; margin-bottom: 20px; }
        .features { padding: 60px 0; background: white; }
        .feature-icon { font-size: 2.5rem; color: #667eea; margin-bottom: 15px; }
        footer { text-align: center; padding: 30px 0; color: #e2e8f0; }
        .btn-custom { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; }
        .btn-custom:hover { color: white; opacity: 0.9; }
    </style>
</head>
<body>

<header class="hero">
    <div class="container">
        <div class="hero-logo"><i class="bi bi-journal-code"></i></div>
        <h1>Учеба24</h1>
        <p class="lead">Платформа для студентов и преподавателей</p>
        <div class="mt-4">
            <a href="auth.php" class="btn btn-light btn-lg me-2">Войти</a>
            <a href="register_new.php" class="btn btn-custom btn-lg">Регистрация</a>
        </div>
    </div>
</header>

<section class="features">
    <div class="container">
        <h2 class="text-center mb-5">Возможности</h2>
        <div class="row text-center">
            <div class="col-md-4">
                <div class="p-3">
                    <i class="bi bi-calendar-event feature-icon"></i>
                    <h5>Расписание</h5>
                    <p class="text-muted small">Пары и уведомления</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <i class="bi bi-graph-up feature-icon"></i>
                    <h5>Оценки</h5>
                    <p class="text-muted small">Контроль успеваемости</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <i class="bi bi-mortarboard feature-icon"></i>
                    <h5>Преподавателям</h5>
                    <p class="text-muted small">Журнал и работы</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <p class="mb-0"><strong>Учеба24</strong></p>
        <p class="text-muted small mb-0">&copy; 2026</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>