<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: ../dashboard.php");
    exit;
}

$fullName = htmlspecialchars($_SESSION['full_name']);
$username = htmlspecialchars($_SESSION['username']);
$currentDate = date("j F Y");

// Умное приветствие по времени
$hour = (int)date('H');
if ($hour >= 5 && $hour < 12) {
    $timeGreeting = 'Доброе утро';
} elseif ($hour >= 12 && $hour < 18) {
    $timeGreeting = 'Добрый день';
} elseif ($hour >= 18 && $hour < 23) {
    $timeGreeting = 'Добрый вечер';
} else {
    $timeGreeting = 'Доброй ночи';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабинет студента — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/neural-network.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .sidebar {
            min-height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            color: white;
            position: fixed;
            width: 260px;
            left: 0;
            top: 0;
            padding: 20px 0;
            border-right: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 5px 0 30px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }
        .sidebar a {
            color: #6c757d;
            margin: 8px 15px;
            border-radius: 12px;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }
        .sidebar a:hover, .sidebar a.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .sidebar a.text-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545 !important;
        }
        .sidebar a.text-danger:hover {
            background: rgba(220, 53, 69, 0.2);
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
            position: relative;
            z-index: 1;
        }
        footer { 
            margin-left: 260px; 
            padding: 20px; 
            text-align: center; 
            font-size: 0.9rem; 
            color: #6c757d;
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="role-student">
    <!-- Живой фон -->
    <div class="neural-bg"></div>
    <canvas id="neuralNetworkCanvas"></canvas>

<!-- Боковое меню -->
<div class="sidebar">
    <div class="text-center mb-4">
        <div style="font-size: 3rem; margin-bottom: 10px;">
            <i class="bi bi-mortarboard" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
        </div>
        <h5 class="text-white mb-1">Учеба24</h5>
        <p class="text-white-50 small mb-0">Студент</p>
    </div>
    <nav>
        <a href="student-dashboard.php" class="active"><i class="bi bi-house-door me-2"></i>Главная</a>
        <a href="student-schedule.php"><i class="bi bi-calendar-event me-2"></i>Расписание</a>
        <a href="student-grades.php"><i class="bi bi-graph-up me-2"></i>Оценки</a>
        <a href="student-assignments.php"><i class="bi bi-journal-check me-2"></i>Мои работы</a>
        <a href="student-library.php"><i class="bi bi-book me-2"></i>Библиотека</a>
        <a href="student-notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<!-- Основной контент -->
<main class="main-content">
    <!-- Приветствие -->
    <div class="neural-fade-in mb-4">
        <h2 class="neural-greeting" style="font-size: 2.5rem;"><?= $timeGreeting ?>, <?= $fullName ?>! 🎓</h2>
        <p class="text-muted fs-5">Добро пожаловать в личный кабинет</p>
        <small class="text-muted"><i class="bi bi-calendar me-1"></i><?= $currentDate ?></small>
    </div>

    <!-- Быстрые действия -->
    <h5 class="mt-4 mb-3 text-dark">Быстрый доступ</h5>
    <div class="row g-3">
        <div class="col-md-4 neural-fade-in neural-stagger-1">
            <a href="student-schedule.php" class="text-decoration-none">
                <div class="neural-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="neural-icon mb-3"><i class="bi bi-calendar-event"></i></div>
                        <h5 class="mt-2">Расписание</h5>
                        <p class="text-muted small mb-0">Посмотреть пары на неделю</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 neural-fade-in neural-stagger-2">
            <a href="student-grades.php" class="text-decoration-none">
                <div class="neural-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="neural-icon mb-3"><i class="bi bi-graph-up"></i></div>
                        <h5 class="mt-2">Оценки</h5>
                        <p class="text-muted small mb-0">Просмотр успеваемости</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 neural-fade-in neural-stagger-3">
            <a href="student-assignments.php" class="text-decoration-none">
                <div class="neural-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="neural-icon mb-3"><i class="bi bi-journal-check"></i></div>
                        <h5 class="mt-2">Мои работы</h5>
                        <p class="text-muted small mb-0">Сданные работы и статусы</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 neural-fade-in neural-stagger-4">
            <a href="student-library.php" class="text-decoration-none">
                <div class="neural-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="neural-icon mb-3"><i class="bi bi-book"></i></div>
                        <h5 class="mt-2">Библиотека</h5>
                        <p class="text-muted small mb-0">Учебные материалы</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 neural-fade-in neural-stagger-1">
            <a href="student-notifications.php" class="text-decoration-none">
                <div class="neural-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="neural-icon mb-3"><i class="bi bi-bell"></i></div>
                        <h5 class="mt-2">Уведомления</h5>
                        <p class="text-muted small mb-0">Просмотр уведомлений</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 neural-fade-in neural-stagger-2">
            <a href="../profile.php" class="text-decoration-none">
                <div class="neural-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="neural-icon mb-3"><i class="bi bi-person-circle"></i></div>
                        <h5 class="mt-2">Профиль</h5>
                        <p class="text-muted small mb-0">Настройки аккаунта</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Статистика -->
    <h5 class="mt-5 mb-3 text-dark">Моя статистика</h5>
    <div class="row g-3">
        <div class="col-md-3 neural-fade-in neural-stagger-1">
            <div class="neural-stat-card">
                <div class="neural-stat-value">4.5</div>
                <div class="neural-stat-label">Средний балл</div>
            </div>
        </div>
        <div class="col-md-3 neural-fade-in neural-stagger-2">
            <div class="neural-stat-card">
                <div class="neural-stat-value">95%</div>
                <div class="neural-stat-label">Посещаемость</div>
            </div>
        </div>
        <div class="col-md-3 neural-fade-in neural-stagger-3">
            <div class="neural-stat-card">
                <div class="neural-stat-value">12</div>
                <div class="neural-stat-label">Работ сдано</div>
            </div>
        </div>
        <div class="col-md-3 neural-fade-in neural-stagger-4">
            <div class="neural-stat-card">
                <div class="neural-stat-value">2</div>
                <div class="neural-stat-label">Новых уведомлений</div>
            </div>
        </div>
    </div>
</main>

<!-- Подвал -->
<footer>&copy; 2026 Учеба24 | Платформа образования будущего</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/neural-network.js"></script>
</body>
</html>
