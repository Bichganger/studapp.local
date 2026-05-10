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
    <title>Работы — Учеба24</title>
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
        <a href="journal.php"><i class="bi bi-journal-text me-2"></i>Журнал</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php" class="active"><i class="bi bi-file-text me-2"></i>Работы</a>
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

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="bi bi-file-text"></i> Работы студентов</h5></div>
        <div class="card-body">
            <table class="table table-hover" id="assignmentsTable">
                <thead>
                    <tr>
                        <th>Студент</th>
                        <th>Работа</th>
                        <th>Дата</th>
                        <th>Статус</th>
                        <th>Действия</th>
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
function loadAssignments() {
    const assignments = Sync.getAssignments();
    const tbody = document.querySelector('#assignmentsTable tbody');
    if (!tbody) return;
    
    if (assignments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Нет работ</td></tr>';
        return;
    }
    
    tbody.innerHTML = assignments.map((item, index) => `
        <tr>
            <td>${item.student}</td>
            <td>${item.title}</td>
            <td>${item.date}</td>
            <td><span class="badge bg-warning">${item.status}</span></td>
            <td>
                <button class="btn btn-sm btn-success" onclick="checkAssignment(${index})">✓</button>
                <button class="btn btn-sm btn-danger" onclick="rejectAssignment(${index})">✗</button>
            </td>
        </tr>
    `).join('');
}

function checkAssignment(index) {
    const assignments = Sync.getAssignments();
    const grade = prompt('Введите оценку:');
    if (grade) {
        Sync.updateAssignment(index, { status: 'Одобрено', grade: grade });
        Sync.sendNotification('Работа проверена', `Ваша работа "${assignments[index].title}" одобрена. Оценка: ${grade}`, 'students');
        loadAssignments();
        Sync.showNotification('Работа одобрена!', 'success');
    }
}

function rejectAssignment(index) {
    const assignments = Sync.getAssignments();
    Sync.updateAssignment(index, { status: 'Требует доработки' });
    Sync.sendNotification('Работа требует доработки', `Ваша работа "${assignments[index].title}" требует исправлений`, 'students');
    loadAssignments();
    Sync.showNotification('Работа отклонена', 'warning');
}

document.addEventListener('DOMContentLoaded', loadAssignments);
</script>
</body>
</html>