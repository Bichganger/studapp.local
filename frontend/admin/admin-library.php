<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'admin') {
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
    <title>Библиотека — Админка — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            margin: 5px 10px;
            border-radius: 5px;
            display: block;
            padding: 8px 15px;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #34495e;
            color: white;
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        footer { margin-left: 260px; padding: 20px; text-align: center; font-size: 0.9rem; color: #6c757d; }
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .work-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
        .work-card:hover { transform: translateY(-2px); transition: transform 0.2s; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<!-- Боковое меню -->
<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-shield-lock"></i> Админка</h5>
        <p class="text-white-50 small">Доступ: полный</p>
    </div>
    <nav>
        <a href="admin-dashboard.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="admin-users.php"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="admin-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="admin-groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="admin-library.php" class="active"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="admin-notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="admin-settings.php"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<!-- Основной контент -->
<main class="main-content">
    <a href="admin-dashboard.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Назад
    </a>

    <h2><i class="bi bi-journal"></i> Модерация библиотеки работ</h2>
    <p class="text-muted">Проверяйте и одобряйте загруженные студентами материалы.</p>

    <!-- Статистика -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">8</h3>
                    <p class="mb-0 text-muted">На проверке</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">156</h3>
                    <p class="mb-0 text-muted">Одобрено</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-danger">12</h3>
                    <p class="mb-0 text-muted">Отклонено</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Предмет</label>
                    <select class="form-select">
                        <option selected>Все предметы</option>
                        <option>Физика</option>
                        <option>Математика</option>
                        <option>Программирование</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Тип работы</label>
                    <select class="form-select">
                        <option selected>Все типы</option>
                        <option>Лабораторная</option>
                        <option>Курсовая</option>
                        <option>Реферат</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Статус</label>
                    <select class="form-select">
                        <option selected>Все статусы</option>
                        <option>Ожидает проверки</option>
                        <option>Одобрено</option>
                        <option>Отклонено</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary"><i class="bi bi-filter me-1"></i>Применить</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Список работ -->
    <h5 class="mb-3">Ожидают модерации</h5>

    <div class="work-card">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">Лабораторная работа №5 по физике</h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-person me-1"></i>Иванов Иван • 
                        <i class="bi bi-people me-1"></i>ИТ-321 • 
                        <i class="bi bi-calendar me-1"></i>Загружено: 17 марта 2026
                    </p>
                    <span class="badge bg-secondary">Лабораторная</span>
                    <a href="#" class="btn btn-sm btn-outline-primary ms-2"><i class="bi bi-download me-1"></i>Скачать</a>
                </div>
                <div class="text-end">
                    <span class="badge status-pending mb-2">На проверке</span>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Одобрить</button>
                        <button class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i> Отклонить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="work-card">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">Курсовая работа по базам данных</h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-person me-1"></i>Петрова Мария • 
                        <i class="bi bi-people me-1"></i>ИТ-312 • 
                        <i class="bi bi-calendar me-1"></i>Загружено: 16 марта 2026
                    </p>
                    <span class="badge bg-info">Курсовая</span>
                    <a href="#" class="btn btn-sm btn-outline-primary ms-2"><i class="bi bi-download me-1"></i>Скачать</a>
                </div>
                <div class="text-end">
                    <span class="badge status-pending mb-2">На проверке</span>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Одобрить</button>
                        <button class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i> Отклонить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3 mt-4">Недавно одобренные</h5>

    <div class="work-card">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">Реферат по истории</h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-person me-1"></i>Сидоров Алексей • 
                        <i class="bi bi-people me-1"></i>ЭК-214 • 
                        <i class="bi bi-calendar me-1"></i>Загружено: 15 марта 2026
                    </p>
                    <span class="badge bg-warning">Реферат</span>
                    <span class="text-muted small ms-2">Одобрено: 16 марта 2026</span>
                </div>
                <div class="text-end">
                    <span class="badge status-approved mb-2">Одобрено</span>
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></button>
                </div>
            </div>
        </div>
    </div>

</main>

<!-- Подвал -->
<footer>&copy; 2026 Учеба24</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
