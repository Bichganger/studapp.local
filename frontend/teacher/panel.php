<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] != 'teacher') {
    header("Location: ../dashboard.php");
    exit;
}

$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Кабинет — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/teacher-style.css">
    <link rel="stylesheet" href="../assets/css/accessibility.css">
    <style>
        body { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .sidebar { background: #1a202c; border-bottom: 3px solid #0dcaf0; }
        .sidebar-header { border-bottom: 3px solid #0dcaf0; }
        .sidebar-header small { color: #0dcaf0; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(90deg, #0dcaf0 0%, #0bb5d6 100%); }
        .welcome-card { background: linear-gradient(135deg, #0dcaf0 0%, #0bb5d6 100%); }
    </style>
</head>
<body class="role-teacher">

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-person-badge"></i> Учеба24</h5>
        <small>Кабинет преподавателя</small>
    </div>
    <nav class="mt-3">
        <a href="panel.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="journal.php"><i class="bi bi-journal me-2"></i>Журнал</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php"><i class="bi bi-file-text me-2"></i>Работы</a>
        <a href="groups.php"><i class="bi bi-people me-2"></i>Группы</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-2">
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card rounded shadow-sm mb-4">
        <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
        <p>Вы вошли как <strong>Преподаватель</strong></p>
    </div>
    
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-journal-text display-4 text-info"></i>
                    <h5 class="mt-2">Журнал</h5>
                    <p class="text-muted small mb-0">Посещаемость</p>
                    <a href="journal.php" class="btn btn-sm btn-info mt-2 text-white">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-star display-4 text-warning"></i>
                    <h5 class="mt-2">Оценки</h5>
                    <p class="text-muted small mb-0">Выставление</p>
                    <a href="grades.php" class="btn btn-sm btn-warning mt-2 text-white">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-text display-4 text-primary"></i>
                    <h5 class="mt-2">Работы</h5>
                    <p class="text-muted small mb-0">Проверка</p>
                    <a href="assignments.php" class="btn btn-sm btn-primary mt-2">Открыть</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-bell"></i> Последние уведомления</h5></div>
                <div class="card-body">
                    <div id="teacherNotificationsContainer">
                        <div class="text-center text-muted">Загрузка...</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-file-earmark-check"></i> На проверке</h5></div>
                <div class="card-body">
                    <div id="teacherAssignmentsContainer">
                        <div class="text-center text-muted">Загрузка...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>

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
                    <button class="btn btn-outline-info" data-a11y="fontSize" data-value="100">100%</button>
                    <button class="btn btn-outline-info" data-a11y="fontSize" data-value="125">125%</button>
                    <button class="btn btn-outline-info" data-a11y="fontSize" data-value="150">150%</button>
                    <button class="btn btn-outline-info" data-a11y="fontSize" data-value="200">200%</button>
                </div>
                <h6>Визуальный режим</h6>
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="highContrast" id="highContrast"><label class="form-check-label" for="highContrast">Высокая контрастность</label></div>
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="largeButtons" id="largeButtons"><label class="form-check-label" for="largeButtons">Увеличенные кнопки</label></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sync.js"></script>
<script src="../assets/js/accessibility.js"></script>
<script>
async function loadTeacherData() {
    const notifContainer = document.getElementById('teacherNotificationsContainer');
    const notifications = await Sync.getNotifications();
    let html = '<ul class="list-group">';
    notifications.slice(0, 5).forEach(n => {
        html += `<li class="list-group-item"><strong>${n.title}</strong><p class="mb-0 small text-muted">${n.message}</p></li>`;
    });
    html += '</ul>';
    notifContainer.innerHTML = html;

    const assignContainer = document.getElementById('teacherAssignmentsContainer');
    const assignments = await Sync.getAssignments();
    const pending = assignments.filter(a => a.status === 'pending');
    html = '<ul class="list-group">';
    if (pending.length === 0) {
        html += '<li class="list-group-item text-muted">Нет работ на проверке</li>';
    } else {
        pending.forEach(a => {
            html += `<li class="list-group-item"><strong>${a.title}</strong><br><small class="text-muted">${a.student_name} — ${a.subject}</small></li>`;
        });
    }
    html += '</ul>';
    assignContainer.innerHTML = html;
}
loadTeacherData();
</script>
</body>
</html>