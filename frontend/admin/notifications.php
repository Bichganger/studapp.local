<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Рассылка — Админка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-family: sans-serif; }
        .sidebar { min-height: 100vh; background: #1a202c; color: white; position: fixed; width: 240px; left: 0; top: 0; padding: 0; }
        .sidebar-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px; text-align: center; }
        .sidebar-header h5 { color: white; font-weight: 700; margin: 0; }
        .sidebar a { color: #cbd5e0; margin: 5px 10px; border-radius: 5px; display: block; padding: 10px 15px; text-decoration: none; transition: all 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; transform: translateX(5px); }
        .main-content { margin-left: 240px; padding: 20px; }
        footer { margin-left: 240px; padding: 15px; text-align: center; font-size: 0.85rem; color: #e2e8f0; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: none; }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; }
        table { background: rgba(255,255,255,0.95); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h5 class="mb-0"><i class="bi bi-journal-code"></i> Учеба24</h5>
        <small>Админ-панель</small>
    </div>
    <nav class="mt-3">
        <a href="dashboard.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="users.php"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php" class="active"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="settings.php"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr class="mx-2">
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <h3 class="mb-4">Отправка уведомлений</h3>
    
    <div class="card mb-4">
        <div class="card-header">Новое уведомление</div>
        <div class="card-body">
            <form id="notifForm">
                <div class="mb-3">
                    <select name="target_type" class="form-select" required>
                        <option value="">Кому отправить?</option>
                        <option value="all">Всем</option>
                        <option value="students">Студентам</option>
                        <option value="teachers">Преподавателям</option>
                        <option value="group">Группе</option>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="text" name="title" class="form-control" placeholder="Тема" required>
                </div>
                <div class="mb-3">
                    <textarea name="message" class="form-control" rows="5" placeholder="Текст сообщения" required></textarea>
                </div>
                <button type="button" onclick="sendNotification()" class="btn btn-primary">Отправить</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">История отправлений</div>
        <div class="card-body">
            <table class="table table-sm" id="notifTable">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Тема</th>
                        <th>Кому</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody><tr><td colspan="4" class="text-center">загрузка...</td></tr></tbody>
            </table>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sync.js"></script>
<script>
function sendNotification() {
    const form = document.getElementById('notifForm');
    const title = form.querySelector('[name="title"]').value;
    const message = form.querySelector('[name="message"]').value;
    const target = form.querySelector('[name="target_type"]').value;
    
    if (!title || !message || !target) {
        alert('Заполните все поля!');
        return;
    }
    
    const notification = {
        id: Date.now(),
        title: title,
        message: message,
        target: target,
        group: target === 'group' ? prompt('Название группы:') : null,
        date: new Date().toLocaleString('ru-RU'),
        read: false
    };
    
    Sync.addNotification(notification);
    alert('Уведомление отправлено!');
    form.reset();
    loadNotifications();
}

function loadNotifications() {
    const notifications = Sync.getNotifications();
    const tbody = document.querySelector('#notifTable tbody');
    if (!tbody) return;
    
    if (notifications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Нет уведомлений</td></tr>';
        return;
    }
    
    const targetMap = { 'all': 'Все', 'students': 'Студенты', 'teachers': 'Преподаватели', 'group': 'Группа' };
    
    tbody.innerHTML = notifications.map(notif => `
        <tr>
            <td>${notif.date}</td>
            <td>${notif.title}</td>
            <td>${targetMap[notif.target] || notif.target}</td>
            <td>Отправлено</td>
        </tr>
    `).join('');
}

document.addEventListener('DOMContentLoaded', loadNotifications);
</script>
</body>
</html>