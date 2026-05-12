<?php
session_start();
require_once '../protected/auth_guard.php';
if ($_SESSION['role'] !== 'admin') { header("Location: ../dashboard.php"); exit; }
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
        .sidebar-header { border-bottom-color: #dc3545; }
        .sidebar-header small { color: #dc3545; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(90deg, #dc3545 0%, #c82333 100%); }
        .welcome-card { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; }
        .card-header { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
        .table thead th { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
        .btn-danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
    </style>
</head>
<body class="role-admin">

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-shield-lock"></i> Учеба24</h5>
        <small>Админ-панель</small>
    </div>
    <nav class="mt-3">
        <a href="dashboard.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="users.php"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="schedule.php" class="active"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="settings.php"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-calendar-week"></i> Управление расписанием</h3>
        <p class="mb-0">Добавление и редактирование занятий</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Добавить занятие</div>
                <div class="card-body">
                    <form id="addScheduleForm">
                        <div class="mb-2"><label class="form-label">Группа</label><select class="form-select" id="schGroup" required></select></div>
                        <div class="mb-2"><label class="form-label">Предмет</label><input type="text" class="form-control" id="schSubject" required></div>
                        <div class="mb-2"><label class="form-label">Преподаватель</label><input type="text" class="form-control" id="schTeacher" required></div>
                        <div class="mb-2"><label class="form-label">День недели</label>
                            <select class="form-select" id="schDay" required>
                                <option value="понедельник">Понедельник</option>
                                <option value="вторник">Вторник</option>
                                <option value="среда">Среда</option>
                                <option value="четверг">Четверг</option>
                                <option value="пятница">Пятница</option>
                            </select>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6"><label class="form-label">Начало</label><input type="time" class="form-control" id="schStart" required></div>
                            <div class="col-6"><label class="form-label">Конец</label><input type="time" class="form-control" id="schEnd" required></div>
                        </div>
                        <div class="mb-2"><label class="form-label">Кабинет</label><input type="text" class="form-control" id="schRoom" placeholder="301" required></div>
                        <button type="button" class="btn btn-danger w-100" onclick="addSchedule()">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-list-ul me-2"></i>Расписание</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Группа</th><th>Предмет</th><th>День</th><th>Время</th><th>Кабинет</th><th>Действия</th></tr></thead>
                            <tbody id="scheduleBody"><tr><td colspan="6" class="text-center">Загрузка...</td></tr></tbody>
                        </table>
                    </div>
                </div>
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
                <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="100">100%</button>
                <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="125">125%</button>
                <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="150">150%</button>
                <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="200">200%</button>
            </div>
            <h6>Визуальный режим</h6>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="highContrast" id="highContrast"><label class="form-check-label" for="highContrast">Высокая контрастность</label></div>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="largeButtons" id="largeButtons"><label class="form-check-label" for="largeButtons">Увеличенные кнопки</label></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Назад</button>
            <button type="button" class="btn btn-danger" onclick="saveAccessibility()">Сохранить</button>
        </div>
    </div></div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let scheduleData = [], groupsData = [];
async function load() {
    const [sR, gR] = await Promise.all([
        fetch('../api/sync.php?action=get_schedule').then(r=>r.json()),
        fetch('../api/sync.php?action=get_groups').then(r=>r.json())
    ]);
    scheduleData = sR.success ? sR.data : [];
    groupsData = gR.success ? gR.data : [];
    const sel = document.getElementById('schGroup');
    sel.innerHTML = groupsData.map(g=>`<option value="${g.name}">${g.name}</option>`).join('');
    const tb = document.getElementById('scheduleBody');
    if(scheduleData.length===0) { tb.innerHTML='<tr><td colspan="6" class="text-center">Нет занятий</td></tr>'; return; }
    tb.innerHTML = scheduleData.map(s=>`
        <tr><td>${s.group_name}</td><td>${s.subject}</td><td>${s.day_of_week}</td><td>${s.start_time||s.start_time.substring?.(0,5)||'-'}-${s.end_time||s.end_time.substring?.(0,5)||'-'}</td><td>${s.classroom}</td><td><button class="btn btn-sm btn-danger" onclick="delSchedule(${s.id})"><i class="bi bi-trash"></i></button></td></tr>
    `).join('');
}
async function addSchedule() {
    const fd = new FormData();
    fd.append('action','add_schedule');
    fd.append('group_name',document.getElementById('schGroup').value);
    fd.append('subject',document.getElementById('schSubject').value);
    fd.append('teacher_name',document.getElementById('schTeacher').value);
    fd.append('day_of_week',document.getElementById('schDay').value);
    fd.append('start_time',document.getElementById('schStart').value+':00');
    fd.append('end_time',document.getElementById('schEnd').value+':00');
    fd.append('classroom',document.getElementById('schRoom').value);
    const r = await fetch('../api/sync.php',{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Занятие добавлено!','success'); document.getElementById('addScheduleForm').reset(); load(); }
    else showToast(d.message||'Ошибка','error');
}
async function delSchedule(id) {
    if(!confirm('Удалить?')) return;
    const fd = new FormData(); fd.append('action','delete_schedule'); fd.append('id',id);
    const r = await fetch('../api/sync.php',{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Удалено!','success'); load(); }
}
function saveAccessibility() {
    const s = {fontSize:localStorage.getItem('a11y_fontSize')||'100',highContrast:document.getElementById('highContrast').checked,largeButtons:document.getElementById('largeButtons').checked};
    localStorage.setItem('accessibility_settings',JSON.stringify(s));
    showToast('Настройки сохранены!','success');
    bootstrap.Modal.getInstance(document.getElementById('accessibilityModal')).hide();
}
function showToast(m,t) {
    const c=document.querySelector('.toast-container'),tEl=document.createElement('div');
    tEl.className=`toast align-items-center text-white bg-${t==='success'?'success':t==='error'?'danger':'primary'} border-0`;
    tEl.innerHTML=`<div class="d-flex"><div class="toast-body">${m}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    c.appendChild(tEl); new bootstrap.Toast(tEl,{delay:3000}).show(); tEl.addEventListener('hidden.bs.toast',()=>tEl.remove());
}
load();
</script>
</body>
</html>