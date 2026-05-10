<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../dashboard.php");
    exit;
}

$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Журнал — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/teacher-style.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <small>Кабинет преподавателя</small>
    </div>
    <nav class="mt-3">
        <a href="panel.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="journal.php" class="active"><i class="bi bi-journal-text me-2"></i>Журнал</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php"><i class="bi bi-file-text me-2"></i>Работы</a>
        <a href="groups.php"><i class="bi bi-people me-2"></i>Группы</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card rounded shadow-sm mb-4">
        <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
        <p>Вы вошли как <strong>Преподаватель</strong></p>
    </div>

    <button class="btn btn-success mb-3" onclick="addJournal()"><i class="bi bi-plus"></i> Добавить запись</button>
    
    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="bi bi-journal-text"></i> Журнал</h5></div>
        <div class="card-body">
            <table class="table table-hover" id="journalTable">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Группа</th>
                        <th>Предмет</th>
                        <th>Комментарий</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="4" class="text-center">загрузка...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sync.js"></script>
<script>
function loadJournal() {
    const journal = Sync.getJournal();
    const tbody = document.querySelector('#journalTable tbody');
    if (!tbody) return;
    
    if (journal.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Нет записей</td></tr>';
        return;
    }
    
    tbody.innerHTML = journal.map(item => `
        <tr>
            <td>${item.date}</td>
            <td>${item.group}</td>
            <td>${item.subject}</td>
            <td>${item.comment || '-'}</td>
        </tr>
    `).join('');
}

function addJournal() {
    const date = prompt('Дата:');
    const group = prompt('Группа:');
    const subject = prompt('Предмет:');
    const comment = prompt('Комментарий:');
    
    if (date && group && subject) {
        Sync.addJournal({ date, group, subject, comment: comment || '' });
        loadJournal();
        Sync.showNotification('Запись добавлена!', 'success');
    }
}

document.addEventListener('DOMContentLoaded', loadJournal);
</script>
</body>
</html>