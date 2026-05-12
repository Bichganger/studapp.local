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
    <title>Админка — Учеба24</title>
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
        .stat-card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="role-admin">

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-shield-lock"></i> Учеба24</h5>
        <small>Админ-панель</small>
    </div>
    <nav class="mt-3">
        <a href="dashboard.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="users.php"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
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
        <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
        <p class="mb-0">Вы вошли как <strong>Администратор</strong></p>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Пользователей</h6><h2 class="mb-0" id="statUsers">0</h2></div>
                    <i class="bi bi-people display-4 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Групп</h6><h2 class="mb-0" id="statGroups">0</h2></div>
                    <i class="bi bi-people-fill display-4 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Предметов</h6><h2 class="mb-0" id="statSubjects">0</h2></div>
                    <i class="bi bi-journal display-4 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted mb-1">Уведомлений</h6><h2 class="mb-0" id="statNotifications">0</h2></div>
                    <i class="bi bi-bell display-4 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-people me-2"></i>Последние пользователи</div>
                <div class="card-body"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Имя</th><th>Роль</th><th>Группа</th></tr></thead><tbody id="recentUsers"><tr><td colspan="3" class="text-center">Загрузка...</td></tr></tbody></table></div></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-people-fill me-2"></i>Группы</div>
                <div class="card-body"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Группа</th><th>Специальность</th><th>Студентов</th></tr></thead><tbody id="groupsList"><tr><td colspan="3" class="text-center">Загрузка...</td></tr></tbody></table></div></div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24. Все права защищены.</footer>

<div class="modal fade" id="accessibilityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-universal-access"></i> Доступность</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
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
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="simplified" id="simplified"><label class="form-check-label" for="simplified">Упрощённый интерфейс</label></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Назад</button>
                <button type="button" class="btn btn-danger" onclick="saveAccessibility()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
async function loadStats() {
    try {
        const [u, g, s, n] = await Promise.all([
            fetch('../api/sync.php?action=get_users').then(r=>r.json()),
            fetch('../api/sync.php?action=get_groups').then(r=>r.json()),
            fetch('../api/sync.php?action=get_schedule').then(r=>r.json()),
            fetch('../api/sync.php?action=get_notifications').then(r=>r.json())
        ]);
        
        const users = u.success ? u.data : [];
        const groups = g.success ? g.data : [];
        const schedule = s.success ? s.data : [];
        const notifications = n.success ? n.data : [];
        
        document.getElementById('statUsers').textContent = users.length;
        document.getElementById('statGroups').textContent = groups.length;
        document.getElementById('statSubjects').textContent = schedule.length;
        document.getElementById('statNotifications').textContent = notifications.length;
        
        const recentUsersBody = document.getElementById('recentUsers');
        if (users.length > 0) {
            recentUsersBody.innerHTML = users.slice(0,5).map(u => `
                <tr><td>${u.full_name}</td><td><span class="badge bg-${u.role==='admin'?'danger':u.role==='teacher'?'info':'success'}">${u.role}</span></td><td>${u.group_name||'-'}</td></tr>
            `).join('');
        } else {
            recentUsersBody.innerHTML = '<tr><td colspan="3" class="text-center">Нет пользователей</td></tr>';
        }
        
        const groupsBody = document.getElementById('groupsList');
        if (groups.length > 0) {
            groupsBody.innerHTML = groups.map(g => `
                <tr><td><strong>${g.name}</strong></td><td>${g.specialty}</td><td><span class="badge bg-success">${g.student_count||0}</span></td></tr>
            `).join('');
        } else {
            groupsBody.innerHTML = '<tr><td colspan="3" class="text-center">Нет групп</td></tr>';
        }
    } catch (e) { console.error(e); }
}

function saveAccessibility() {
    const settings = {
        fontSize: localStorage.getItem('a11y_fontSize') || '100',
        highContrast: document.getElementById('highContrast').checked,
        largeButtons: document.getElementById('largeButtons').checked,
        simplified: document.getElementById('simplified').checked
    };
    localStorage.setItem('accessibility_settings', JSON.stringify(settings));
    showToast('Настройки доступности сохранены!', 'success');
    bootstrap.Modal.getInstance(document.getElementById('accessibilityModal')).hide();
}

function showToast(msg, type) {
    const c = document.querySelector('.toast-container');
    const t = document.createElement('div');
    t.className = `toast align-items-center text-white bg-${type==='success'?'success':type==='error'?'danger':'primary'} border-0`;
    t.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    c.appendChild(t);
    new bootstrap.Toast(t,{delay:3000}).show();
    t.addEventListener('hidden.bs.toast',()=>t.remove());
}

loadStats();
</script>
</body>
</html>