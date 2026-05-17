<?php
session_start();
require_once '../protected/auth_guard.php';
if ($_SESSION['role'] !== 'admin') { header('Location: ../dashboard.php'); exit; }
$pageTitle = 'Админ-панель';
require_once '../includes/header.php';
$user = getCurrentUser();
?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Администрирование</span>
            <h1 class="section-title">Панель управления</h1>
            <p class="section-subtitle">Добро пожаловать, <?= e($user['name']) ?>!</p>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-md-3"><div class="stat-card"><div class="stat-number" id="statUsers">0</div><div class="stat-label">Пользователей</div></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number" id="statGroups">0</div><div class="stat-label">Групп</div></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number" id="statSubjects">0</div><div class="stat-label">Предметов</div></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number" id="statNotifications">0</div><div class="stat-label">Уведомлений</div></div></div>
        </div>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card"><div class="card-body"><h5><i class="bi bi-people me-2"></i>Пользователи</h5><div class="table-responsive"><table class="table table-hover"><thead><tr><th>Имя</th><th>Роль</th><th>Группа</th><th>Действия</th></tr></thead><tbody id="usersList"><tr><td colspan="4" class="text-center">Загрузка...</td></tr></tbody></table></div></div></div>
            </div>
            <div class="col-lg-4">
                <div class="card"><div class="card-body"><h5><i class="bi bi-gear me-2"></i>Быстрые действия</h5><div class="d-grid gap-2 mt-3">
                    <a href="users.php" class="btn btn-outline-light"><i class="bi bi-people me-2"></i>Управление пользователями</a>
                    <a href="groups.php" class="btn btn-outline-light"><i class="bi bi-people-fill me-2"></i>Управление группами</a>
                    <a href="schedule.php" class="btn btn-outline-light"><i class="bi bi-calendar me-2"></i>Расписание</a>
                    <a href="library.php" class="btn btn-outline-light"><i class="bi bi-journal me-2"></i>Библиотека</a>
                    <a href="map.php" class="btn btn-outline-light"><i class="bi bi-geo-alt me-2"></i>Карта</a>
                    <a href="teachers.php" class="btn btn-outline-light"><i class="bi bi-people me-2"></i>Преподаватели</a>
                    <a href="tips.php" class="btn btn-outline-light"><i class="bi bi-lightbulb me-2"></i>Советы</a>
                    <a href="../logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
                </div></div></div>
            </div>
        </div>
    </div>
</section>
<script src="../assets/js/api-config.js"></script>
<script>
async function loadStats() {
    try {
        const [u, g, s, n] = await Promise.all([
            fetch(API_BASE + '?action=get_users').then(r=>r.json()),
            fetch(API_BASE + '?action=get_groups').then(r=>r.json()),
            fetch(API_BASE + '?action=get_schedule').then(r=>r.json()),
            fetch(API_BASE + '?action=get_notifications').then(r=>r.json())
        ]);
        const users = u.success ? u.data : [];
        const groups = g.success ? g.data : [];
        const schedule = s.success ? s.data : [];
        const notifications = n.success ? n.data : [];
        document.getElementById('statUsers').textContent = users.length;
        document.getElementById('statGroups').textContent = groups.length;
        document.getElementById('statSubjects').textContent = schedule.length;
        document.getElementById('statNotifications').textContent = notifications.length;
        const usersBody = document.getElementById('usersList');
        if (users.length > 0) {
            usersBody.innerHTML = users.slice(0,8).map(u => `<tr><td><?= e($user['name']) ?></td><td><span class="badge bg-${u.role==='admin'?'danger':u.role==='teacher'?'info':'success'}">${u.role}</span></td><td>${u.group_name||'-'}</td><td><button class="btn btn-sm btn-outline-light">Ред.</button></td></tr>`).join('');
        } else { usersBody.innerHTML = '<tr><td colspan="4" class="text-center">Нет пользователей</td></tr>'; }
    } catch (e) { console.error(e); }
}
loadStats();
</script>
<?php require_once '../includes/footer.php'; ?>