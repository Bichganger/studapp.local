<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../dashboard.php");
    exit;
}

$name = explode(' ', $_SESSION['full_name'])[0] ?? $_SESSION['full_name'];
$fullName = htmlspecialchars($_SESSION['full_name']);
$currentDate = date("j F Y");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Уведомления — Кабинет преподавателя — Учёба.Онлайн</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: sans-serif; }
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
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }
        .notification-item:hover {
            background: #f8f9fa;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-item.unread {
            background: #e7f3ff;
        }
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .icon-info { background: #e7f3ff; color: #0d6efd; }
        .icon-warning { background: #fff3cd; color: #ffc107; }
        .icon-success { background: #d4edda; color: #198754; }
        .icon-danger { background: #f8d7da; color: #dc3545; }
    </style>
</head>
<body>

<!-- Боковое меню -->
<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-mortarboard"></i> Учёба.Онлайн</h5>
        <p class="text-white-50 small">Преподаватель</p>
    </div>
    <nav>
        <a href="teacher.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="teacher-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="teacher-grades.php"><i class="bi bi-graph-up me-2"></i>����������� ������</a>`n        <a href="teacher-journal.php"><i class="bi bi-journal-text me-2"></i>������</a>`n        <a href="teacher-assignments.php"><i class="bi bi-journal-check me-2"></i>������ ���������</a>`n        <a href="teacher-groups.php""><i class="bi bi-people-fill me-2"></i>Мои группы</a>
        <a href="teacher-notifications.php" class="active"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<!-- Основной контент -->
<main class="main-content">
    <a href="teacher.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Назад
    </a>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-bell"></i> Уведомления</h2>
        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-check-all me-1"></i>Отметить все прочитанными</button>
    </div>

    <!-- Статистика уведомлений -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary">5</h3>
                    <p class="mb-0 text-muted">Новых</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">3</h3>
                    <p class="mb-0 text-muted">Важных</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">28</h3>
                    <p class="mb-0 text-muted">Прочитано</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">36</h3>
                    <p class="mb-0 text-muted">Всего</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active">Все</button>
                <button type="button" class="btn btn-outline-primary">Новые</button>
                <button type="button" class="btn btn-outline-primary">Важные</button>
                <button type="button" class="btn btn-outline-primary">Прочитанные</button>
            </div>
        </div>
    </div>

    <!-- Список уведомлений -->
    <div class="card">
        <div class="list-group list-group-flush">
            <!-- Новое уведомление -->
            <div class="notification-item unread">
                <div class="d-flex">
                    <div class="notification-icon icon-success me-3">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Студент сдал работу</h6>
                        <p class="mb-1 text-muted small">Иван Петров — Лабораторная №4 по физике</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>5 минут назад</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
            </div>

            <!-- Предупреждение -->
            <div class="notification-item unread">
                <div class="d-flex">
                    <div class="notification-icon icon-warning me-3">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Дедлайн завтра</h6>
                        <p class="mb-1 text-muted small">Группа ИТ-321 должна сдать курсовую работу по ООП</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>2 часа назад</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
            </div>

            <!-- Важное уведомление -->
            <div class="notification-item unread">
                <div class="d-flex">
                    <div class="notification-icon icon-danger me-3">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Замечание от администратора</h6>
                        <p class="mb-1 text-muted small">Обновите расписание на следующую неделю до пятницы</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>3 часа назад</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
            </div>

            <!-- Информационное -->
            <div class="notification-item">
                <div class="d-flex">
                    <div class="notification-icon icon-info me-3">
                        <i class="bi bi-info-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Новое объявление</h6>
                        <p class="mb-1 text-muted small">Проведение промежуточной аттестации запланировано на 25 марта</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>Вчера, 14:30</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye-slash"></i></button>
                    </div>
                </div>
            </div>

            <!-- Уведомление о посещаемости -->
            <div class="notification-item">
                <div class="d-flex">
                    <div class="notification-icon icon-warning me-3">
                        <i class="bi bi-graph-down"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Низкая посещаемость</h6>
                        <p class="mb-1 text-muted small">Студент Сидоров А. пропустил 5 занятий подряд</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>Вчера, 10:00</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye-slash"></i></button>
                    </div>
                </div>
            </div>

            <!-- Уведомление о сданной работе -->
            <div class="notification-item">
                <div class="d-flex">
                    <div class="notification-icon icon-success me-3">
                        <i class="bi bi-upload"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Работа загружена</h6>
                        <p class="mb-1 text-muted small">Группа ИТ-312 сдала отчёт по лабораторной работе №5</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>2 дня назад</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye-slash"></i></button>
                    </div>
                </div>
            </div>

            <!-- Уведомление о совещании -->
            <div class="notification-item">
                <div class="d-flex">
                    <div class="notification-icon icon-info me-3">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Педагогический совет</h6>
                        <p class="mb-1 text-muted small">Заседание педагогического совета состоится 22 марта в 15:00</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>3 дня назад</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye-slash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Пагинация -->
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item disabled"><a class="page-link" href="#">Назад</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Вперёд</a></li>
        </ul>
    </nav>
</main>

<!-- Подвал -->
<footer>&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



