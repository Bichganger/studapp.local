<?php
session_start();
require_once '../protected/auth_guard.php';
if ($_SESSION['role'] !== 'admin') { header('Location: ../dashboard.php'); exit; }
$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Группы — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sidebar-header { border-bottom-color: var(--danger); }
        .sidebar-header small { color: var(--danger); }
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
        <a href="groups.php" class="active"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
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
        <h3><i class="bi bi-people-fill"></i> Управление группами</h3>
        <p class="mb-0">Создание и редактирование учебных групп</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Создать группу</div>
                <div class="card-body">
                    <form id="addGroupForm">
                        <div class="mb-2"><label class="form-label">Название</label><input type="text" class="form-control" id="grpName" placeholder="ПИ-21" required></div>
                        <div class="mb-2"><label class="form-label">Специальность</label><input type="text" class="form-control" id="grpSpec" placeholder="Программная инженерия" required></div>
                        <div class="mb-2"><label class="form-label">Курс</label><input type="number" class="form-control" id="grpCourse" min="1" max="5" value="1" required></div>
                        <button type="button" class="btn btn-danger w-100" onclick="addGroup()">Создать</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-list-ul me-2"></i>Список групп</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Группа</th><th>Специальность</th><th>Курс</th><th>Студентов</th><th>Действия</th></tr></thead>
                            <tbody id="groupsBody"><tr><td colspan="5" class="text-center">Загрузка...</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>

<div class="modal fade" id="studentsModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="studentsModalTitle">Студенты группы</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>ФИО</th><th>Логин</th><th>Email</th></tr></thead>
                    <tbody id="studentsBody"><tr><td colspan="3" class="text-center">Загрузка...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div></div>
</div>

<div class="modal fade" id="groupModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="groupModalTitle">Редактировать группу</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form id="groupForm">
                <input type="hidden" id="editGroupId">
                <div class="mb-3"><label class="form-label">Название</label><input type="text" class="form-control" id="editGroupName" required></div>
                <div class="mb-3"><label class="form-label">Специальность</label><input type="text" class="form-control" id="editGroupSpec" required></div>
                <div class="mb-3"><label class="form-label">Курс</label><input type="number" class="form-control" id="editGroupCourse" min="1" max="5" required></div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            <button type="button" class="btn btn-danger" onclick="saveGroup()">Сохранить</button>
        </div>
    </div></div>
</div>

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
<script src="../assets/js/api-config.js"></script>
<script>
let groupsData = [];
const groupModal = new bootstrap.Modal(document.getElementById('groupModal'));
const studentsModal = new bootstrap.Modal(document.getElementById('studentsModal'));

async function loadGroups() {
    const r = await fetch(API_BASE + '?action=get_groups').then(r=>r.json());
    groupsData = r.success ? r.data : [];
    const tb = document.getElementById('groupsBody');
    if(groupsData.length===0) { tb.innerHTML='<tr><td colspan="5" class="text-center">Нет групп</td></tr>'; return; }
    tb.innerHTML = groupsData.map(g=>`
        <tr>
            <td><strong>${g.name}</strong></td>
            <td>${g.specialty}</td>
            <td><span class="badge bg-info">${g.course}</span></td>
            <td><span class="badge bg-success">${g.student_count||0}</span></td>
            <td>
                <button class="btn btn-sm btn-primary me-1" onclick="editGroup(${g.id})"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-success me-1" onclick="viewStudents('${g.name}')"><i class="bi bi-people"></i></button>
                <button class="btn btn-sm btn-danger" onclick="delGroup(${g.id})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `).join('');
}

async function viewStudents(groupName) {
    document.getElementById('studentsModalTitle').textContent = `Студенты группы ${groupName}`;
    document.getElementById('studentsBody').innerHTML = '<tr><td colspan="3" class="text-center">Загрузка...</td></tr>';
    studentsModal.show();
    try {
        const r = await fetch(API_BASE + '?action=get_users_by_group&group_name=' + encodeURIComponent(groupName)).then(r=>r.json());
        const studs = r.success ? r.data : [];
        const tb = document.getElementById('studentsBody');
        if(studs.length===0) { tb.innerHTML='<tr><td colspan="3" class="text-center text-muted">Нет студентов в группе</td></tr>'; return; }
        tb.innerHTML = studs.map(s=>`
            <tr>
                <td>${s.full_name}</td>
                <td>${s.username}</td>
                <td>${s.email||'-'}</td>
            </tr>
        `).join('');
    } catch(e) { console.error(e); }
}
async function addGroup() {
    const name = document.getElementById('grpName').value.trim();
    const specialty = document.getElementById('grpSpec').value.trim();
    const course = parseInt(document.getElementById('grpCourse').value);
    if(!name||!specialty||!course) { showToast('Заполните все поля!','error'); return; }
    const fd = new FormData();
    fd.append('action','add_group');
    fd.append('name',name);
    fd.append('specialty',specialty);
    fd.append('course',course);
    const r = await fetch(API_BASE,{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Группа создана!','success'); document.getElementById('addGroupForm').reset(); loadGroups(); }
    else showToast(d.message||'Ошибка','error');
}

function editGroup(id) {
    const g = groupsData.find(x => x.id === id);
    if (!g) return;
    document.getElementById('editGroupId').value = g.id;
    document.getElementById('editGroupName').value = g.name;
    document.getElementById('editGroupSpec').value = g.specialty;
    document.getElementById('editGroupCourse').value = g.course;
    document.getElementById('groupModalTitle').textContent = 'Редактировать группу';
    groupModal.show();
}

async function saveGroup() {
    const id = document.getElementById('editGroupId').value;
    const fd = new FormData();
    fd.append('action', 'update_group');
    fd.append('id', id);
    fd.append('name', document.getElementById('editGroupName').value.trim());
    fd.append('specialty', document.getElementById('editGroupSpec').value.trim());
    fd.append('course', document.getElementById('editGroupCourse').value);
    const r = await fetch(API_BASE, {method:'POST', body:fd});
    const d = await r.json();
    if (d.success) {
        showToast('Группа обновлена!','success');
        groupModal.hide();
        loadGroups();
    } else {
        showToast(d.message||'Ошибка','error');
    }
}

async function delGroup(id) {
    if(!confirm('Удалить группу?')) return;
    const fd = new FormData(); fd.append('action','delete_group'); fd.append('id',id);
    const r = await fetch(API_BASE,{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Группа удалена!','success'); loadGroups(); }
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
loadGroups();
</script>
</body>
</html>
