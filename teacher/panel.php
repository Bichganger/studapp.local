<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'teacher') { 
    header('Location: ../dashboard.php'); 
    exit; 
}

$pageTitle = 'Кабинет преподавателя';
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM grades WHERE teacher_id = ?");
$stmt->execute([$userId]);
$gradesCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT group_name) FROM users WHERE role='student' AND group_name IS NOT NULL AND group_name != ''");
$stmt->execute();
$groupsCount = $stmt->fetchColumn();

require_once '../includes/header.php';
?>

<style>.stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; }</style>

<div class="section">
    <div class="container">
        <div class="welcome-card" style="background: linear-gradient(135deg, #00e676 0%, #00c853 100%); color: #0a0e27; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <h3><i class="bi bi-person-badge me-2"></i>Добро пожаловать, <?= e($_SESSION['full_name']) ?>!</h3>
            <p class="mb-0 opacity-75">Кабинет преподавателя</p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="stat-card text-center"><h6 class="text-muted mb-2">Выставлено оценок</h6><h2><?= $gradesCount ?></h2></div></div>
            <div class="col-md-4"><div class="stat-card text-center"><h6 class="text-muted mb-2">Групп</h6><h2><?= $groupsCount ?></h2></div></div>
            <div class="col-md-4"><div class="stat-card text-center"><h6 class="text-muted mb-2">Роль</h6><h2>Преподаватель</h2></div></div>
        </div>

        <div class="row g-4">
            <div class="col-md-6"><div class="card"><div class="card-header">Быстрые действия</div><div class="card-body">
                <div class="d-grid gap-2">
                    <a href="journal.php" class="btn btn-outline-light"><i class="bi bi-journal-text me-2"></i>Журнал оценок</a>
                    <a href="grades.php" class="btn btn-outline-light"><i class="bi bi-star me-2"></i>Выставить оценку</a>
                    <a href="groups.php" class="btn btn-outline-light"><i class="bi bi-collection me-2"></i>Мои группы</a>
                    <a href="schedule.php" class="btn btn-outline-light"><i class="bi bi-calendar me-2"></i>Расписание</a>
                </div>
            </div></div></div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
