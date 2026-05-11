<?php
session_start();
require_once '../protected/auth_guard.php';
if ($_SESSION['role'] !== 'student') { header("Location: ../dashboard.php"); exit; }
$name = htmlspecialchars($_SESSION['full_name']);
$group = $_SESSION['group_name'] ?? 'ПИ-21';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Расписание — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-family: sans-serif; }
        .sidebar { min-height: 100vh; background: #1a202c; color: white; position: fixed; width: 240px; left: 0; top: 0; }
        .sidebar-header { background: #0d1b2a; padding: 20px; text-align: center; border-bottom: 3px solid #198754; }
        .sidebar-header h5 { color: #fff; font-weight: 700; margin: 0; }
        .sidebar-header small { color: #198754; display: block; margin-top: 5px; }
        .sidebar a { color: #d8dee9; margin: 5px 10px; border-radius: 5px; display: block; padding: 10px 15px; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background: #198754; color: white; }
        .main-content { margin-left: 240px; padding: 20px; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: none; }
        .card-header { background: linear-gradient(135deg, #198754 0%, #20c997 100%); color: white; border: none; }
        .schedule-item { padding: 15px; margin: 10px 0; background: rgba(255,255,255,0.95); border-radius: 10px; border-left: 4px solid #198754; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">
        <h5 class="mb-0"><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <small>Кабинет студента</small>
    </div>
    <nav class="mt-3">
        <a href="panel.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="schedule.php" class="active"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php"><i class="bi bi-file-text me-2"></i>Работы</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <hr class="mx-2">
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 rounded shadow-sm mb-4" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); color: white; border-radius: 15px;">
        <h3><i class="bi bi-calendar-week"></i> Расписание занятий</h3>
        <p>Группа: <strong><?= $group ?></strong></p>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="bi bi-list-ul"></i> Ваше расписание</h5></div>
        <div class="card-body">
            <div id="scheduleContainer">
                <div class="text-center text-muted">Загрузка...</div>
            </div>
        </div>
    </div>
</main>

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
    </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sync.js"></script>
<script src="../assets/js/accessibility.js"></script>
<script>
// ДАННЫЕ ЗАГРУЖАЮТСЯ СРАЗУ!
const userGroup = '<?= $group ?>';

function loadSchedule() {
    const container = document.getElementById('scheduleContainer');
    if (!container) return;
    
    // Получаем расписание из Sync (данные уже загружены!)
    const allSchedule = window.Sync.getSchedule();
    const groupSchedule = allSchedule.filter(s => s.group_name === userGroup);
    
    console.log('Загружено расписания:', groupSchedule.length);
    
    if (groupSchedule.length === 0) {
        container.innerHTML = '<div class="text-center text-muted"><i class="bi bi-infinity display-4"></i><p class="mt-3">Расписание не найдено</p></div>';
        return;
    }
    
    // Группируем по дням
    const days = ['понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'];
    const byDay = {};
    days.forEach(d => byDay[d] = []);
    groupSchedule.forEach(item => {
        if (byDay[item.day_of_week]) {
            byDay[item.day_of_week].push(item);
        }
    });
    
    let html = '';
    days.forEach(day => {
        if (byDay[day].length > 0) {
            html += `<h6 class="mt-4 mb-3 text-white"><i class="bi bi-calendar-event"></i> ${day.charAt(0).toUpperCase() + day.slice(1)}</h6>`;
            byDay[day].forEach(item => {
                html += `
                    <div class="schedule-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1"><strong>${item.subject}</strong></h6>
                                <small class="text-muted"><i class="bi bi-clock"></i> ${item.start_time} - ${item.end_time}</small>
                                <small class="text-muted ms-3"><i class="bi bi-person"></i> ${item.teacher_name || '-'}</small>
                                <small class="text-muted ms-3"><i class="bi bi-geo-alt"></i> ${item.classroom || '-'}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
    });
    
    container.innerHTML = html;
}

// Загружаем сразу при загрузке страницы
loadSchedule();
</script>
</body>
</html>
