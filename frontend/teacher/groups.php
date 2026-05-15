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
    <title>Группы — Учеба24</title>
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
        <a href="groups.php" class="active"><i class="bi bi-people me-2"></i>Группы</a>
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-people"></i> Группы</h3>
        <p class="mb-0">Список учебных групп и студенты</p>
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-list-ul me-2"></i>Список групп</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Группа</th><th>Специальность</th><th>Курс</th><th>Студентов</th><th>Список</th></tr></thead>
                    <tbody id="groupsBody"><tr><td colspan="5" class="text-center">Загрузка...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>

<div class="modal fade" id="studentsModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Студенты группы</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><ul id="studentsList" class="list-group"></ul></div>
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
async function load() {
    try {
        const [g,u] = await Promise.all([
            fetch(API_BASE + '?action=get_groups').then(r=>r.json()),
            fetch(API_BASE + '?action=get_users').then(r=>r.json())
        ]);
        const groups = g.success ? g.data : [];
        const users = u.success ? u.data : [];
        const tb = document.getElementById('groupsBody');
        if(groups.length===0) { tb.innerHTML='<tr><td colspan="4" class="text-center">Нет групп</td></tr>'; return; }
        tb.innerHTML = groups.map(gr=>{
            const studs = users.filter(u=>u.group_name===gr.name && u.role==='student');
            return `<tr>
                <td>${gr.name}</td>
                <td>${gr.specialty||'-'}</td>
                <td>${gr.course||'-'}</td>
                <td><button class="btn btn-sm btn-info" onclick="viewStudents('${gr.name}')"><i class="bi bi-people"></i> ${studs.length}</button></td>
            </tr>`;
        }).join('');
    } catch(e) { console.error(e); }
}
function viewStudents(group) {
    fetch(API_BASE + '?action=get_users').then(r=>r.json()).then(d=>{
        const users = d.success ? d.data : [];
        const studs = users.filter(u=>u.group_name===group && u.role==='student');
        const tb = document.getElementById('studentsBody');
        if(studs.length===0) { tb.innerHTML='<tr><td colspan="2" class="text-center">Нет студентов</td></tr>'; }
        else { tb.innerHTML = studs.map(s=>`<tr><td>${s.full_name}</td><td>${s.email||'-'}</td></tr>`).join(''); }
        document.getElementById('studentsModalTitle').textContent = `Студенты группы ${group}`;
        new bootstrap.Modal(document.getElementById('studentsModal')).show();
    });
}
load();
</script>
</body>
</html>