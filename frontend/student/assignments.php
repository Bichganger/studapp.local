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
    <title>Мои работы — Учеба24</title>
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
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php" class="active"><i class="bi bi-file-text me-2"></i>Мои работы</a>
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

    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0"><i class="bi bi-file-text"></i> Загрузить работу</h5></div>
        <div class="card-body">
            <form id="uploadForm">
                <div class="mb-3">
                    <label class="form-label">Название работы</label>
                    <input type="text" name="title" class="form-control" placeholder="Лабораторная №4" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Предмет</label>
                    <select name="subject" class="form-select">
                        <option value="Физика">Физика</option>
                        <option value="Математика">Математика</option>
                        <option value="Программирование">Программирование</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Файл</label>
                    <input type="text" name="file" class="form-control" placeholder="lab4.pdf">
                </div>
                <button type="button" onclick="uploadAssignment()" class="btn btn-success">Загрузить</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">История работ</h5></div>
        <div class="card-body">
            <table class="table" id="assignmentsTable">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Предмет</th>
                        <th>Дата</th>
                        <th>Статус</th>
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
function loadMyAssignments() {
    const allAssignments = Sync.getAssignments();
    const studentName = '<?= $name ?>';
    const myAssignments = allAssignments.filter(a => a.student === studentName);
    
    const tbody = document.querySelector('#assignmentsTable tbody');
    if (!tbody) return;
    
    if (myAssignments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Нет работ</td></tr>';
        return;
    }
    
    tbody.innerHTML = myAssignments.map(item => `
        <tr>
            <td>${item.title}</td>
            <td>${item.subject || '-'}</td>
            <td>${item.date}</td>
            <td><span class="badge bg-${item.status === 'Одобрено' ? 'success' : item.status === 'Требует доработки' ? 'danger' : 'warning'}">${item.status}</span></td>
        </tr>
    `).join('');
}

function uploadAssignment() {
    const form = document.getElementById('uploadForm');
    const studentName = '<?= $name ?>';
    const assignment = {
        student: studentName,
        title: form.querySelector('[name="title"]').value,
        subject: form.querySelector('[name="subject"]').value,
        file: form.querySelector('[name="file"]').value,
        date: new Date().toLocaleDateString('ru-RU'),
        status: 'На проверке'
    };
    
    if (!assignment.title) {
        alert('Введите название работы!');
        return;
    }
    
    Sync.addAssignment(assignment);
    form.reset();
    loadMyAssignments();
    Sync.showNotification('Работа загружена!', 'success');
}

document.addEventListener('DOMContentLoaded', loadMyAssignments);
</script>
</body>
</html>