<?php
session_start();
require_once '../protected/auth_guard.php';
if ($_SESSION['role'] !== 'student') { header("Location: ../dashboard.php"); exit; }
$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-common.css">
    <style>
        .sidebar-header { border-bottom-color: #198754; }
        .sidebar-header small { color: #198754; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(90deg, #198754 0%, #146c43 100%); }
        .welcome-card { background: linear-gradient(135deg, #198754 0%, #146c43 100%); color: white; }
        .card-header { background: linear-gradient(135deg, #198754 0%, #146c43 100%); color: white; }
        .table thead th { background: linear-gradient(135deg, #198754 0%, #146c43 100%); color: white; }
        .btn-success { background: linear-gradient(135deg, #198754 0%, #146c43 100%); border: none; }
    </style>
</head>
<body class="role-student">

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <small>Кабинет студента</small>
    </div>
    <nav class="mt-3">
        <a href="panel.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="schedule.php" class="active"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php"><i class="bi bi-file-text me-2"></i>Мои работы</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-calendar-week"></i> Моё расписание</h3>
        <p class="mb-0">Расписание занятий вашей группы</p>
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-list-ul me-2"></i>Занятия</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>День</th><th>Предмет</th><th>Преподаватель</th><th>Время</th><th>Кабинет</th></tr></thead>
                    <tbody id="scheduleBody"><tr><td colspan="5" class="text-center">Загрузка...</td></tr></tbody>
                </table>
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
                <button class="btn btn-outline-success" data-a11y="fontSize" data-value="100">100%</button>
                <button class="btn btn-outline-success" data-a11y="fontSize" data-value="125">125%</button>
                <button class="btn btn-outline-success" data-a11y="fontSize" data-value="150">150%</button>
                <button class="btn btn-outline-success" data-a11y="fontSize" data-value="200">200%</button>
            </div>
            <h6>Визуальный режим</h6>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="highContrast" id="highContrast"><label class="form-check-label" for="highContrast">Высокая контрастность</label></div>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="largeButtons" id="largeButtons"><label class="form-check-label" for="largeButtons">Увеличенные кнопки</label></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Назад</button>
            <button type="button" class="btn btn-success" onclick="saveAccessibility()">Сохранить</button>
        </div>
    </div></div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/api-config.js"></script>
<script src="../assets/js/accessibility.js"></script>
<script>
async function load() {
    try {
        const [s,g] = await Promise.all([
            fetch(API_BASE + '?action=get_schedule').then(r=>r.json()),
            fetch(API_BASE + '?action=get_groups').then(r=>r.json())
        ]);
        const schedule = s.success ? s.data : [];
        const groups = g.success ? g.data : [];
        const today = new Date().toLocaleDateString('ru-RU',{weekday:'long'}).toLowerCase();
        const todayClasses = schedule.filter(x=>x.day_of_week?.toLowerCase()===today);

        const tl = document.getElementById('todayList');
        if(todayClasses.length===0) { tl.innerHTML='<div class="list-group-item text-muted">Сегодня нет занятий</div>'; return; }
        tl.innerHTML = todayClasses.map(x=>{
            const st=(x.start_time||'').substring(0,5);
            const et=(x.end_time||'').substring(0,5);
            return `<div class="list-group-item"><strong>${x.subject}</strong><br><small class="text-muted">${st}-${et} | Каб. ${x.classroom}</small></div>`;
        }).join('');

        const tb = document.getElementById('weekBody');
        const days = ['понедельник','вторник','среда','четверг','пятница','суббота'];
        tb.innerHTML = days.map(d=>{
            const dayClasses = schedule.filter(x=>x.day_of_week?.toLowerCase()===d);
            if(dayClasses.length===0) return '';
            return `<tr><td class="fw-bold text-capitalize">${d}</td><td>`+dayClasses.map(x=>{
                const st=(x.start_time||'').substring(0,5);
                const et=(x.end_time||'').substring(0,5);
                return `<div class="mb-1"><strong>${x.subject}</strong> — ${x.teacher_name}<br><small class="text-muted">${st}-${et} | Каб. ${x.classroom}</small></div>`;
            }).join('')+`</td></tr>`;
        }).filter(Boolean).join('');
    } catch(e) { console.error(e); }
}
load();
</script>
</body>
</html>