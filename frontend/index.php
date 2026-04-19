<?php
// Если пользователь уже вошёл — перенаправляем в кабинет
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudApp — Умная система для студентов</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Стили -->
    <style>
        body {
            background: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .hero {
            padding: 100px 20px;
            text-align: center;
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            color: white;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
        }
        .hero .lead {
            font-size: 1.25rem;
            opacity: 0.9;
        }
        .btn-lg {
            padding: 12px 30px;
            font-size: 1.2rem;
            font-weight: 500;
        }
        .features {
            padding: 60px 0;
        }
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        footer {
            padding: 40px 0;
            font-size: 0.9rem;
            color: #6c757d;
        }
        .heart {
            color: #e74c3c;
        }
    </style>
</head>
<body>

<!-- Герой -->
<section class="hero">
    <div class="container">
        <h1><i class="bi bi-journal-bookmark-fill me-2"></i> StudApp</h1>
        <p class="lead">Единая платформа для студентов, преподавателей и администраторов</p>
        <div class="mt-4">
            <a href="login.php" class="btn btn-light btn-lg me-3 shadow-sm">Войти</a>
            <a href="register.php" class="btn btn-outline-light btn-lg shadow-sm">Зарегистрироваться</a>
        </div>
    </div>
</section>

<!-- Преимущества -->
<section class="container features">
    <div class="row text-center g-4">
        <div class="col-md-4">
            <div class="text-primary feature-icon">
                <i class="bi bi-calendar-event"></i>
            </div>
            <h5>Расписание</h5>
            <p class="text-muted">Всегда в курсе своих пар и экзаменов</p>
        </div>
        <div class="col-md-4">
            <div class="text-success feature-icon">
                <i class="bi bi-graph-up"></i>
            </div>
            <h5>Успеваемость</h5>
            <p class="text-muted">Следите за своими оценками в реальном времени</p>
        </div>
        <div class="col-md-4">
            <div class="text-warning feature-icon">
                <i class="bi bi-shop"></i>
            </div>
            <h5>Библиотека работ</h5>
            <p class="text-muted">Находите и делитесь учебными материалами</p>
        </div>
    </div>
</section>

<!-- Подвал -->
<footer class="text-center">
    <p>&copy; 2024 StudApp. Разработано с <i class="bi bi-heart-fill heart"></i></p>
</footer>

</body>
</html>