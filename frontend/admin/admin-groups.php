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
    <title>Группы — Админка — Учеба24</title>
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
        .group-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .group-card:hover { transform: translateY(-3px); }
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
        <a href="admin-groups.php" class="active"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="admin-library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
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

    <h2><i class="bi bi-people-fill"></i> Управление учебными группами</h2>
    <p class="text-muted">Создавайте и редактируйте учебные группы.</p>

    <!-- Форма создания группы -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Создать группу</h5>
        </div>
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Название группы *</label>
                    <input type="text" class="form-control" placeholder="ИТ-321" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Специальность *</label>
                    <input type="text" class="form-control" placeholder="Информационные технологии" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Курс</label>
                    <select class="form-select">
                        <option>1 курс</option>
                        <option>2 курс</option>
                        <option>3 курс</option>
                        <option>4 курс</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Количество студентов</label>
                    <input type="number" class="form-control" placeholder="25">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Заведующий кафедрой</label>
                    <input type="text" class="form-control" placeholder="ФИО">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i>Создать группу</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Список групп -->
    <h5 class="mb-3">Существующие группы</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="group-card">
                <div class="p-3 bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">ИТ-321</h5>
                        <span class="badge bg-light text-primary">24 студента</span>
                    </div>
                </div>
                <div class="p-3">
                    <p class="mb-2"><i class="bi bi-mortarboard me-1"></i><strong>Информационные технологии</strong></p>
                    <p class="text-muted small mb-0"><i class="bi bi-calendar me-1"></i>3 курс • Создана: 01.09.2024</p>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1"><i class="bi bi-eye me-1"></i>Просмотреть</button>
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Редактировать</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="group-card">
                <div class="p-3 bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">ИТ-312</h5>
                        <span class="badge bg-light text-success">22 студента</span>
                    </div>
                </div>
                <div class="p-3">
                    <p class="mb-2"><i class="bi bi-mortarboard me-1"></i><strong>Информационные технологии</strong></p>
                    <p class="text-muted small mb-0"><i class="bi bi-calendar me-1"></i>3 курс • Создана: 01.09.2024</p>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1"><i class="bi bi-eye me-1"></i>Просмотреть</button>
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Редактировать</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="group-card">
                <div class="p-3 bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">ЭК-214</h5>
                        <span class="badge bg-light text-info">20 студентов</span>
                    </div>
                </div>
                <div class="p-3">
                    <p class="mb-2"><i class="bi bi-mortarboard me-1"></i><strong>Электроника</strong></p>
                    <p class="text-muted small mb-0"><i class="bi bi-calendar me-1"></i>2 курс • Создана: 01.09.2024</p>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1"><i class="bi bi-eye me-1"></i>Просмотреть</button>
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Редактировать</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="group-card">
                <div class="p-3 bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">ПС-133</h5>
                        <span class="badge bg-dark text-warning">18 студентов</span>
                    </div>
                </div>
                <div class="p-3">
                    <p class="mb-2"><i class="bi bi-mortarboard me-1"></i><strong>Программное обеспечение</strong></p>
                    <p class="text-muted small mb-0"><i class="bi bi-calendar me-1"></i>1 курс • Создана: 01.09.2024</p>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1"><i class="bi bi-eye me-1"></i>Просмотреть</button>
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Редактировать</button>
                    </div>
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
