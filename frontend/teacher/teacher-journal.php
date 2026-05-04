<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'teacher') {
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
    <title>Журнал — Кабинет преподавателя — Учёба.Онлайн</title>
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
        .journal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .journal-table {
            width: 100%;
            border-collapse: collapse;
        }
        .journal-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 0.85rem;
        }
        .journal-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        .journal-table tr:hover {
            background: #f8f9fa;
        }
        .journal-table .student-name {
            text-align: left;
            font-weight: 500;
        }
        .grade-input {
            width: 40px;
            height: 40px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            font-weight: bold;
        }
        .grade-input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.25);
        }
        .grade-5 { background: #d4edda; color: #155724; }
        .grade-4 { background: #cce5ff; color: #004085; }
        .grade-3 { background: #fff3cd; color: #856404; }
        .grade-2 { background: #f8d7da; color: #721c24; }
        .attendance-present { color: #198754; font-size: 1.2rem; }
        .attendance-absent { color: #dc3545; font-size: 1.2rem; }
        .attendance-lat { color: #fd7e14; font-size: 1.2rem; }
        .week-nav {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .subject-badge {
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
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
        <a href="teacher-journal.php" class="active"><i class="bi bi-journal-text me-2"></i>Журнал</a>
        <a href="teacher-grades.php"><i class="bi bi-graph-up me-2"></i>����������� ������</a>`n        <a href="teacher-journal.php"><i class="bi bi-journal-text me-2"></i>������</a>`n        <a href="teacher-assignments.php"><i class="bi bi-journal-check me-2"></i>������ ���������</a>`n        <a href="teacher-groups.php""><i class="bi bi-people-fill me-2"></i>Мои группы</a>
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

    <div class="journal-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1"><i class="bi bi-journal-text"></i> Электронный журнал</h2>
                <p class="mb-0 opacity-75"><?= $fullName ?></p>
            </div>
            <div>
                <span class="subject-badge">Программирование (практика)</span>
            </div>
        </div>
    </div>

    <!-- Навигация по неделям -->
    <div class="week-nav">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-left"></i> Предыдущая неделя</button>
            </div>
            <div class="text-center">
                <h5 class="mb-0">18 – 24 марта 2026</h5>
                <small class="text-muted">Текущая неделя</small>
            </div>
            <div>
                <button class="btn btn-outline-secondary btn-sm">Следующая неделя <i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <!-- Выбор группы и предмета -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Группа</label>
                    <select class="form-select">
                        <option>ИТ-321</option>
                        <option>ИТ-312</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Дисциплина</label>
                    <select class="form-select">
                        <option>Программирование (практика)</option>
                        <option>Алгоритмы и структуры данных</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Семестр</label>
                    <select class="form-select">
                        <option>Весна 2026</option>
                        <option>Осень 2025</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Журнал посещаемости и оценок -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="journal-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">№</th>
                            <th style="min-width: 200px;">ФИО студента</th>
                            <th style="min-width: 100px;">Материалы</th>
                            <th>18.03<br><small>Вт</small></th>
                            <th>19.03<br><small>Ср</small></th>
                            <th>20.03<br><small>Чт</small></th>
                            <th>23.03<br><small>Пн</small></th>
                            <th>24.03<br><small>Вт</small></th>
                            <th style="min-width: 100px;">Ср. балл</th>
                            <th style="min-width: 80px;">Итог</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td class="student-name">Иванов Иван Петрович</td>
                            <td>
                                <button class="btn btn-sm btn-link" title="Добавить материал"><i class="bi bi-plus-circle"></i></button>
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td><strong>—</strong></td>
                            <td>
                                <select class="form-select form-select-sm" style="width: 70px; margin: 0 auto;">
                                    <option>−</option>
                                    <option>5</option>
                                    <option>4</option>
                                    <option>3</option>
                                    <option>2</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td class="student-name">Петров Петр Сергеевич</td>
                            <td>
                                <button class="btn btn-sm btn-link"><i class="bi bi-plus-circle"></i></button>
                            </td>
                            <td>
                                <input type="text" class="grade-input grade-5" value="5">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input grade-4" value="4">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td><strong>4.5</strong></td>
                            <td>
                                <select class="form-select form-select-sm" style="width: 70px; margin: 0 auto;">
                                    <option selected>5</option>
                                    <option>4</option>
                                    <option>3</option>
                                    <option>2</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td class="student-name">Сидорова Анна Ивановна</td>
                            <td>
                                <button class="btn btn-sm btn-link"><i class="bi bi-plus-circle"></i></button>
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input attendance-absent" value="Н" title="Отсутствовал">
                            </td>
                            <td>
                                <input type="text" class="grade-input grade-5" value="5">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td><strong>5</strong></td>
                            <td>
                                <select class="form-select form-select-sm" style="width: 70px; margin: 0 auto;">
                                    <option selected>5</option>
                                    <option>4</option>
                                    <option>3</option>
                                    <option>2</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td class="student-name">Козлов Дмитрий Алексеевич</td>
                            <td>
                                <button class="btn btn-sm btn-link"><i class="bi bi-plus-circle"></i></button>
                            </td>
                            <td>
                                <input type="text" class="grade-input grade-4" value="4">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input attendance-lat" value="З" title="Запоздал">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td><strong>4</strong></td>
                            <td>
                                <select class="form-select form-select-sm" style="width: 70px; margin: 0 auto;">
                                    <option>5</option>
                                    <option selected>4</option>
                                    <option>3</option>
                                    <option>2</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td class="student-name">Смирнова Мария Владимировна</td>
                            <td>
                                <button class="btn btn-sm btn-link"><i class="bi bi-plus-circle"></i></button>
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input grade-3" value="3">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td>
                                <input type="text" class="grade-input" placeholder="-">
                            </td>
                            <td><strong>3</strong></td>
                            <td>
                                <select class="form-select form-select-sm" style="width: 70px; margin: 0 auto;">
                                    <option>5</option>
                                    <option>4</option>
                                    <option selected>3</option>
                                    <option>2</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Сохранить все изменения</button>
        <button class="btn btn-outline-primary"><i class="bi bi-printer me-1"></i>Распечатать журнал</button>
        <button class="btn btn-outline-secondary"><i class="bi bi-download me-1"></i>Экспорт в Excel</button>
    </div>

    <!-- Легенда -->
    <div class="card mt-4">
        <div class="card-body">
            <h6 class="mb-3"><i class="bi bi-info-circle"></i> Легенда журнала</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <span class="grade-5 px-3 py-1 rounded me-2">5</span>
                        <small>Отлично</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <span class="grade-4 px-3 py-1 rounded me-2">4</span>
                        <small>Хорошо</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <span class="grade-3 px-3 py-1 rounded me-2">3</span>
                        <small>Удовлетворительно</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <span class="grade-2 px-3 py-1 rounded me-2">2</span>
                        <small>Неудовлетворительно</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <span class="attendance-present px-2 me-2"><i class="bi bi-check-circle"></i></span>
                        <small>Посещал</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <span class="attendance-absent px-2 me-2"><i class="bi bi-x-circle"></i></span>
                        <small>Отсутствовал (Н)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <span class="attendance-lat px-2 me-2"><i class="bi bi-hourglass-split"></i></span>
                        <small>Запоздал (З)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Подвал -->
<footer>&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Автоматическая раскраска оценок
document.querySelectorAll('.grade-input').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('grade-5', 'grade-4', 'grade-3', 'grade-2');
        if (this.value === '5') this.classList.add('grade-5');
        else if (this.value === '4') this.classList.add('grade-4');
        else if (this.value === '3') this.classList.add('grade-3');
        else if (this.value === '2') this.classList.add('grade-2');
    });
});
</script>
</body>
</html>



