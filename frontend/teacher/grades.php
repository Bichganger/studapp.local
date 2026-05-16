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
    <title>Оценки — Учеба24</title>
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
        <a href="grades.php" class="active"><i class="bi bi-star me-2"></i>Оценки</a>
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
        <h3><i class="bi bi-star"></i> Управление оценками</h3>
        <p class="mb-0">Выставление и просмотр оценок студентов</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Выставить оценку</div>
                <div class="card-body">
                    <form id="gradeForm">
                        <div class="mb-2"><label class="form-label">Группа</label><select class="form-select" id="gGroup" onchange="onGroupChange()" required></select></div>
                        <div class="mb-2"><label class="form-label">Студент</label><select class="form-select" id="gStudent" required><option value="">Сначала выберите группу</option></select></div>
                        <div class="mb-2"><label class="form-label">Предмет</label><input type="text" class="form-control" id="gSubject" required></div>
                        <div class="mb-2"><label class="form-label">Оценка</label>
                            <select class="form-select" id="gGrade" required>
                                <option value="5">5 (Отлично)</option>
                                <option value="4">4 (Хорошо)</option>
                                <option value="3">3 (Удовл.)</option>
                                <option value="2">2 (Неуд.)</option>
                            </select>
                        </div>
                        <div class="mb-2"><label class="form-label">Дата</label><input type="date" class="form-control" id="gDate" required></div>
                        <button type="button" class="btn btn-info w-100" onclick="addGrade()">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-list-ul me-2"></i>Журнал оценок</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Дата</th><th>Студент</th><th>Группа</th><th>Предмет</th><th>Оценка</th></tr></thead>
                            <tbody id="gradesBody"><tr><td colspan="5" class="text-center">Загрузка...</td></tr></tbody>
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
        const [r, g] = await Promise.all([
            fetch(API_BASE + '?action=get_grades').then(r=>r.json()),
            fetch(API_BASE + '?action=get_groups').then(r=>r.json())
        ]);
        const grades = r.success ? r.data : [];
        const groups = g.success ? g.data : [];
        
        const gg = document.getElementById('gGroup');
        gg.innerHTML = '<option value="">Выберите группу</option>' + groups.map(gr=>`<option value="${gr.name}">${gr.name}</option>`).join('');
        
        const tb = document.getElementById('gradesBody');
        if(grades.length===0) { tb.innerHTML='<tr><td colspan="5" class="text-center">Нет оценок</td></tr>'; return; }
        tb.innerHTML = grades.map(gr=>`
            <tr>
                <td>${gr.date||'-'}</td>
                <td>${gr.student_name||'-'}</td>
                <td>${gr.group_name||'-'}</td>
                <td>${gr.subject||'-'}</td>
                <td><span class="badge bg-${gr.grade>=4?'success':gr.grade==3?'warning':'danger'}">${gr.grade}</span></td>
            </tr>
        `).join('');
    } catch(e) { console.error(e); }
}

async function onGroupChange() {
    const group = document.getElementById('gGroup').value;
    const studentSel = document.getElementById('gStudent');
    if(!group) { studentSel.innerHTML='<option value="">Сначала выберите группу</option>'; return; }
    studentSel.innerHTML = '<option value="">Выберите студента</option>';
    try {
        const r = await fetch(API_BASE + '?action=get_users_by_group&group_name=' + encodeURIComponent(group)).then(r=>r.json());
        const studs = r.success ? r.data : [];
        studentSel.innerHTML = '<option value="">Выберите студента</option>' + studs.map(s=>`<option value="${s.full_name}">${s.full_name}</option>`).join('');
    } catch(e) { console.error(e); }
}

async function addGrade() {
    const fd = new FormData();
    fd.append('action','add_grade');
    fd.append('student_name',document.getElementById('gStudent').value);
    fd.append('group_name',document.getElementById('gGroup').value);
    fd.append('subject',document.getElementById('gSubject').value.trim());
    fd.append('grade',document.getElementById('gGrade').value);
    fd.append('date',document.getElementById('gDate').value);
    const r = await fetch(API_BASE,{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Оценка сохранена!','success'); document.getElementById('gradeForm').reset(); load(); }
    else showToast(d.message||'Ошибка','error');
}
load();
</script>
</body>
</html>