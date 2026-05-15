<?php
session_start();
require_once '../protected/auth_guard.php';
if ($_SESSION['role'] !== 'teacher') { header("Location: ../dashboard.php"); exit; }
$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабинет преподавателя — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-common.css">
    <style>
        .sidebar-header { border-bottom-color: #0dcaf0; }
        .sidebar-header small { color: #0dcaf0; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(90deg, #0dcaf0 0%, #0bb5d6 100%); }
        .welcome-card { background: linear-gradient(135deg, #0dcaf0 0%, #0bb5d6 100%); color: white; }
        .card-header { background: linear-gradient(135deg, #0dcaf0 0%, #0bb5d6 100%); color: white; }
        .table thead th { background: linear-gradient(135deg, #0dcaf0 0%, #0bb5d6 100%); color: white; }
        .btn-info { background: linear-gradient(135deg, #0dcaf0 0%, #0bb5d6 100%); border: none; color: #fff; }
        .stat-card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="role-teacher">

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-person-badge"></i> Учеба24</h5>
        <small>Кабинет преподавателя</small>
    </div>
    <nav class="mt-3">
        <a href="panel.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="journal.php"><i class="bi bi-journal-text me-2"></i>Журнал</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php"><i class="bi bi-file-text me-2"></i>Работы</a>
        <a href="groups.php"><i class="bi bi-people me-2"></i>Группы</a>
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
        <p class="mb-0">Вы вошли как <strong>Преподаватель</strong></p>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Работ на проверке</h6><h2 class="mb-0" id="statPending">0</h2></div>
                    <i class="bi bi-file-earmark-text display-4 text-info opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Уведомлений</h6><h2 class="mb-0" id="statNotif">0</h2></div>
                    <i class="bi bi-bell display-4 text-info opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Групп</h6><h2 class="mb-0" id="statGroups">0</h2></div>
                    <i class="bi bi-people display-4 text-info opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Занятий сегодня</h6><h2 class="mb-0" id="statClasses">0</h2></div>
                    <i class="bi bi-calendar display-4 text-info opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-bell me-2"></i>Последние уведомления</div>
                <div class="card-body"><div id="notifList" class="list-group list-group-flush">Загрузка...</div></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-file-earmark-check me-2"></i>Работы на проверке</div>
                <div class="card-body"><div id="assignList" class="list-group list-group-flush">Загрузка...</div></div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>

<div class="modal fade" id="accessibilityModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-universal-access"></i> Доступность</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <h6>Размер текста</h6>
            <div class="btn-group w-100 mb-3">
                <button class="btn btn-outline-info" data-a11y="fontSize" data-value="100">100%</button>
                <button class="btn btn-outline-info" data-a11y="fontSize" data-value="125">125%</button>
                <button class="btn btn-outline-info" data-a11y="fontSize" data-value="150">150%</button>
                <button class="btn btn-outline-info" data-a11y="fontSize" data-value="200">200%</button>
            </div>
            <h6>Визуальный режим</h6>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="highContrast" id="highContrast"><label class="form-check-label" for="highContrast">Высокая контрастность</label></div>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="largeButtons" id="largeButtons"><label class="form-check-label" for="largeButtons">Увеличенные кнопки</label></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Назад</button>
            <button type="button" class="btn btn-info" onclick="saveAccessibility()">Сохранить</button>
        </div>
    </div></div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/api-config.js"></script>
<script src="../assets/js/accessibility.js"></script>
<script>
async function loadStats() {
    try {
        const [n, a, g, s] = await Promise.all([
            fetch(API_BASE + '?action=get_notifications').then(r=>r.json()),
            fetch(API_BASE + '?action=get_assignments').then(r=>r.json()),
            fetch(API_BASE + '?action=get_groups').then(r=>r.json()),
            fetch(API_BASE + '?action=get_schedule').then(r=>r.json())
        ]);
        const notifs = n.success ? n.data : [];
        const assigns = a.success ? a.data : [];
        const groups = g.success ? g.data : [];
        const schedule = s.success ? s.data : [];
        const pending = assigns.filter(x=>x.status==='pending'||!x.status);
        const today = new Date().toLocaleDateString('ru-RU',{weekday:'long'}).toLowerCase();
        const todayClasses = schedule.filter(x=>x.day_of_week?.toLowerCase()===today);

        document.getElementById('statPending').textContent = pending.length;
        document.getElementById('statNotif').textContent = notifs.length;
        document.getElementById('statGroups').textContent = groups.length;
        document.getElementById('statClasses').textContent = todayClasses.length;

        const nl = document.getElementById('notifList');
        nl.innerHTML = notifs.slice(0,5).map(x=>`<div class="list-group-item"><strong>${x.title}</strong><p class="mb-0 small text-muted">${x.message}</p></div>`).join('') || '<div class="list-group-item text-muted">Нет уведомлений</div>';

        const al = document.getElementById('assignList');
        al.innerHTML = pending.slice(0,5).map(x=>`<div class="list-group-item"><strong>${x.title}</strong><br><small class="text-muted">${x.student_name} — ${x.subject}</small></div>`).join('') || '<div class="list-group-item text-muted">Нет работ на проверке</div>';
    } catch(e) { console.error(e); }
}

// Запускаем загрузку данных
loadStats();
</script>
</body>
</html>