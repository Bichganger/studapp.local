<?php
session_start();
require_once '../protected/auth_guard.php';
if ($_SESSION['role'] !== 'student') { header("Location: ../dashboard.php"); exit; }
$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабинет студента — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-common.css">
    <style>
        .sidebar-header { border-bottom-color: #198754; }
        .sidebar-header small { color: #198754; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(90deg, #198754 0%, #146c43 100%); }
        .welcome-card { background: linear-gradient(135deg, #198754 0%, #146c43 100%); color: white; }
        .card-header { background: linear-gradient(135deg, #198754 0%, #146c43 100%); color: white; }
        .table thead th { background: linear-gradient(135deg, #198754 0%, #146c43 100%); color: white; }
        .btn-success { background: linear-gradient(135deg, #198754 0%, #146c43 100%); border: none; }
        .stat-card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="role-student">

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <small>Кабинет студента</small>
    </div>
    <nav class="mt-3">
        <a href="panel.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php"><i class="bi bi-file-text me-2"></i>Мои работы</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
        <p class="mb-0">Вы вошли как <strong>Студент</strong></p>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Занятий сегодня</h6><h2 class="mb-0" id="statClasses">0</h2></div>
                    <i class="bi bi-calendar display-4 text-success opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Оценок</h6><h2 class="mb-0" id="statGrades">0</h2></div>
                    <i class="bi bi-star display-4 text-success opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Работ</h6><h2 class="mb-0" id="statAssigns">0</h2></div>
                    <i class="bi bi-file-text display-4 text-success opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Уведомлений</h6><h2 class="mb-0" id="statNotifs">0</h2></div>
                    <i class="bi bi-bell display-4 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-calendar me-2"></i>Сегодня</div>
                <div class="card-body"><div id="todayList" class="list-group list-group-flush">Загрузка...</div></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-star me-2"></i>Последние оценки</div>
                <div class="card-body"><div id="gradesList" class="list-group list-group-flush">Загрузка...</div></div>
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
async function loadStats() {
    console.log('=== loadStats start ===');
    try {
        const urls = [
            API_BASE + '?action=get_schedule',
            API_BASE + '?action=get_grades',
            API_BASE + '?action=get_assignments',
            API_BASE + '?action=get_notifications'
        ];
        
        const responses = await Promise.all(urls.map(async (url) => {
            console.log('Fetching:', url);
            const r = await fetch(url);
            console.log('Response status:', r.status, url);
            if (!r.ok) {
                console.error('HTTP error:', r.status, url);
                return { success: false, error: 'HTTP ' + r.status };
            }
            const text = await r.text();
            console.log('Raw response:', text.substring(0, 200), url);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e, url);
                return { success: false, error: 'JSON parse error' };
            }
        }));
        
        const [s, g, a, n] = responses;
        console.log('API results:', { schedule: s.success, grades: g.success, assigns: a.success, notifs: n.success });
        
        const schedule = s.success ? s.data : [];
        const grades = g.success ? g.data : [];
        const assigns = a.success ? a.data : [];
        const notifs = n.success ? n.data : [];
        const today = new Date().toLocaleDateString('ru-RU',{weekday:'long'}).toLowerCase();
        const todayClasses = schedule.filter(x=>x.day_of_week?.toLowerCase()===today);

        document.getElementById('statClasses').textContent = todayClasses.length;
        document.getElementById('statGrades').textContent = grades.length;
        document.getElementById('statAssigns').textContent = assigns.length;
        document.getElementById('statNotifs').textContent = notifs.length;

        const tl = document.getElementById('todayList');
        tl.innerHTML = todayClasses.length===0 ? '<div class="list-group-item text-muted">Сегодня нет занятий</div>' :
            todayClasses.map(x=>`<div class="list-group-item"><strong>${x.subject}</strong><br><small class="text-muted">${(x.start_time||'').substring(0,5)}-${(x.end_time||'').substring(0,5)} | Каб. ${x.classroom}</small></div>`).join('');

        const gl = document.getElementById('gradesList');
        gl.innerHTML = grades.length===0 ? '<div class="list-group-item text-muted">Нет оценок</div>' :
            grades.slice(0,5).map(x=>`<div class="list-group-item d-flex justify-content-between"><span>${x.subject}</span><span class="badge bg-${x.grade>=4?'success':x.grade==3?'warning':'danger'}">${x.grade}</span></div>`).join('');
    } catch(e) { 
        console.error('loadStats error:', e); 
    }
    console.log('=== loadStats end ===');
}

// Запускаем загрузку данных
loadStats();
</script>
</body>
</html>