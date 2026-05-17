<?php
session_start();
require_once '../protected/auth_guard.php';
if ($_SESSION['role'] !== 'teacher') { header('Location: ../dashboard.php'); exit; }
$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Журнал — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sidebar-header { border-bottom-color: var(--info); }
        .sidebar-header small { color: var(--info); }
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
        <a href="journal.php" class="active"><i class="bi bi-journal-text me-2"></i>Журнал</a>
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
        <h3><i class="bi bi-journal-text"></i> Журнал посещаемости</h3>
        <p class="mb-0">Учёт посещаемости занятий</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Отметить посещаемость</div>
                <div class="card-body">
                    <form id="journalForm">
                        <div class="mb-2"><label class="form-label">Группа</label><select class="form-select" id="jGroup" onchange="onGroupChange()" required></select></div>
                        <div class="mb-2"><label class="form-label">Студент</label><select class="form-select" id="jStudent" required><option value="">Сначала выберите группу</option></select></div>
                        <div class="mb-2"><label class="form-label">Предмет</label><input type="text" class="form-control" id="jSubject" required></div>
                        <div class="mb-2"><label class="form-label">Дата</label><input type="date" class="form-control" id="jDate" required></div>
                        <div class="mb-2"><label class="form-label">Статус</label>
                            <select class="form-select" id="jStatus">
                                <option value="present">Присутствовал</option>
                                <option value="absent">Отсутствовал</option>
                                <option value="sick">Болеет</option>
                                <option value="late">Опоздал</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-info w-100" onclick="addJournal()">Отметить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-list-ul me-2"></i>Записи журнала</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Дата</th><th>Студент</th><th>Группа</th><th>Предмет</th><th>Статус</th></tr></thead>
                            <tbody id="journalBody"><tr><td colspan="5" class="text-center">Загрузка...</td></tr></tbody>
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
<script src="../assets/js/api-config.js"></script>
<script src="../assets/js/accessibility.js"></script>
<script>
async function load() {
    try {
        const [g,j] = await Promise.all([
            fetch(API_BASE + '?action=get_groups').then(r=>r.json()),
            fetch(API_BASE + '?action=get_journal').then(r=>r.json())
        ]);
        const groups = g.success ? g.data : [];
        const journal = j.success ? j.data : [];
        
        const jg = document.getElementById('jGroup');
        jg.innerHTML = '<option value="">Выберите группу</option>' + groups.map(gr=>`<option value="${gr.name}">${gr.name}</option>`).join('');
        
        const tb = document.getElementById('journalBody');
        if(journal.length===0) { tb.innerHTML='<tr><td colspan="5" class="text-center">Нет записей</td></tr>'; return; }
        tb.innerHTML = journal.map(r=>`
            <tr>
                <td>${r.date||'-'}</td>
                <td>${r.student_name||'-'}</td>
                <td>${r.group_name||'-'}</td>
                <td>${r.subject||'-'}</td>
                <td>${r.status==='present'?'<span class="badge bg-success">Присутствовал</span>':r.status==='absent'?'<span class="badge bg-danger">Отсутствовал</span>':r.status==='sick'?'<span class="badge bg-warning">Болеет</span>':r.status==='late'?'<span class="badge bg-info">Опоздал</span>':'<span class="badge bg-secondary">-</span>'}</td>
            </tr>
        `).join('');
    } catch(e) { console.error(e); }
}

async function onGroupChange() {
    const group = document.getElementById('jGroup').value;
    const studentSel = document.getElementById('jStudent');
    if(!group) { studentSel.innerHTML='<option value="">Сначала выберите группу</option>'; return; }
    studentSel.innerHTML = '<option value="">Выберите студента</option>';
    try {
        const r = await fetch(API_BASE + '?action=get_users_by_group&group_name=' + encodeURIComponent(group)).then(r=>r.json());
        const studs = r.success ? r.data : [];
        studentSel.innerHTML = '<option value="">Выберите студента</option>' + studs.map(s=>`<option value="${s.full_name}">${s.full_name}</option>`).join('');
    } catch(e) { console.error(e); }
}

async function addJournal() {
    const student = document.getElementById('jStudent').value;
    const group = document.getElementById('jGroup').value;
    const subject = document.getElementById('jSubject').value.trim();
    const date = document.getElementById('jDate').value;
    const status = document.getElementById('jStatus').value;
    if(!student||!group||!subject||!date) { showToast('Заполните все поля!','error'); return; }
    const fd = new FormData();
    fd.append('action','add_journal');
    fd.append('student_name',student);
    fd.append('group_name',group);
    fd.append('subject',subject);
    fd.append('date',date);
    fd.append('status',status);
    const r = await fetch(API_BASE,{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Запись добавлена!','success'); document.getElementById('journalForm').reset(); load(); }
    else showToast(d.message||'Ошибка','error');
}

load();
</script>
</body>
</html>