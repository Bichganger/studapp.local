<?php
session_start();
require_once '../protected/auth_guard.php';
if ($_SESSION['role'] !== 'student') { header('Location: ../dashboard.php'); exit; }
$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои работы — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sidebar-header { border-bottom-color: var(--accent); }
        .sidebar-header small { color: var(--accent); }
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
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php" class="active"><i class="bi bi-file-text me-2"></i>Мои работы</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-file-earmark-text"></i> Мои работы</h3>
        <p class="mb-0">Отправка и просмотр работ</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Отправить работу</div>
                <div class="card-body">
                    <form id="assignForm">
                        <div class="mb-2"><label class="form-label">Предмет</label><input type="text" class="form-control" id="aSubject" required></div>
                        <div class="mb-2"><label class="form-label">Название</label><input type="text" class="form-control" id="aTitle" required></div>
                        <div class="mb-2"><label class="form-label">Описание</label><textarea class="form-control" id="aDesc" rows="3"></textarea></div>
                        <button type="button" class="btn btn-success w-100" onclick="submitWork()">Отправить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-list-ul me-2"></i>Мои работы</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Предмет</th><th>Название</th><th>Статус</th><th>Оценка</th><th>Комментарий</th></tr></thead>
                            <tbody id="assignBody"><tr><td colspan="5" class="text-center">Загрузка...</td></tr></tbody>
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
        const r = await fetch(API_BASE + '?action=get_assignments').then(r=>r.json());
        const assigns = r.success ? r.data : [];
        const tb = document.getElementById('assignBody');
        if(assigns.length===0) { tb.innerHTML='<tr><td colspan="5" class="text-center">Нет работ</td></tr>'; return; }
        tb.innerHTML = assigns.map(a=>{
            const st = a.status==='pending'?'<span class="badge bg-warning">На проверке</span>':
                       a.status==='graded'?'<span class="badge bg-success">Проверено</span>':
                       a.status==='accepted'?'<span class="badge bg-info">Принято</span>':
                       a.status==='rejected'?'<span class="badge bg-danger">Отклонено</span>':
                       '<span class="badge bg-secondary">Нет статуса</span>';
            return `<tr>
                <td>${a.subject||'-'}</td>
                <td>${a.title||'-'}</td>
                <td>${st}</td>
                <td>${a.grade||'-'}</td>
                <td>${a.comment||'-'}</td>
            </tr>`;
        }).join('');
    } catch(e) { console.error(e); }
}
async function submitWork() {
    const fd = new FormData();
    fd.append('action','add_assignment');
    fd.append('subject',document.getElementById('aSubject').value.trim());
    fd.append('title',document.getElementById('aTitle').value.trim());
    fd.append('description',document.getElementById('aDesc').value.trim());
    const r = await fetch(API_BASE,{method:'POST',body:fd});
    const d = await r.json();
    if(d.success) { showToast('Работа отправлена!','success'); document.getElementById('assignForm').reset(); load(); }
    else showToast(d.message||'Ошибка','error');
}
load();
</script>
</body>
</html>