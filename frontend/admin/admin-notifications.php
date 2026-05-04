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
    <title>Рассылка — Админка — Учеба24</title>
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
        <a href="admin-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="admin-groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="admin-library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="admin-notifications.php" class="active"><i class="bi bi-bell me-2"></i>Рассылка</a>
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

    <h2><i class="bi bi-bell"></i> Отправка уведомлений</h2>
    <p class="text-muted">Отправляйте сообщения всем или отдельным группам пользователей.</p>

    <!-- Форма отправки -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-send me-2"></i>Новое уведомление</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="mb-3">
                    <label class="form-label">Кому отправить? *</label>
                    <select class="form-select" required>
                        <option value="">Выберите получателей...</option>
                        <option value="all">Всем пользователям</option>
                        <option value="students">Всем студентам</option>
                        <option value="teachers">Всем преподавателям</option>
                        <option value="group">Конкретной группе</option>
                    </select>
                </div>
                <div class="mb-3" id="groupSelect" style="display:none;">
                    <label class="form-label">Выберите группу</label>
                    <select class="form-select">
                        <option>ИТ-321</option>
                        <option>ИТ-312</option>
                        <option>ЭК-214</option>
                        <option>ПС-133</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Тема *</label>
                    <input type="text" class="form-control" placeholder="Введите тему уведомления" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Текст сообщения *</label>
                    <textarea class="form-control" rows="6" placeholder="Введите текст уведомления..." required></textarea>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="sendEmail">
                    <label class="form-check-label" for="sendEmail">Отправить также на email</label>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Отправить уведомление</button>
            </form>
        </div>
    </div>

    <!-- История уведомлений -->
    <h5 class="mb-3">История отправлений</h5>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Дата</th>
                            <th>Тема</th>
                            <th>Получатели</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>17.03.2026</td>
                            <td>Изменение расписания</td>
                            <td>ИТ-321</td>
                            <td><span class="badge bg-success">Отправлено</span></td>
                            <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></button></td>
                        </tr>
                        <tr>
                            <td>15.03.2026</td>
                            <td>Напоминание о дедлайне</td>
                            <td>Все студенты</td>
                            <td><span class="badge bg-success">Отправлено</span></td>
                            <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></button></td>
                        </tr>
                        <tr>
                            <td>14.03.2026</td>
                            <td>Педагогический совет</td>
                            <td>Все преподаватели</td>
                            <td><span class="badge bg-success">Отправлено</span></td>
                            <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></button></td>
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
<script>
document.querySelector('select[name="receivers"]').addEventListener('change', function() {
    const groupSelect = document.getElementById('groupSelect');
    groupSelect.style.display = this.value === 'group' ? 'block' : 'none';
});
</script>
</body>
</html>
