<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

$name = htmlspecialchars($_SESSION['full_name']);
$fullName = $name;
$currentDate = date("j F Y");

require_once '../config/db.php';

// Статистика
try {
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $studentCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
    $teacherCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'teacher'")->fetchColumn();
} catch (Exception $e) {
    $userCount = $studentCount = $teacherCount = 0;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/neural-network.css">
    <style>
        body { background: #f8f9fa; font-family: sans-serif; }
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
            position: fixed;
            width: 260px;
            left: 0;
            top: 0;
            padding: 20px 0;
        }
        .sidebar-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 20px;
            margin-bottom: 20px;
        }
        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            margin: 5px 10px;
            border-radius: 5px;
            display: block;
            padding: 8px 15px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #34495e;
            color: white;
            transform: translateX(5px);
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        .top-bar {
            margin-left: 260px;
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        footer { margin-left: 260px; padding: 20px; text-align: center; font-size: 0.9rem; color: #6c757d; }
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        /* Цвета для админки */
        .role-admin .neural-bg {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
    </style>
</head>
<body class="role-admin">
    <!-- Живой фон (опционально) -->
    <div class="neural-bg"></div>
    <canvas id="neuralNetworkCanvas"></canvas>

<!-- Боковое меню -->
<div class="sidebar">
    <div class="sidebar-header text-center">
        <h4 class="mb-0"><i class="bi bi-journal-code"></i> Учеба24</h4>
        <p class="text-white-50 small mb-0">Админ-панель</p>
    </div>
    <nav>
        <a href="admin-dashboard.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="admin-users.php"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="admin-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="admin-groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="admin-library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="admin-notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="admin-settings.php"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<!-- Верхняя панель -->
<div class="top-bar neural-fade-in">
    <div>
        <h4 class="mb-0"><i class="bi bi-shield-lock text-primary me-2"></i>Панель администратора</h4>
        <p class="mb-0 text-muted mt-1">Добро пожаловать, <strong><?= $name ?></strong>!</p>
    </div>
    <div class="text-end">
        <span class="badge bg-primary"><?= $currentDate ?></span>
    </div>
</div>

<!-- Основной контент -->
<main class="main-content">
    <!-- Статистика -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6><i class="bi bi-people"></i> Всего пользователей</h6>
                    <p class="display-6 mb-0"><?= $userCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6><i class="bi bi-mortarboard"></i> Студенты</h6>
                    <p class="display-6 mb-0"><?= $studentCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6><i class="bi bi-person-badge"></i> Преподаватели</h6>
                    <p class="display-6 mb-0"><?= $teacherCount ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Быстрый доступ -->
    <h5 class="mb-3">Быстрый доступ</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="admin-users.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-4 text-primary"></i>
                        <h5 class="mt-2">Пользователи</h5>
                        <p class="text-muted small mb-0">Управление аккаунтами</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="admin-schedule.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar display-4 text-success"></i>
                        <h5 class="mt-2">Расписание</h5>
                        <p class="text-muted small mb-0">Редактирование расписания</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="admin-groups.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill display-4 text-warning"></i>
                        <h5 class="mt-2">Группы</h5>
                        <p class="text-muted small mb-0">Управление учебными группами</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="admin-library.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-journal display-4 text-info"></i>
                        <h5 class="mt-2">Библиотека</h5>
                        <p class="text-muted small mb-0">Модерация работ</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="admin-notifications.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-bell display-4 text-danger"></i>
                        <h5 class="mt-2">Рассылка</h5>
                        <p class="text-muted small mb-0">Отправка уведомлений</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="admin-settings.php" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-gear display-4 text-secondary"></i>
                        <h5 class="mt-2">Настройки</h5>
                        <p class="text-muted small mb-0">Конфигурация системы</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</main>

<!-- Подвал -->
<footer>
    <div class="container">
        <p class="mb-0">&copy; 2026 <strong>Учеба24</strong> | Платформа образования будущего</p>
        <small class="text-muted">Админ-панель v1.0</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/neural-network.js"></script>
</body>
</html>
