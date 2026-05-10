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
    <title>Настройки — Админка</title>
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
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
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
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="settings.php" class="active"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr class="mx-2">
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <h3 class="mb-4">Настройки системы</h3>
    
    <div class="card">
        <div class="card-header">Общие настройки</div>
        <div class="card-body">
            <form id="settingsForm">
                <div class="mb-3">
                    <label class="form-label">Название системы</label>
                    <input type="text" name="system_name" class="form-control" value="Учеба24">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email для уведомлений</label>
                    <input type="email" name="notification_email" class="form-control" value="admin@ucheba.online">
                </div>
                <div class="mb-3">
                    <label class="form-label">Часовой пояс</label>
                    <select name="timezone" class="form-select">
                        <option value="MSK">MSK (Москва)</option>
                        <option value="MSK+1">MSK+1</option>
                        <option value="MSK+2">MSK+2</option>
                    </select>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="allow_registration" class="form-check-input" checked>
                    <label class="form-check-label">Открытая регистрация</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="email_confirmation" class="form-check-input" checked>
                    <label class="form-check-label">Подтверждение email</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Мин. длина пароля</label>
                    <input type="number" name="min_password_length" class="form-control" value="6">
                </div>
                <button type="button" onclick="saveSettings()" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sync.js"></script>
<script>
function loadSettings() {
    const settings = JSON.parse(localStorage.getItem('sync_settings') || '{}');
    const form = document.getElementById('settingsForm');
    if (settings.system_name) form.querySelector('[name="system_name"]').value = settings.system_name;
    if (settings.notification_email) form.querySelector('[name="notification_email"]').value = settings.notification_email;
    if (settings.timezone) form.querySelector('[name="timezone"]').value = settings.timezone;
    if (settings.min_password_length) form.querySelector('[name="min_password_length"]').value = settings.min_password_length;
}

function saveSettings() {
    const form = document.getElementById('settingsForm');
    const settings = {
        system_name: form.querySelector('[name="system_name"]').value,
        notification_email: form.querySelector('[name="notification_email"]').value,
        timezone: form.querySelector('[name="timezone"]').value,
        allow_registration: form.querySelector('[name="allow_registration"]').checked,
        email_confirmation: form.querySelector('[name="email_confirmation"]').checked,
        min_password_length: form.querySelector('[name="min_password_length"]').value
    };
    
    localStorage.setItem('sync_settings', JSON.stringify(settings));
    Sync.showNotification('Настройки сохранены!', 'success');
}

document.addEventListener('DOMContentLoaded', loadSettings);
</script>
</body>
</html>