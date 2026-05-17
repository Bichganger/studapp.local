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
    <title>Библиотека — Учеба24</title>
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
        <a href="groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="library.php" class="active"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="settings.php"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-journal"></i> Библиотека материалов</h3>
        <p class="mb-0">Управление учебными материалами и ресурсами</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Добавить материал</div>
                <div class="card-body">
                    <form id="addLibraryForm">
                        <div class="mb-2"><label class="form-label">Название</label><input type="text" class="form-control" id="libTitle" required></div>
                        <div class="mb-2"><label class="form-label">Описание</label><textarea class="form-control" id="libDesc" rows="2"></textarea></div>
                        <div class="mb-2"><label class="form-label">Тип</label>
                            <select class="form-select" id="libType">
                                <option value="document">Документ</option>
                                <option value="presentation">Презентация</option>
                                <option value="code">Код</option>
                                <option value="video">Видео</option>
                            </select>
                        </div>
                        <div class="mb-2"><label class="form-label">Группа</label><select class="form-select" id="libGroup"><option value="">Все группы</option></select></div>
                        <button type="button" class="btn btn-danger w-100" onclick="addLibrary()">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-list-ul me-2"></i>Материалы</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Название</th><th>Тип</th><th>Группа</th><th>Действия</th></tr></thead>
                            <tbody id="libraryBody"><tr><td colspan="4" class="text-center">Загрузка...</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>

<div class="modal fade" id="libraryModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="libraryModalTitle">Редактировать материал</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form id="editLibraryForm">
                <input type="hidden" id="editLibId">
                <div class="mb-2"><label class="form-label">Название</label><input type="text" class="form-control" id="editLibTitle" required></div>
                <div class="mb-2"><label class="form-label">Описание</label><textarea class="form-control" id="editLibDesc" rows="2"></textarea></div>
                <div class="mb-2"><label class="form-label">Тип</label>
                    <select class="form-select" id="editLibType">
                        <option value="document">Документ</option>
                        <option value="presentation">Презентация</option>
                        <option value="code">Код</option>
                        <option value="video">Видео</option>
                    </select>
                </div>
                <div class="mb-2"><label class="form-label">Группа</label><select class="form-select" id="editLibGroup"><option value="">Все группы</option></select></div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            <button type="button" class="btn btn-danger" onclick="saveLibraryEdit()">Сохранить</button>
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
let libraryData = [], groupsData = [];
const libraryModal = new bootstrap.Modal(document.getElementById('libraryModal'));
async function loadLibrary() {
    const [libR, grpR] = await Promise.all([
        fetch(API_BASE + '?action=get_library').then(r=>r.json()),
        fetch(API_BASE + '?action=get_groups').then(r=>r.json())
    ]);
    libraryData = libR.success ? libR.data : [];
    groupsData = grpR.success ? grpR.data : [];
    const sel = document.getElementById('libGroup');
    sel.innerHTML = '<option value="">Все группы</option>' + groupsData.map(g=>`<option value="${g.name}">${g.name}</option>`).join('');
    const tb = document.getElementById('libraryBody');
    if(libraryData.length===0) { tb.innerHTML='<tr><td colspan="4" class="text-center">Нет материалов</td></tr>'; return; }
    tb.innerHTML = libraryData.map(l=>`
        <tr><td><strong>${l.title}</strong><br><small class="text-muted">${l.description||''}</small></td><td><span class="badge bg-info">${l.file_type||'document'}</span></td><td>${l.group_name||'Все'}</td>                        <td><button class="btn btn-sm btn-primary me-1" onclick="editLibrary(${l.id})"><i class="bi bi-pencil"></i></button><button class="btn btn-sm btn-danger" onclick="delLibrary(${l.id})"><i class="bi bi-trash"></i></button></td></tr>
    `).join('');
}
async function addLibrary() {
    const fd = new FormData();
    fd.append('action','add_library');
    fd.append('title',document.getElementById('libTitle').value);
    fd.append('description',document.getElementById('libDesc').value);
    fd.append('file_type',document.getElementById('libType').value);
    fd.append('group_name',document.getElementById('libGroup').value);
    const r = await fetch(API_BASE,{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Материал добавлен!','success'); document.getElementById('addLibraryForm').reset(); loadLibrary(); }
    else showToast(d.message||'Ошибка','error');
}

function editLibrary(id) {
    const l = libraryData.find(x => x.id === id);
    if (!l) return;
    document.getElementById('editLibId').value = l.id;
    document.getElementById('editLibTitle').value = l.title;
    document.getElementById('editLibDesc').value = l.description || '';
    document.getElementById('editLibType').value = l.file_type || 'document';
    const sel = document.getElementById('editLibGroup');
    sel.innerHTML = '<option value="">Все группы</option>' + groupsData.map(g=>`<option value="${g.name}">${g.name}</option>`).join('');
    sel.value = l.group_name || '';
    libraryModal.show();
}

async function saveLibraryEdit() {
    const fd = new FormData();
    fd.append('action','update_library');
    fd.append('id',document.getElementById('editLibId').value);
    fd.append('title',document.getElementById('editLibTitle').value);
    fd.append('description',document.getElementById('editLibDesc').value);
    fd.append('file_type',document.getElementById('editLibType').value);
    fd.append('group_name',document.getElementById('editLibGroup').value);
    const r = await fetch(API_BASE,{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Материал обновлен!','success'); libraryModal.hide(); loadLibrary(); }
    else showToast(d.message||'Ошибка','error');
}

async function delLibrary(id) {
    if(!confirm('Удалить?')) return;
    const fd = new FormData(); fd.append('action','delete_library'); fd.append('id',id);
    const r = await fetch(API_BASE,{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Удалено!','success'); loadLibrary(); }
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
loadLibrary();
</script>
</body>
</html>