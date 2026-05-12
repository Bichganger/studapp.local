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
    <title>Рассылка — Учеба24</title>
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
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php" class="active"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="settings.php"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-bell"></i> Рассылка уведомлений</h3>
        <p class="mb-0">Отправка уведомлений пользователям и группам</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Создать уведомление</div>
                <div class="card-body">
                    <form id="notifForm">
                        <div class="mb-2"><label class="form-label">Заголовок</label><input type="text" class="form-control" id="notifTitle" required></div>
                        <div class="mb-2"><label class="form-label">Сообщение</label><textarea class="form-control" id="notifMsg" rows="3" required></textarea></div>
                        <div class="mb-2"><label class="form-label">Кому</label>
                            <select class="form-select" id="notifType">
                                <option value="all">Всем</option>
                                <option value="students">Студентам</option>
                                <option value="teachers">Преподавателям</option>
                                <option value="group">Группе</option>
                            </select>
                        </div>
                        <div class="mb-2"><label class="form-label">Группа</label><select class="form-select" id="notifGroup"><option value="">Не выбрано</option></select></div>
                        <button type="button" class="btn btn-danger w-100" onclick="sendNotification()">Отправить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-list-ul me-2"></i>История уведомлений</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Заголовок</th><th>Кому</th><th>Дата</th><th>Действия</th></tr></thead>
                            <tbody id="notifBody"><tr><td colspan="4" class="text-center">Загрузка...</td></tr></tbody>
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
let notifData = [], groupsData = [];
async function load() {
    const [nR, gR] = await Promise.all([
        fetch('../api/sync.php?action=get_notifications').then(r=>r.json()),
        fetch('../api/sync.php?action=get_groups').then(r=>r.json())
    ]);
    notifData = nR.success ? nR.data : [];
    groupsData = gR.success ? gR.data : [];
    const sel = document.getElementById('notifGroup');
    sel.innerHTML = '<option value="">Не выбрано</option>' + groupsData.map(g=>`<option value="${g.name}">${g.name}</option>`).join('');
    const tb = document.getElementById('notifBody');
    if(notifData.length===0) { tb.innerHTML='<tr><td colspan="4" class="text-center">Нет уведомлений</td></tr>'; return; }
    tb.innerHTML = notifData.map(n=>`
        <tr><td><strong>${n.title}</strong><br><small class="text-muted">${n.message}</small></td><td><span class="badge bg-${n.target_type==='all'?'primary':n.target_type==='students'?'success':'info'}">${n.target_type}</span> ${n.target_group||''}</td><td>${n.created_at||'-'}</td><td><button class="btn btn-sm btn-danger" onclick="delNotif(${n.id})"><i class="bi bi-trash"></i></button></td></tr>
    `).join('');
}
async function sendNotification() {
    const fd = new FormData();
    fd.append('action','add_notification');
    fd.append('title',document.getElementById('notifTitle').value);
    fd.append('message',document.getElementById('notifMsg').value);
    fd.append('target_type',document.getElementById('notifType').value);
    fd.append('target_group',document.getElementById('notifGroup').value);
    const r = await fetch('../api/sync.php',{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Уведомление отправлено!','success'); document.getElementById('notifForm').reset(); load(); }
    else showToast(d.message||'Ошибка','error');
}
async function delNotif(id) {
    if(!confirm('Удалить?')) return;
    const fd = new FormData(); fd.append('action','delete_notification'); fd.append('id',id);
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