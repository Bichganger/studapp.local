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
    <title>Расписание — Учеба24</title>
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
        <a href="groups.php"><i class="bi bi-people me-2"></i>Группы</a>
        <a href="schedule.php" class="active"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-calendar-week"></i> Расписание занятий</h3>
        <p class="mb-0">Просмотр и добавление занятий</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Добавить занятие</div>
                <div class="card-body">
                    <form id="scheduleForm">
                        <div class="mb-2"><label class="form-label">Группа</label><select class="form-select" id="sGroup" required></select></div>
                        <div class="mb-2"><label class="form-label">Предмет</label><input type="text" class="form-control" id="sSubject" required></div>
                        <div class="mb-2"><label class="form-label">Преподаватель</label><input type="text" class="form-control" id="sTeacher" value="<?= $name ?>" required></div>
                        <div class="mb-2"><label class="form-label">День недели</label>
                            <select class="form-select" id="sDay" required>
                                <option value="понедельник">Понедельник</option>
                                <option value="вторник">Вторник</option>
                                <option value="среда">Среда</option>
                                <option value="четверг">Четверг</option>
                                <option value="пятница">Пятница</option>
                            </select>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6"><label class="form-label">Начало</label><input type="time" class="form-control" id="sStart" required></div>
                            <div class="col-6"><label class="form-label">Конец</label><input type="time" class="form-control" id="sEnd" required></div>
                        </div>
                        <div class="mb-2"><label class="form-label">Кабинет</label><input type="text" class="form-control" id="sRoom" placeholder="301" required></div>
                        <button type="button" class="btn btn-info w-100" onclick="addSchedule()">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-list-ul me-2"></i>Расписание</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>День</th><th>Группа</th><th>Предмет</th><th>Время</th><th>Кабинет</th></tr></thead>
                            <tbody id="scheduleBody"><tr><td colspan="5" class="text-center">Загрузка...</td></tr></tbody>
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
        const [s,g] = await Promise.all([
            fetch(API_BASE + '?action=get_schedule').then(r=>r.json()),
            fetch(API_BASE + '?action=get_groups').then(r=>r.json())
        ]);
        const schedule = s.success ? s.data : [];
        const groups = g.success ? g.data : [];
        const today = new Date().toLocaleDateString('ru-RU',{weekday:'long'}).toLowerCase();
        const todayClasses = schedule.filter(x=>x.day_of_week?.toLowerCase()===today);

        const tl = document.getElementById('todayList');
        if(todayClasses.length===0) { tl.innerHTML='<div class="list-group-item text-muted">Сегодня нет занятий</div>'; return; }
        tl.innerHTML = todayClasses.map(x=>{
            const st=(x.start_time||'').substring(0,5);
            const et=(x.end_time||'').substring(0,5);
            return `<div class="list-group-item"><strong>${x.subject}</strong><br><small class="text-muted">${x.group_name} | ${st}-${et} | Каб. ${x.classroom}</small></div>`;
        }).join('');

        const tb = document.getElementById('weekBody');
        const days = ['понедельник','вторник','среда','четверг','пятница','суббота'];
        tb.innerHTML = days.map(d=>{
            const dayClasses = schedule.filter(x=>x.day_of_week?.toLowerCase()===d);
            if(dayClasses.length===0) return '';
            return `<tr><td class="fw-bold text-capitalize">${d}</td><td>`+dayClasses.map(x=>{
                const st=(x.start_time||'').substring(0,5);
                const et=(x.end_time||'').substring(0,5);
                return `<div class="mb-1"><strong>${x.subject}</strong> — ${x.group_name}<br><small class="text-muted">${x.teacher_name} | ${st}-${et} | Каб. ${x.classroom}</small></div>`;
            }).join('')+`</td></tr>`;
        }).filter(Boolean).join('');
    } catch(e) { console.error(e); }
}
load();
</script>
</body>
</html>