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
    <title>Рассылка — Учеба24</title>
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
    </style>
</head>
<body class="role-teacher">

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-person-badge"></i> Учеба24</h5>
        <small>Кабинет преподавателя</small>
    </div>
    <nav class="mt-3">
        <a href="panel.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="journal.php"><i class="bi bi-journal-text me-2"></i>Журнал</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php"><i class="bi bi-file-text me-2"></i>Работы</a>
        <a href="groups.php"><i class="bi bi-people me-2"></i>Группы</a>
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="notifications.php" class="active"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-bell"></i> Рассылка уведомлений</h3>
        <p class="mb-0">Отправка уведомлений студентам и группам</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Создать уведомление</div>
                <div class="card-body">
                    <form id="notifForm">
                        <div class="mb-2"><label class="form-label">Заголовок</label><input type="text" class="form-control" id="nTitle" required></div>
                        <div class="mb-2"><label class="form-label">Сообщение</label><textarea class="form-control" id="nMsg" rows="3" required></textarea></div>
                        <div class="mb-2"><label class="form-label">Кому</label>
                            <select class="form-select" id="nType">
                                <option value="all">Всем</option>
                                <option value="students">Студентам</option>
                                <option value="group">Группе</option>
                            </select>
                        </div>
                        <div class="mb-2"><label class="form-label">Группа</label><select class="form-select" id="nGroup"><option value="">Не выбрано</option></select></div>
                        <button type="button" class="btn btn-info w-100" onclick="sendNotif()">Отправить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-list-ul me-2"></i>История уведомлений</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Заголовок</th><th>Кому</th><th>Дата</th></tr></thead>
                            <tbody id="notifBody"><tr><td colspan="3" class="text-center">Загрузка...</td></tr></tbody>
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
<script>
let notifData = [], groupsData = [];
async function load() {
    const [n,g] = await Promise.all([
        fetch('../api/sync.php?action=get_notifications').then(r=>r.json()),
        fetch('../api/sync.php?action=get_groups').then(r=>r.json())
    ]);
    notifData = n.success ? n.data : [];
    groupsData = g.success ? g.data : [];
    const sel = document.getElementById('nGroup');
    sel.innerHTML = '<option value="">Не выбрано</option>' + groupsData.map(gr=>`<option value="${gr.name}">${gr.name}</option>`).join('');
    const tb = document.getElementById('notifBody');
    if(notifData.length===0) { tb.innerHTML='<tr><td colspan="3" class="text-center">Нет уведомлений</td></tr>'; return; }
    tb.innerHTML = notifData.map(x=>`
        <tr><td><strong>${x.title}</strong><br><small class="text-muted">${x.message}</small></td>
        <td><span class="badge bg-${x.target_type==='all'?'primary':x.target_type==='students'?'success':'info'}">${x.target_type}</span> ${x.target_group||''}</td>
        <td>${x.created_at||'-'}</td></tr>
    `).join('');
}
async function sendNotif() {
    const fd = new FormData();
    fd.append('action','add_notification');
    fd.append('title',document.getElementById('nTitle').value);
    fd.append('message',document.getElementById('nMsg').value);
    fd.append('target_type',document.getElementById('nType').value);
    fd.append('target_group',document.getElementById('nGroup').value);
    const r = await fetch('../api/sync.php',{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Отправлено!','success'); document.getElementById('notifForm').reset(); load(); }
    else showToast(d.message||'Ошибка','error');
}
function saveAccessibility() {
    const s = {fontSize:localStorage.getItem('a11y_fontSize')||'100',highContrast:document.getElementById('highContrast').checked,largeButtons:document.getElementById('largeButtons').checked};
    localStorage.setItem('accessibility_settings',JSON.stringify(s));
    showToast('Настройки сохранены!','success');
    bootstrap.Modal.getInstance(document.getElementById('accessibilityModal')).hide();
}
function showToast(m,t) {
    const c=document.querySelector('.toast-container'),el=document.createElement('div');
    el.className=`toast align-items-center text-white bg-${t==='success'?'success':t==='error'?'danger':'primary'} border-0`;
    el.innerHTML=`<div class="d-flex"><div class="toast-body">${m}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    c.appendChild(el); new bootstrap.Toast(el,{delay:3000}).show(); el.addEventListener('hidden.bs.toast',()=>el.remove());
}
load();
</script>
</body>
</html>