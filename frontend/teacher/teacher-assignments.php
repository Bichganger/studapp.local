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
    <title>Работы студентов — Кабинет преподавателя — Учеба24</title>
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
        .assignment-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
        .assignment-card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-reviewed { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<!-- Боковое меню -->
<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <p class="text-white-50 small">Преподаватель</p>
    </div>
    <nav>
        <a href="teacher.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="teacher-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="teacher-grades.php"><i class="bi bi-graph-up me-2"></i>Выставление оценок</a>
        <a href="teacher-assignments.php" class="active"><i class="bi bi-journal-check me-2"></i>Работы студентов</a>
        <a href="teacher-groups.php""><i class="bi bi-people-fill me-2"></i>Мои группы</a>
        <a href="teacher-notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<!-- Основной контент -->
<main class="main-content">
    <a href="teacher.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Назад
    </a>

    <h2><i class="bi bi-journal-check"></i> Сданные работы студентов</h2>
    <p class="text-muted">Проверка и оценка учебных работ</p>

    <!-- Статистика -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">12</h3>
                    <p class="mb-0 text-muted">Ожидают проверки</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">28</h3>
                    <p class="mb-0 text-muted">Проверено сегодня</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">5</h3>
                    <p class="mb-0 text-muted">Требуют доработки</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Группа</label>
                    <select class="form-select">
                        <option selected>Все группы</option>
                        <option>ИТ-321</option>
                        <option>ИТ-312</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Предмет</label>
                    <select class="form-select">
                        <option selected>Все предметы</option>
                        <option>Физика</option>
                        <option>Математический анализ</option>
                        <option>Программирование</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Статус</label>
                    <select class="form-select">
                        <option selected>Все статусы</option>
                        <option>Ожидает проверки</option>
                        <option>Проверено</option>
                        <option>Требует доработки</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary"><i class="bi bi-filter me-1"></i>Применить</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Список работ -->
    <h5 class="mb-3">Ожидают проверки</h5>

    <div class="assignment-card">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">Лабораторная работа №4 по физике</h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-person me-1"></i>Иванов Иван • 
                        <i class="bi bi-people me-1"></i>ИТ-321 • 
                        <i class="bi bi-calendar me-1"></i>Сдано: 17 марта 2026
                    </p>
                    <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-download me-1"></i>Скачать работу</a>
                </div>
                <div class="text-end">
                    <span class="badge status-pending mb-2">Ожидает проверки</span>
                    <div class="btn-group-vertical">
                        <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></button>
                        <button class="btn btn-sm btn-warning"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="assignment-card">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">Курсовая работа по алгоритмам</h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-person me-1"></i>Петров Петр • 
                        <i class="bi bi-people me-1"></i>ИТ-312 • 
                        <i class="bi bi-calendar me-1"></i>Сдано: 16 марта 2026
                    </p>
                    <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-download me-1"></i>Скачать работу</a>
                </div>
                <div class="text-end">
                    <span class="badge status-pending mb-2">Ожидает проверки</span>
                    <div class="btn-group-vertical">
                        <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></button>
                        <button class="btn btn-sm btn-warning"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3 mt-4">Проверено сегодня</h5>

    <div class="assignment-card">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">Лабораторная работа №3 по математике</h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-person me-1"></i>Сидорова Анна • 
                        <i class="bi bi-people me-1"></i>ИТ-321 • 
                        <i class="bi bi-calendar me-1"></i>Сдано: 15 марта 2026
                    </p>
                    <span class="badge bg-success">Оценка: 5 (отлично)</span>
                    <span class="text-muted small ms-2">Комментарий: Отличная работа!</span>
                </div>
                <div class="text-end">
                    <span class="badge status-reviewed mb-2">Проверено</span>
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




