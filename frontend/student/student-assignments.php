<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'student') {
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
    <title>Мои работы — Кабинет студента — Учёба.Онлайн</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: sans-serif; }
        .sidebar {
            min-height: 100vh;
            background: #0d6efd;
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
            background: #0b5ed7;
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

<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-mortarboard"></i> Учёба.Онлайн</h5>
        <p class="text-white-50 small">Студент</p>
    </div>
    <nav>
        <a href="student-dashboard.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="student-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="student-grades.php"><i class="bi bi-graph-up me-2"></i>Оценки</a>
        <a href="student-assignments.php" class="active"><i class="bi bi-journal-check me-2"></i>Мои работы</a>
        <a href="student-library.php"><i class="bi bi-book me-2"></i>Библиотека</a>
        <a href="student-notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <a href="student-dashboard.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Назад
    </a>

    <h2><i class="bi bi-journal-check"></i> Мои сданные работы</h2>
    <p class="text-muted">Отслеживайте статусы своих работ</p>

    <!-- Статистика -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">2</h3>
                    <p class="mb-0 text-muted">На проверке</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">12</h3>
                    <p class="mb-0 text-muted">Одобрено</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-danger">1</h3>
                    <p class="mb-0 text-muted">Требует доработки</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Загрузить работу -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Загрузить работу</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="mb-3">
                    <label class="form-label">Предмет *</label>
                    <select class="form-select" required>
                        <option value="">Выберите предмет...</option>
                        <option>Физика</option>
                        <option>Математический анализ</option>
                        <option>Программирование</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Тип работы *</label>
                    <select class="form-select" required>
                        <option value="">Выберите тип...</option>
                        <option>Лабораторная</option>
                        <option>Курсовая</option>
                        <option>Реферат</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Файл *</label>
                    <input type="file" class="form-control" required>
                    <small class="text-muted">Поддерживаемые форматы: PDF, DOC, DOCX, ZIP (макс. 10 МБ)</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Комментарий</label>
                    <textarea class="form-control" rows="3" placeholder="Дополнительная информация..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Загрузить</button>
            </form>
        </div>
    </div>

    <!-- Список работ -->
    <h5 class="mb-3">История работ</h5>

    <div class="work-card">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">Лабораторная работа №4 по физике</h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-calendar me-1"></i>Загружено: 17 марта 2026
                    </p>
                    <span class="badge status-pending">На проверке</span>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="work-card">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">Курсовая работа по ООП</h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-calendar me-1"></i>Загружено: 15 марта 2026 • 
                        <i class="bi bi-check-circle me-1"></i>Проверено: 16 марта 2026
                    </p>
                    <span class="badge status-approved">Одобрено</span>
                    <span class="badge bg-success ms-2">Оценка: 5</span>
                    <p class="text-muted small mt-1"><i class="bi bi-chat-left-text me-1"></i>Отличная работа!</p>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="work-card">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">Лабораторная работа №3 по математике</h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-calendar me-1"></i>Загружено: 10 марта 2026 • 
                        <i class="bi bi-check-circle me-1"></i>Проверено: 12 марта 2026
                    </p>
                    <span class="badge status-rejected">Требует доработки</span>
                    <p class="text-muted small mt-1"><i class="bi bi-chat-left-text me-1"></i>Необходимо исправить ошибки в разделе 3</p>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
