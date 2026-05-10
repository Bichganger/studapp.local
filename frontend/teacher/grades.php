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
    <title>Оценки — Учеба24</title>
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
        <a href="grades.php" class="active"><i class="bi bi-star me-2"></i>Оценки</a>
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

    <button class="btn btn-success mb-3" onclick="addGrade()"><i class="bi bi-plus"></i> Выставить оценку</button>
    
    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="bi bi-star"></i> Оценки студентов</h5></div>
        <div class="card-body">
            <table class="table table-hover" id="gradesTable">
                <thead>
                    <tr>
                        <th>Студент</th>
                        <th>Предмет</th>
                        <th>Оценка</th>
                        <th>Дата</th>
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
function loadGrades() {
    const grades = Sync.getGrades();
    const tbody = document.querySelector('#gradesTable tbody');
    if (!tbody) return;
    
    if (grades.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Нет оценок</td></tr>';
        return;
    }
    
    tbody.innerHTML = grades.map(item => `
        <tr>
            <td>${item.student}</td>
            <td>${item.subject}</td>
            <td><span class="badge bg-success">${item.grade}</span></td>
            <td>${item.date}</td>
        </tr>
    `).join('');
}

function addGrade() {
    const student = prompt('ФИО студента:');
    const subject = prompt('Предмет:');
    const grade = prompt('Оценка (2-5):');
    const teacher = '<?= $name ?>';
    const date = new Date().toLocaleDateString('ru-RU');
    
    if (student && subject && grade) {
        Sync.addGrade({ student, subject, grade, teacher, date });
        Sync.sendNotification('Новая оценка', `По предмету ${subject} выставлена оценка ${grade}`, 'students');
        loadGrades();
        Sync.showNotification('Оценка выставлена!', 'success');
    }
}

document.addEventListener('DOMContentLoaded', loadGrades);
</script>
</body>
</html>