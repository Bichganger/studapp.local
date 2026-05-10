<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] != 'student') {
    header("Location: ../dashboard.php");
    exit;
}

$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Расписание — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/student-style.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <small>Кабинет студента</small>
    </div>
    <nav class="mt-3">
        <a href="panel.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="schedule.php" class="active"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php"><i class="bi bi-file-text me-2"></i>Мои работы</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card rounded shadow-sm mb-4">
        <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
        <p>Вы вошли как <strong>Студент</strong></p>
    </div>
    
    <a href="panel.php" class="btn btn-outline-light mb-3"><i class="bi bi-arrow-left"></i> Назад</a>

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="bi bi-calendar"></i> Моё расписание</h5></div>
        <div class="card-body">
            <table class="table table-hover" id="scheduleTable">
                <thead>
                    <tr>
                        <th>День</th>
                        <th>Группа</th>
                        <th>Предмет</th>
                        <th>Время</th>
                        <th>Тип</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="5" class="text-center">загрузка...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sync.js"></script>
<script>
function loadSchedule() {
    const schedule = Sync.getSchedule();
    const tbody = document.querySelector('#scheduleTable tbody');
    if (!tbody) return;
    
    if (schedule.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Нет занятий</td></tr>';
        return;
    }
    
    tbody.innerHTML = schedule.map(item => `
        <tr>
            <td>${item.day}</td>
            <td>${item.group}</td>
            <td>${item.subject}</td>
            <td>${item.time}</td>
            <td><span class="badge bg-info">${item.type}</span></td>
        </tr>
    `).join('');
}

document.addEventListener('DOMContentLoaded', loadSchedule);
</script>
</body>
</html>