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
    <title>Мои группы — Кабинет преподавателя — Учёба.Онлайн</title>
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
        .group-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .group-card:hover {
            transform: translateY(-3px);
        }
        .student-list {
            max-height: 200px;
            overflow-y: auto;
        }
        .student-item {
            padding: 8px 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        .student-item:last-child {
            border-bottom: none;
        }
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
        <a href="teacher-grades.php"><i class="bi bi-graph-up me-2"></i>����������� ������</a>`n        <a href="teacher-journal.php"><i class="bi bi-journal-text me-2"></i>������</a>`n        <a href="teacher-assignments.php"><i class="bi bi-journal-check me-2"></i>������ ���������</a>`n        <a href="teacher-groups.php"" class="active"><i class="bi bi-people-fill me-2"></i>Мои группы</a>
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

    <h2><i class="bi bi-people-fill"></i> Кураторские группы</h2>
    <p class="text-muted">Управление группами, где вы являетесь куратором/классным руководителем</p>

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-1"></i> 
        Куратор отвечает за академическую успеваемость, посещаемость и воспитательную работу со студентами группы.
    </div>

    <div class="row g-4">
        <!-- Группа ИТ-321 -->
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
                    <p class="text-muted small mb-2"><i class="bi bi-person-badge me-1"></i>Куратор: <?= $fullName ?></p>
                    <p class="text-muted small mb-3"><i class="bi bi-calendar me-1"></i>3 курс • Группа образована: 01.09.2024</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <h6 class="mb-0 text-success">92%</h6>
                                <small class="text-muted">Посещаемость</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <h6 class="mb-0 text-info">4.2</h6>
                                <small class="text-muted">Средний балл</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <h6 class="mb-0 text-warning">2</h6>
                                <small class="text-muted">Должников</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <h6 class="mb-0 text-primary">18</h6>
                                <small class="text-muted">Активистов</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1"><i class="bi bi-eye me-1"></i>Журнал группы</button>
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-people me-1"></i>Студенты</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Группа ИТ-312 -->
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
                    <p class="text-muted small mb-2"><i class="bi bi-person-badge me-1"></i>Куратор: <?= $fullName ?></p>
                    <p class="text-muted small mb-3"><i class="bi bi-calendar me-1"></i>3 курс • Группа образована: 01.09.2024</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <h6 class="mb-0 text-success">88%</h6>
                                <small class="text-muted">Посещаемость</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <h6 class="mb-0 text-info">4.0</h6>
                                <small class="text-muted">Средний балл</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <h6 class="mb-0 text-warning">3</h6>
                                <small class="text-muted">Должников</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <h6 class="mb-0 text-primary">15</h6>
                                <small class="text-muted">Активистов</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1"><i class="bi bi-eye me-1"></i>Журнал группы</button>
                        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-people me-1"></i>Студенты</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Мероприятия группы -->
    <h5 class="mt-5 mb-3"><i class="bi bi-calendar-event"></i> Ближайшие мероприятия кураторских групп</h5>
    <div class="card">
        <div class="card-body">
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Общегрупповое собрание</h6>
                        <p class="text-muted small mb-0">Группа ИТ-321 • 25 марта 2026, 14:00</p>
                    </div>
                    <span class="badge bg-warning">Завтра</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">День открытых дверей</h6>
                        <p class="text-muted small mb-0">Группа ИТ-312 • 28 марта 2026, 10:00</p>
                    </div>
                    <span class="badge bg-info">Скоро</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Подведение итогов семестра</h6>
                        <p class="text-muted small mb-0">Обе группы • 30 марта 2026, 15:00</p>
                    </div>
                    <span class="badge bg-secondary">25 марта</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Быстрые действия куратора -->
    <h5 class="mt-5 mb-3"><i class="bi bi-lightning-charge"></i> Быстрые действия</h5>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-bell display-6 text-primary mb-2"></i>
                    <h6>Рассылка</h6>
                    <p class="text-muted small mb-2">Уведомить группу</p>
                    <button class="btn btn-sm btn-outline-primary">Создать</button>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-file-earmark-text display-6 text-success mb-2"></i>
                    <h6>Отчёт</h6>
                    <p class="text-muted small mb-2">По успеваемости</p>
                    <button class="btn btn-sm btn-outline-success">Сформировать</button>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-people display-6 text-info mb-2"></i>
                    <h6>Собрание</h6>
                    <p class="text-muted small mb-2">Запланировать встречу</p>
                    <button class="btn btn-sm btn-outline-info">Создать</button>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-journal-text display-6 text-warning mb-2"></i>
                    <h6>Журнал</h6>
                    <p class="text-muted small mb-2">Посещаемость</p>
                    <button class="btn btn-sm btn-outline-warning">Открыть</button>
                </div>
            </div>
        </div>
    </div>

</main>

<!-- Подвал -->
<footer>&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



