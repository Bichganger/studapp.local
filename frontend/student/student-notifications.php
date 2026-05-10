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
    <title>Уведомления — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/student-style.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <small>Кабинет студента</small>
    </div>
    <nav class="mt-3">
        <a href="student-dashboard.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="student-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="student-grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="student-assignments.php"><i class="bi bi-file-text me-2"></i>Мои работы</a>
        <a href="student-library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="student-notifications.php" class="active"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <hr class="mx-2">
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card rounded shadow-sm mb-4">
        <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
        <p>Вы вошли как <strong>Студент</strong></p>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="bi bi-bell"></i> Мои уведомления</h5></div>
        <div class="card-body">
            <table class="table" id="notificationsTable">
                <thead>
                    <tr>
                        <th>Заголовок</th>
                        <th>Сообщение</th>
                        <th>Дата</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="3" class="text-center">загрузка...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/sync.js"></script>
<script>
function loadMyNotifications() {
    const notifications = Sync.getNotificationsForUser('student');
    const tbody = document.querySelector('#notificationsTable tbody');
    if (!tbody) return;
    
    if (notifications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">Нет уведомлений</td></tr>';
        return;
    }
    
    tbody.innerHTML = notifications.map(notif => `
        <tr class="${notif.read ? '' : 'table-active'}">
            <td><strong>${notif.title}</strong></td>
            <td>${notif.message}</td>
            <td>${notif.date}</td>
        </tr>
    `).join('');
}

document.addEventListener('DOMContentLoaded', loadMyNotifications);
</script>
</body>
</html>
