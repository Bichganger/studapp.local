<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админка — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/accessibility.css">
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-family: sans-serif; }
        .sidebar { min-height: 100vh; background: #1a202c; color: white; position: fixed; width: 240px; left: 0; top: 0; padding: 0; }
        .sidebar-header { background: #0d1b2a; padding: 20px; text-align: center; border-bottom: 3px solid #dc3545; }
        .sidebar-header h5 { color: #fff; font-weight: 700; font-size: 1.3rem; margin: 0; }
        .sidebar-header small { color: #dc3545; display: block; margin-top: 5px; font-size: 0.9rem; }
        .sidebar a { color: #d8dee9; margin: 5px 10px; border-radius: 5px; display: block; padding: 10px 15px; text-decoration: none; transition: all 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(90deg, #dc3545 0%, #c82333 100%); color: white; transform: translateX(5px); }
        .main-content { margin-left: 240px; padding: 20px; }
        footer { margin-left: 240px; padding: 15px; text-align: center; color: #e2e8f0; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: none; transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .welcome-card { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; }
    </style>
</head>
<body class="role-admin">

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-shield-lock"></i> Учеба24</h5>
        <small>Админ-панель</small>
    </div>
    <nav class="mt-3">
        <a href="dashboard.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="users.php"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-2">
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card rounded shadow-sm mb-4">
        <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
        <p>Вы вошли как <strong>Администратор</strong></p>
    </div>
    
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-people display-4 text-danger"></i>
                    <h5 class="mt-2">Пользователи</h5>
                    <p class="text-muted small mb-0">Управление</p>
                    <a href="users.php" class="btn btn-sm btn-danger mt-2">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill display-4 text-primary"></i>
                    <h5 class="mt-2">Группы</h5>
                    <p class="text-muted small mb-0">Студенты</p>
                    <a href="groups.php" class="btn btn-sm btn-primary mt-2">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check display-4 text-success"></i>
                    <h5 class="mt-2">Журнал</h5>
                    <p class="text-muted small mb-0">Посещаемость</p>
                    <a href="journal.php" class="btn btn-sm btn-success mt-2">Открыть</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-calendar-week"></i> Расписание</h5></div>
                <div class="card-body">
                    <div id="adminScheduleContainer">
                        <div class="text-center text-muted">Загрузка...</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-bell"></i> Уведомления</h5></div>
                <div class="card-body">
                    <div id="adminNotificationsContainer">
                        <div class="text-center text-muted">Загрузка...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>

<div class="modal fade" id="accessibilityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-universal-access"></i> Доступность</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Размер текста</h6>
                <div class="btn-group w-100 mb-3">
                    <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="100">100%</button>
                    <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="125">125%</button>
                    <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="150">150%</button>
                    <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="200">200%</button>
                </div>
                <h6>Визуальный режим</h6>
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="highContrast" id="highContrast"><label class="form-check-label" for="highContrast">Высокая контрастность</label></div>
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="largeButtons" id="largeButtons"><label class="form-check-label" for="largeButtons">Увеличенные кнопки</label></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sync.js"></script>
<script src="../assets/js/accessibility.js"></script>
<script>
async function loadDashboard() {
    const scheduleContainer = document.getElementById('adminScheduleContainer');
    const schedule = await Sync.getSchedule();
    const days = ['понедельник', 'вторник', 'среда', 'четверг', 'пятница'];
    let html = '<ul class="list-group">';
    days.forEach(day => {
        const dayItems = schedule.filter(s => s.day_of_week === day);
        if (dayItems.length > 0) html += `<li class="list-group-item"><strong>${day.charAt(0).toUpperCase() + day.slice(1)}</strong>: ${dayItems.length} занятий</li>`;
    });
    html += '</ul>';
    scheduleContainer.innerHTML = html;

    const notifContainer = document.getElementById('adminNotificationsContainer');
    const notifications = await Sync.getNotifications();
    html = '<ul class="list-group">';
    notifications.forEach(n => {
        html += `<li class="list-group-item"><strong>${n.title}</strong><p class="mb-0 small text-muted">${n.message}</p></li>`;
    });
    html += '</ul>';
    notifContainer.innerHTML = html;
}
loadDashboard();
</script>
</body>
</html>