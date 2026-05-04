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
    <title>Выставление оценок — Кабинет преподавателя — Учёба.Онлайн</title>
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
        .grade-input { width: 80px; }
        .table th { background: #f8f9fa; }
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
        <a href="teacher-journal.php"><i class="bi bi-journal-text me-2"></i>Журнал</a>
        <a href="teacher-grades.php" class="active"><i class="bi bi-graph-up me-2"></i>Выставление оценок</a>
        <a href="teacher-assignments.php"><i class="bi bi-journal-check me-2"></i>Работы студентов</a>
        <a href="teacher-groups.php"><i class="bi bi-people-fill me-2"></i>Мои группы</a>
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

    <h2><i class="bi bi-graph-up"></i> Выставление оценок</h2>
    <p class="text-muted">Выберите группу и предмет для выставления оценок студентам.</p>

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Группа</label>
                    <select class="form-select">
                        <option selected>Выберите группу...</option>
                        <option>ИТ-321</option>
                        <option>ИТ-312</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Предмет</label>
                    <select class="form-select">
                        <option selected>Выберите предмет...</option>
                        <option>Программирование (практика)</option>
                        <option>Алгоритмы и структуры данных</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Показать журнал</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Журнал оценок -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Журнал успеваемости</h5>
            <button class="btn btn-sm btn-success"><i class="bi bi-check-circle me-1"></i>Сохранить все</button>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-1"></i> Группа ИТ-321 • Программирование (практика)
            </div>
            
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>ФИО студента</th>
                        <th>Лекции</th>
                        <th>Практика</th>
                        <th>Лабораторные</th>
                        <th>Средний балл</th>
                        <th>Итоговая оценка</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Иванов Иван</td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><select class="form-select grade-input"><option>−</option><option>5</option><option>4</option><option>3</option><option>2</option></select></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Петров Петр</td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><select class="form-select grade-input"><option>−</option><option>5</option><option>4</option><option>3</option><option>2</option></select></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Сидорова Анна</td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><select class="form-select grade-input"><option>−</option><option>5</option><option>4</option><option>3</option><option>2</option></select></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Козлов Дмитрий</td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><input type="number" class="form-control grade-input" placeholder="-" min="0" max="100"></td>
                        <td><select class="form-select grade-input"><option>−</option><option>5</option><option>4</option><option>3</option><option>2</option></select></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Подвал -->
<footer>&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



