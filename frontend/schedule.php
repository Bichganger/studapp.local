<?php
session_start();
require_once 'protected/auth_guard.php';

$name = explode(' ', $_SESSION['full_name'])[0] ?? $_SESSION['full_name'];
$fullName = htmlspecialchars($_SESSION['full_name']);
$currentDate = date("j F Y");
$is_admin = $_SESSION['role'] === 'admin';
$is_teacher = $_SESSION['role'] === 'teacher';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание — Учёба.Онлайн</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Единые стили -->
    <style>
        body {
            background: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .navbar-brand { font-weight: 600; }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }
        .card:hover { transform: translateY(-2px); }
        .footer { margin-top: 60px; text-align: center; font-size: 0.9rem; color: #6c757d; padding: 20px 0; }
        .profile-img {
            width: 50px; height: 50px; border-radius: 50%;
            background: #0d6efd; color: white; display: flex;
            align-items: center; justify-content: center;
            font-size: 1.2rem; font-weight: bold;
        }
        .day-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .day-header {
            background: #f1f3f5;
            padding: 12px 16px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .lesson-item {
            padding: 14px 16px;
            border-bottom: 1px dashed #dee2e6;
        }
        .lesson-item:last-child {
            border-bottom: none;
        }
        .lesson-time {
            font-weight: 600;
            color: #495057;
        }
        .lesson-type {
            font-size: 0.8rem;
            padding: 2px 8px;
            border-radius: 12px;
            background: #e9ecef;
            color: #495057;
        }
        .badge-secondary {
            background: #6c757d;
        }
        .lecture { background: #d1ecf1; color: #0c5460; }
        .practice { background: #d4edda; color: #155724; }
        .lab { background: #fff3cd; color: #856404; }
        .seminar { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<!-- Навигация -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php"><i class="bi bi-journal-code"></i> Учёба.Онлайн</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house-door me-1"></i>Главная</a></li>
                <li class="nav-item"><a class="nav-link active" href="schedule.php"><i class="bi bi-calendar-event me-1"></i>Расписание</a></li>
                <li class="nav-item"><a class="nav-link" href="distance-learning.php"><i class="bi bi-laptop me-1"></i>Дистанционка</a></li>
                <li class="nav-item"><a class="nav-link" href="grades.php"><i class="bi bi-graph-up me-1"></i>Успеваемость</a></li>
                <?php if ($is_admin): ?>
                    <li class="nav-item"><a class="nav-link" href="admin-panel.php"><i class="bi bi-shield-lock me-1"></i>Админка</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav d-flex align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="profile-img me-2"><?= strtoupper(substr($name, 0, 1)) ?></div>
                        <span><?= $name ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Профиль</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Настройки</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Выход</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Основной контент -->
<div class="container mt-4">
    <!-- Кнопка "Назад" -->
    <a href="dashboard.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Назад
    </a>

    <!-- Заголовок -->
    <h2><i class="bi bi-calendar-event text-primary"></i> Расписание занятий</h2>
    <p class="text-muted">Группа ИТ-321 • Текущая неделя: 18–24 марта 2026</p>

    <!-- Фильтры и навигация -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <button class="btn btn-outline-primary btn-sm active">Текущая неделя</button>
        <button class="btn btn-outline-primary btn-sm">Следующая неделя</button>
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-filter me-1"></i>Фильтры
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Все предметы</a></li>
                <li><a class="dropdown-item" href="#">Только лекции</a></li>
                <li><a class="dropdown-item" href="#">Только практики</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Сегодня</a></li>
                <li><a class="dropdown-item" href="#">Завтра</a></li>
            </ul>
        </div>
        <?php if ($is_admin || $is_teacher): ?>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                <i class="bi bi-plus-circle me-1"></i>Добавить пару
            </button>
        <?php endif; ?>
    </div>

    <!-- Расписание по дням -->
    <div class="row g-4">
        <!-- Понедельник -->
        <div class="col-md-6 col-lg-4">
            <div class="day-card">
                <div class="day-header">
                    <div>
                        <h5 class="mb-0">Понедельник</h5>
                        <small>18 марта</small>
                    </div>
                    <span class="badge bg-secondary">5 пар</span>
                </div>
                <div class="day-body">
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">09:00 – 10:30</span>
                            <span class="lesson-type lecture">Лекция</span>
                        </div>
                        <h6 class="mb-1">Математический анализ</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Ауд. 201 • Лесная М.А.</p>
                    </div>
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">10:45 – 12:15</span>
                            <span class="lesson-type practice">Практика</span>
                        </div>
                        <h6 class="mb-1">Программирование</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Комп. класс 3 • Петров И.С.</p>
                    </div>
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">13:00 – 14:30</span>
                            <span class="lesson-type seminar">Семинар</span>
                        </div>
                        <h6 class="mb-1">Английский язык</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Ауд. 105 • Иванова О.П.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Вторник -->
        <div class="col-md-6 col-lg-4">
            <div class="day-card">
                <div class="day-header">
                    <div>
                        <h5 class="mb-0">Вторник</h5>
                        <small>19 марта</small>
                    </div>
                    <span class="badge bg-secondary">4 пары</span>
                </div>
                <div class="day-body">
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">09:00 – 10:30</span>
                            <span class="lesson-type lecture">Лекция</span>
                        </div>
                        <h6 class="mb-1">Физика</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Ауд. 301 • Сидоров А.В.</p>
                    </div>
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">10:45 – 12:15</span>
                            <span class="lesson-type lab">Лабораторная</span>
                        </div>
                        <h6 class="mb-1">Физика</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Лаб. 204 • Сидоров А.В.</p>
                    </div>
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">14:00 – 15:30</span>
                            <span class="lesson-type practice">Практика</span>
                        </div>
                        <h6 class="mb-1">Базы данных</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Комп. класс 2 • Козлов Д.М.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Среда -->
        <div class="col-md-6 col-lg-4">
            <div class="day-card">
                <div class="day-header">
                    <div>
                        <h5 class="mb-0">Среда</h5>
                        <small>20 марта</small>
                    </div>
                    <span class="badge bg-secondary">3 пары</span>
                </div>
                <div class="day-body">
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">09:00 – 10:30</span>
                            <span class="lesson-type lecture">Лекция</span>
                        </div>
                        <h6 class="mb-1">ООП</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Ауд. 205 • Морозов К.Л.</p>
                    </div>
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="lesson-time">11:00 – 12:30</span>
                            <span class="lesson-type practice">Практика</span>
                        </div>
                        <h6 class="mb-1">Web-разработка</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i> Комп. класс 4 • Григорьева Т.И.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно добавления пары -->
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Добавить пару</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form id="addLessonForm">
                    <div class="mb-3">
                        <label class="form-label">Предмет *</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">День *</label>
                            <select class="form-select" required>
                                <option>Понедельник</option>
                                <option>Вторник</option>
                                <option>Среда</option>
                                <option>Четверг</option>
                                <option>Пятница</option>
                                <option>Суббота</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Тип *</label>
                            <select class="form-select" required>
                                <option>Лекция</option>
                                <option>Практика</option>
                                <option>Лабораторная</option>
                                <option>Семинар</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Время начала *</label>
                            <input type="time" class="form-control" value="09:00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Аудитория *</label>
                            <input type="text" class="form-control" placeholder="Ауд. 201" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Преподаватель</label>
                        <input type="text" class="form-control" placeholder="ФИО">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary">Добавить</button>
            </div>
        </div>
    </div>
</div>

<!-- Подвал -->
<footer class="footer">&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>

<!-- Скрипты -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>