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
    <title>Расписание — Админка — Учеба24</title>
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
        <a href="admin-schedule.php" class="active"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="admin-groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
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

    <h2><i class="bi bi-calendar"></i> Управление расписанием</h2>
    <p class="text-muted">Добавляйте и редактируйте пары, выбрав день и группу.</p>

    <!-- Форма добавления пары -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Добавить пару</h5>
        </div>
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">День *</label>
                    <select class="form-select" required>
                        <option value="">Выберите...</option>
                        <option>Понедельник</option>
                        <option>Вторник</option>
                        <option>Среда</option>
                        <option>Четверг</option>
                        <option>Пятница</option>
                        <option>Суббота</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Группа *</label>
                    <select class="form-select" required>
                        <option value="">Выберите...</option>
                        <option>ИТ-321</option>
                        <option>ИТ-312</option>
                        <option>ЭК-214</option>
                        <option>ПС-133</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Предмет *</label>
                    <input type="text" class="form-control" required placeholder="Название предмета">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Время начала *</label>
                    <input type="time" class="form-control" value="09:00" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Тип *</label>
                    <select class="form-select" required>
                        <option>Лекция</option>
                        <option>Практика</option>
                        <option>Лабораторная</option>
                        <option>Семинар</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Аудитория</label>
                    <input type="text" class="form-control" placeholder="Ауд. 201">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Преподаватель</label>
                    <input type="text" class="form-control" placeholder="ФИО преподавателя">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Добавить пару</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Текущее расписание -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Текущее расписание</h5>
            <button class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i>Экспорт</button>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-1"></i> Здесь будет таблица с полным расписанием занятий.
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>День</th>
                            <th>Время</th>
                            <th>Группа</th>
                            <th>Предмет</th>
                            <th>Тип</th>
                            <th>Аудитория</th>
                            <th>Преподаватель</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Понедельник</td>
                            <td>09:00 - 10:30</td>
                            <td>ИТ-321</td>
                            <td>Математический анализ</td>
                            <td><span class="badge bg-info">Лекция</span></td>
                            <td>Ауд. 201</td>
                            <td>Лесная М.А.</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>Понедельник</td>
                            <td>10:45 - 12:15</td>
                            <td>ИТ-321</td>
                            <td>Программирование</td>
                            <td><span class="badge bg-success">Практика</span></td>
                            <td>Комп. класс 3</td>
                            <td>Петров И.С.</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Подвал -->
<footer>&copy; 2026 Учеба24</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
