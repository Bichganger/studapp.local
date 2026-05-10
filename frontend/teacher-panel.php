<?php
session_start();
require_once 'protected/auth_guard.php';

if ($_SESSION['role'] != 'teacher') {
    header("Location: dashboard.php");
    exit;
}

$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Кабинет — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); font-family: sans-serif; }
        .sidebar { min-height: 100vh; background: #1a202c; color: white; position: fixed; width: 240px; left: 0; top: 0; }
        .sidebar-header { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 20px; text-align: center; border-bottom: 2px solid rgba(255,255,255,0.2); }
        .sidebar-header h5 { color: white; font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.3); margin: 0; }
        .sidebar-header small { color: rgba(255,255,255,0.9); display: block; margin-top: 5px; }
        .sidebar a { color: #cbd5e0; margin: 5px 10px; border-radius: 5px; display: block; padding: 10px 15px; text-decoration: none; transition: all 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; transform: translateX(5px); }
        .main-content { margin-left: 240px; padding: 20px; }
        footer { margin-left: 240px; padding: 15px; text-align: center; color: #e2e8f0; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: none; transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .btn-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; }
        .btn-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; }
        .btn-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none; }
        .btn-secondary { background: linear-gradient(135deg, #868f96 0%, #596164 100%); border: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-person-badge"></i> Учеба24</h5>
        <small>Кабинет преподавателя</small>
    </div>
    <nav class="mt-3">
        <a href="teacher-panel.php" class="active"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="teacher/teacher-journal.php"><i class="bi bi-journal me-2"></i>Журнал</a>
        <a href="teacher/teacher-grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="teacher/teacher-groups.php"><i class="bi bi-people me-2"></i>Группы</a>
        <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card rounded shadow-sm mb-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border-radius: 15px;">
        <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
        <p>Вы вошли как <strong>Преподаватель</strong></p>
    </div>
    
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-journal display-4 text-info"></i>
                    <h5 class="mt-2">Журнал</h5>
                    <p class="text-muted small mb-0">Записи посещаемости</p>
                    <a href="teacher/teacher-journal.php" class="btn btn-sm btn-info mt-2 text-white">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-star display-4 text-warning"></i>
                    <h5 class="mt-2">Оценки</h5>
                    <p class="text-muted small mb-0">Выставление оценок</p>
                    <a href="teacher/teacher-grades.php" class="btn btn-sm btn-warning mt-2 text-white">Открыть</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-people display-4 text-success"></i>
                    <h5 class="mt-2">Группы</h5>
                    <p class="text-muted small mb-0">Мои группы</p>
                    <a href="teacher/teacher-groups.php" class="btn btn-sm btn-success mt-2">Открыть</a>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/panel.js"></script>
</body>
</html>
