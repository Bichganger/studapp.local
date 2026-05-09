<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Учеба24 — современная образовательная платформа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/neural-network.css">
    <style>
        body { 
            background: #f8f9fa; 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
        }
        .hero { 
            padding: 100px 0; 
            text-align: center;
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            color: white;
        }
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
        }
        .hero-logo {
            font-size: 5rem;
            margin-bottom: 20px;
            animation: iconBounce 2s ease-in-out infinite;
        }
        .features { padding: 80px 0; background: white; }
        .feature-icon { 
            font-size: 3rem; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 16px; 
        }
        .btn-custom { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 14px 40px; 
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }
        .btn-light {
            border-radius: 50px;
            padding: 14px 40px;
            font-weight: 600;
        }
        footer { 
            text-align: center; 
            padding: 40px 0; 
            font-size: 0.9rem; 
            color: #6c757d;
            background: #f8f9fa;
        }
        @keyframes iconBounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    </style>
</head>
<body>

<!-- Шапка -->
<header class="hero">
    <div class="container">
        <div class="hero-logo">
            <i class="bi bi-journal-code"></i>
        </div>
        <h1>Учеба24</h1>
        <p class="lead fs-4">Современная платформа для студентов и преподавателей</p>
        <div class="mt-5">
            <a href="login_new.php" class="btn btn-light btn-lg me-3">Войти</a>
            <a href="register_new.php" class="btn btn-custom btn-lg">Регистрация</a>
        </div>
    </div>
</header>

<!-- Возможности -->
<section class="features">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">Возможности платформы</h2>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="p-4 h-100">
                    <i class="bi bi-calendar-event feature-icon"></i>
                    <h4>Расписание</h4>
                    <p class="text-muted">Удобное расписание пар с уведомлениями и напоминаниями.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-4 h-100">
                    <i class="bi bi-graph-up feature-icon"></i>
                    <h4>Успеваемость</h4>
                    <p class="text-muted">Контроль оценок и посещаемости в реальном времени.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-4 h-100">
                    <i class="bi bi-mortarboard feature-icon"></i>
                    <h4>Для преподавателей</h4>
                    <p class="text-muted">Выставление оценок, проверка работ, рассылки.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Подвал -->
<footer>
    <div class="container">
        <p class="mb-2"><strong>Учеба24</strong> — Платформа образования будущего</p>
        <p class="text-muted mb-0">&copy; 2026 Все права защищены</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>