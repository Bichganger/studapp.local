<?php
session_start();
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

$pageTitle = 'Панель администратора';

$usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$studentsCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$teachersCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role='teacher'")->fetchColumn();
$groupsCount = $pdo->query("SELECT COUNT(DISTINCT group_name) FROM users WHERE group_name IS NOT NULL AND group_name != ''")->fetchColumn();
$worksCount = $pdo->query("SELECT COUNT(*) FROM works")->fetchColumn();

require_once '../includes/header.php';
?>

<style>.stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; } .stat-card h2 { color: var(--accent); }</style>

<div class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Администрирование</span>
            <h1 class="section-title">Панель управления</h1>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="stat-card text-center"><h6 class="text-muted mb-2">Пользователей</h6><h2><?= $usersCount ?></h2></div></div>
            <div class="col-md-3"><div class="stat-card text-center"><h6 class="text-muted mb-2">Студентов</h6><h2><?= $studentsCount ?></h2></div></div>
            <div class="col-md-3"><div class="stat-card text-center"><h6 class="text-muted mb-2">Преподавателей</h6><h2><?= $teachersCount ?></h2></div></div>
            <div class="col-md-3"><div class="stat-card text-center"><h6 class="text-muted mb-2">Групп</h6><h2><?= $groupsCount ?></h2></div></div>
        </div>

        <div class="row g-4">
            <div class="col-md-6"><div class="card"><div class="card-header"><i class="bi bi-gear me-2"></i>Быстрые действия</div><div class="card-body">
                <div class="d-grid gap-2">
                    <a href="users.php" class="btn btn-outline-light"><i class="bi bi-people me-2"></i>Пользователи</a>
                    <a href="groups.php" class="btn btn-outline-light"><i class="bi bi-collection me-2"></i>Группы</a>
                    <a href="schedule.php" class="btn btn-outline-light"><i class="bi bi-calendar me-2"></i>Расписание</a>
                    <a href="library.php" class="btn btn-outline-light"><i class="bi bi-journal me-2"></i>Библиотека</a>
                    <a href="teachers.php" class="btn btn-outline-light"><i class="bi bi-person-badge me-2"></i>Преподаватели</a>
                    <a href="tips.php" class="btn btn-outline-light"><i class="bi bi-lightbulb me-2"></i>Советы</a>
                </div>
            </div></div></div>
            <div class="col-md-6"><div class="card"><div class="card-header"><i class="bi bi-info-circle me-2"></i>Информация</div><div class="card-body">
                <p class="text-muted mb-1"><i class="bi bi-journal-check me-2"></i>Работ в библиотеке: <strong><?= $worksCount ?></strong></p>
                <p class="text-muted mb-0"><i class="bi bi-database me-2"></i>БД: <strong>studapp</strong></p>
            </div></div></div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
