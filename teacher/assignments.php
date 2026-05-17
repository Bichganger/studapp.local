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
    <title>Работы — Учеба24</title>
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
        <a href="journal.php"><i class="bi bi-journal-text me-2"></i>Журнал</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php" class="active"><i class="bi bi-file-text me-2"></i>Работы</a>
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
        <h3><i class="bi bi-file-earmark-check"></i> Проверка работ</h3>
        <p class="mb-0">Просмотр и оценка работ студентов</p>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-ul me-2"></i>Работы студентов</span>
            <select class="form-select form-select-sm w-auto" id="filterGroup" onchange="load()">
                <option value="">Все группы</option>
            </select>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Студент</th><th>Группа</th><th>Предмет</th><th>Название</th><th>Статус</th><th>Действия</th></tr></thead>
                    <tbody id="assignBody"><tr><td colspan="6" class="text-center">Загрузка...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>

<!-- Модалка проверки -->
<div class="modal fade" id="checkModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Проверка работы</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" id="checkId">
            <div class="mb-3"><label class="form-label">Статус</label>
                <select class="form-select" id="checkStatus">
                    <option value="accepted">Принято</option>
                    <option value="rejected">Отклонено</option>
                </select>
            </div>
            <div class="mb-3"><label class="form-label">Комментарий</label><textarea class="form-control" id="checkComment" rows="3"></textarea></div>
            <div class="mb-3"><label class="form-label">Оценка</label>
                <select class="form-select" id="checkGrade">
                    <option value="">Без оценки</option>
                    <option value="5">5 (Отлично)</option>
                    <option value="4">4 (Хорошо)</option>
                    <option value="3">3 (Удовл.)</option>
                    <option value="2">2 (Неуд.)</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            <button type="button" class="btn btn-info" onclick="submitCheck()">Проверить</button>
        </div>
    </div></div>
</div>

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
let allAssignments = [];
let allGroups = [];

async function load() {
    try {
        const [a, g] = await Promise.all([
            fetch(API_BASE + '?action=get_assignments').then(r=>r.json()),
            fetch(API_BASE + '?action=get_groups').then(r=>r.json())
        ]);
        allAssignments = a.success ? a.data : [];
        allGroups = g.success ? g.data : [];
        
        // Заполнить фильтр групп
        const filterSel = document.getElementById('filterGroup');
        const currentFilter = filterSel.value;
        filterSel.innerHTML = '<option value="">Все группы</option>' + allGroups.map(gr=>`<option value="${gr.name}">${gr.name}</option>`).join('');
        filterSel.value = currentFilter;
        
        // Фильтрация
        const groupFilter = filterSel.value;
        const assigns = groupFilter ? allAssignments.filter(x => x.group_name === groupFilter) : allAssignments;
        
        const tb = document.getElementById('assignBody');
        if(assigns.length===0) { tb.innerHTML='<tr><td colspan="6" class="text-center">Нет работ</td></tr>'; return; }
        tb.innerHTML = assigns.map(a=>{
            const st = a.status==='pending'?'<span class="badge bg-warning">На проверке</span>':
                       a.status==='graded'?'<span class="badge bg-success">Проверено</span>':
                       a.status==='accepted'?'<span class="badge bg-info">Принято</span>':
                       a.status==='rejected'?'<span class="badge bg-danger">Отклонено</span>':
                       '<span class="badge bg-secondary">Нет статуса</span>';
            return `<tr>
                <td>${a.student_name||'-'}</td>
                <td>${a.group_name||'-'}</td>
                <td>${a.subject||'-'}</td>
                <td>${a.title||'-'}</td>
                <td>${st}</td>
                <td><button class="btn btn-sm btn-info" onclick="openCheck(${a.id})"><i class="bi bi-check-circle"></i> Проверить</button></td>
            </tr>`;
        }).join('');
    } catch(e) { console.error(e); }
}
function openCheck(id) {
    document.getElementById('checkId').value = id;
    document.getElementById('checkStatus').value = 'accepted';
    document.getElementById('checkComment').value = '';
    document.getElementById('checkGrade').value = '';
    new bootstrap.Modal(document.getElementById('checkModal')).show();
}
async function submitCheck() {
    const fd = new FormData();
    fd.append('action','update_assignment');
    fd.append('id',document.getElementById('checkId').value);
    fd.append('status',document.getElementById('checkStatus').value);
    fd.append('teacher_comment',document.getElementById('checkComment').value);
    fd.append('grade',document.getElementById('checkGrade').value);
    const r = await fetch(API_BASE,{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Проверено!','success'); bootstrap.Modal.getInstance(document.getElementById('checkModal')).hide(); load(); }
    else showToast(d.message||'Ошибка','error');
}
load();
</script>
</body>
</html>