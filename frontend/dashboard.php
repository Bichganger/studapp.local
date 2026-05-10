<?php
session_start();
require_once 'protected/auth_guard.php';

$name = htmlspecialchars($_SESSION['full_name']);
$role = $_SESSION['role'];

$roleLabel = '';
if ($role == 'admin') $roleLabel = 'Администратор';
elseif ($role == 'teacher') $roleLabel = 'Преподаватель';
elseif ($role == 'student') $roleLabel = 'Студент';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-family: sans-serif; min-height: 100vh; }
        .navbar { background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%) !important; }
        .navbar-brand { font-weight: 700; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: none; transition: transform 0.3s; }
        .card:hover { transform: translateY(-8px); }
        .welcome-card { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border-radius: 15px; }
        .footer { margin-top: 60px; text-align: center; color: #e2e8f0; padding: 20px 0; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php"><i class="bi bi-journal-code"></i> Учеба24</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-house-door me-1"></i>Главная</a></li>
                <?php if ($role == 'teacher'): ?>
                    <li class="nav-item"><a class="nav-link" href="teacher-panel.php"><i class="bi bi-calendar me-1"></i>Кабинет</a></li>
                <?php elseif ($role == 'student'): ?>
                    <li class="nav-item"><a class="nav-link" href="student-panel.php"><i class="bi bi-calendar-event me-1"></i>Кабинет</a></li>
                <?php elseif ($role == 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="admin/admin-dashboard.php"><i class="bi bi-shield-lock me-1"></i>Админка</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>Выход</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="p-4 welcome-card rounded shadow-sm mb-4">
                <h3><i class="bi bi-person-circle"></i> Добро пожаловать, <?= $name ?>!</h3>
                <p>Вы вошли как <strong><?= $roleLabel ?></strong></p>
            </div>
            <div class="row g-4">
                <?php if ($role == 'teacher'): ?>
                    <div class="col-md-6">
                        <a href="teacher-panel.php" class="card text-decoration-none text-reset h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-person-badge display-4 text-info"></i>
                                <h5 class="mt-2">Кабинет</h5>
                                <p class="text-muted small">Журнал, оценки</p>
                            </div>
                        </a>
                    </div>
                <?php elseif ($role == 'student'): ?>
                    <div class="col-md-6">
                        <a href="student-panel.php" class="card text-decoration-none text-reset h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-mortarboard display-4 text-success"></i>
                                <h5 class="mt-2">Кабинет</h5>
                                <p class="text-muted small">Расписание, оценки</p>
                            </div>
                        </a>
                    </div>
                <?php elseif ($role == 'admin'): ?>
                    <div class="col-md-6">
                        <a href="admin/admin-dashboard.php" class="card text-decoration-none text-reset h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-shield-lock display-4 text-danger"></i>
                                <h5 class="mt-2">Админка</h5>
                                <p class="text-muted small">Управление</p>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Профиль</h5>
                </div>
                <div class="card-body">
                    <p><strong>ФИО:</strong> <?= $name ?></p>
                    <p><strong>Роль:</strong> <span class="badge bg-primary"><?= $roleLabel ?></span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer">&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sync.js"></script>
</body>
</html>