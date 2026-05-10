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
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-family: sans-serif; }
        .sidebar { min-height: 100vh; background: #1a202c; color: white; position: fixed; width: 240px; left: 0; top: 0; padding: 0; }
        .sidebar-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px; text-align: center; border-bottom: 2px solid rgba(255,255,255,0.2); }
        .sidebar-header h5 { color: white; font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.3); margin: 0; }
        .sidebar-header small { color: rgba(255,255,255,0.9); display: block; margin-top: 5px; }
        .sidebar a { color: #cbd5e0; margin: 5px 10px; border-radius: 5px; display: block; padding: 10px 15px; text-decoration: none; transition: all 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; transform: translateX(5px); }
        .main-content { margin-left: 240px; padding: 20px; }
        footer { margin-left: 240px; padding: 15px; text-align: center; font-size: 0.85rem; color: #e2e8f0; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: none; transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; }
        .btn-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none; }
        .btn-danger { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); border: none; }
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
    
    <div class="card mb-4">
        <div class="card-header">Добавить в библиотеку</div>
        <div class="card-body">
            <form id="addLibraryForm" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="title" class="form-control" placeholder="Название работы" required>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="лекция">Лекция</option>
                        <option value="методичка">Методичка</option>
                        <option value="пример">Пример</option>
                        <option value="заданием">Задание</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="student" class="form-control" placeholder="Автор (студент)">
                </div>
                <div class="col-md-2">
                    <input type="text" name="group" class="form-control" placeholder="Группа">
                </div>
                <div class="col-md-2">
                    <button type="button" onclick="addLibraryItem()" class="btn btn-success w-100">Добавить</button>
                </div>
            </form>
        </div>
    </div>
    
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
<script src="../js/sync.js"></script>
<script>
function loadLibrary() {
    const library = Sync.getLibrary();
    const tbody = document.querySelector('#libraryTable tbody');
    if (!tbody) return;
    
    if (library.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Нет работ</td></tr>';
        return;
    }
    
    tbody.innerHTML = library.map((item, index) => `
        <tr>
            <td>${item.title}</td>
            <td><span class="badge bg-info">${item.type}</span></td>
            <td>${item.student || '-'}</td>
            <td>${item.group || '-'}</td>
            <td><span class="badge bg-success">${item.status}</span></td>
            <td><button class="btn btn-sm btn-danger" onclick="deleteLibraryItem(${index})">✗</button></td>
        </tr>
    `).join('');
}

function addLibraryItem() {
    const form = document.getElementById('addLibraryForm');
    const item = {
        title: form.querySelector('[name="title"]').value,
        type: form.querySelector('[name="type"]').value,
        student: form.querySelector('[name="student"]').value,
        group: form.querySelector('[name="group"]').value,
        status: 'Опубликовано',
        id: Date.now()
    };
    
    if (!item.title) {
        alert('Введите название работы!');
        return;
    }
    
    Sync.addLibrary(item);
    form.reset();
    loadLibrary();
    Sync.showNotification('Работа добавлена в библиотеку!', 'success');
}

function deleteLibraryItem(index) {
    if (!confirm('Удалить работу из библиотеки?')) return;
    const library = Sync.getLibrary();
    library.splice(index, 1);
    localStorage.setItem('sync_library', JSON.stringify(library));
    loadLibrary();
    Sync.showNotification('Работа удалена', 'warning');
}

document.addEventListener('DOMContentLoaded', loadLibrary);
</script>
</body>
</html>
