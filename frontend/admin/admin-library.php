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
    <title>Библиотека — Админка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: sans-serif; }
        .sidebar { min-height: 100vh; background: #343a40; color: white; position: fixed; width: 240px; left: 0; top: 0; padding: 0; }
        .sidebar-header { background: #007bff; padding: 20px; text-align: center; }
        .sidebar a { color: #adb5bd; margin: 5px 10px; border-radius: 5px; display: block; padding: 10px 15px; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background: #007bff; color: white; }
        .main-content { margin-left: 240px; padding: 20px; }
        footer { margin-left: 240px; padding: 15px; text-align: center; font-size: 0.85rem; color: #6c757d; }
        .card { border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h5 class="mb-0"><i class="bi bi-journal-code"></i> Учеба24</h5>
        <small>Админ-панель</small>
    </div>
    <nav class="mt-3">
        <a href="admin-dashboard.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="admin-users.php"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="admin-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="admin-groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="admin-library.php" class="active"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="admin-notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="admin-settings.php"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr class="mx-2">
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <h3 class="mb-4">Модерация работ</h3>
    
    <div class="card">
        <div class="card-header">Работы на проверке</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover" id="libraryTable">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Тип</th>
                            <th>Студент</th>
                            <th>Группа</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody><tr><td colspan="6" class="text-center">загрузка...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="admin.js"></script>
</body>
</html>
