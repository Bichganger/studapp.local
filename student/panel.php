<?php
session_start();
require_once '../config/db.php';
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'student') { header('Location: ../dashboard.php'); exit; }

$pageTitle = 'Кабинет студента';
$userId = $_SESSION['user_id'];
$groupName = $_SESSION['group_name'] ?? '';

$todayRu = match(strtolower(date('l'))) {
    'monday' => 'понедельник', 'tuesday' => 'вторник', 'wednesday' => 'среда',
    'thursday' => 'четверг', 'friday' => 'пятница', 'saturday' => 'суббота',
    default => 'понедельник'
};

$stmt = $pdo->prepare("SELECT COUNT(*) FROM schedule WHERE group_name = ? AND day_of_week = ?");
$stmt->execute([$groupName, $todayRu]);
$classesToday = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM grades WHERE student_id = ?");
$stmt->execute([$userId]);
$gradesCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM assignments WHERE student_id = ?");
$stmt->execute([$userId]);
$assignsCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE target_type IN ('all', 'students') OR target_group = ?");
$stmt->execute([$groupName]);
$notifsCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM schedule WHERE group_name = ? AND day_of_week = ? ORDER BY start_time");
$stmt->execute([$groupName, $todayRu]);
$todayClasses = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT g.*, u.full_name as teacher_name FROM grades g LEFT JOIN users u ON g.teacher_id = u.id WHERE g.student_id = ? ORDER BY g.created_at DESC LIMIT 5");
$stmt->execute([$userId]);
$recentGrades = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<style>
    .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
</style>

<div class="section">
    <div class="container">
        <div class="welcome-card" style="background: linear-gradient(135deg, #00e676 0%, #00c853 100%); color: #0a0e27; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <h3><i class="bi bi-person-circle me-2"></i>Добро пожаловать, <?= e($_SESSION['full_name']) ?>!</h3>
            <p class="mb-0 opacity-75">Кабинет студента — группа <?= e($groupName) ?></p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="stat-card text-center"><h6 class="text-muted mb-2">Занятий сегодня</h6><h2 class="mb-0"><?= $classesToday ?></h2><i class="bi bi-calendar text-accent" style="font-size: 1.5rem;"></i></div></div>
            <div class="col-md-3"><div class="stat-card text-center"><h6 class="text-muted mb-2">Оценок</h6><h2 class="mb-0"><?= $gradesCount ?></h2><i class="bi bi-star text-accent" style="font-size: 1.5rem;"></i></div></div>
            <div class="col-md-3"><div class="stat-card text-center"><h6 class="text-muted mb-2">Заданий</h6><h2 class="mb-0"><?= $assignsCount ?></h2><i class="bi bi-file-text text-accent" style="font-size: 1.5rem;"></i></div></div>
            <div class="col-md-3"><div class="stat-card text-center"><h6 class="text-muted mb-2">Уведомлений</h6><h2 class="mb-0"><?= $notifsCount ?></h2><i class="bi bi-bell text-accent" style="font-size: 1.5rem;"></i></div></div>
        </div>

        <div class="row g-3" style="color: white;">
            <div class="col-md-6">
                <div class="card" style="color: white;"><div class="card-header"><i class="bi bi-calendar me-2"></i>Расписание на сегодня</div>
                    <div class="card-body">
                        <?php if (empty($todayClasses)): ?>
                            <p class="text-muted mb-0">Сегодня нет занятий</p>
                        <?php else: ?>
                            <?php foreach ($todayClasses as $c): ?>
                            <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom border-secondary" >
                                <div style="color: white;"><strong><?= e($c['subject']) ?></strong><br><small class="text-muted"><?= e($c['teacher_name'] ?? '—') ?></small></div>
                                <small class="text-muted" style="color: white;"><?= substr($c['start_time'], 0, 5) ?>–<?= substr($c['end_time'], 0, 5) ?><br>Каб. <?= e($c['classroom']) ?></small>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card"><div class="card-header"><i class="bi bi-star me-2"></i>Последние оценки</div>
                    <div class="card-body">
                        <?php if (empty($recentGrades)): ?>
                            <p class="text-muted mb-0">Нет оценок</p>
                        <?php else: ?>
                            <?php foreach ($recentGrades as $g): ?>
                            <?php $bc = $g['grade'] >= 4 ? 'bg-success' : ($g['grade'] == 3 ? 'bg-warning' : 'bg-danger'); ?>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary">
                                <div><strong><?= e($g['subject']) ?></strong><br><small class="text-muted"><?= date('d.m.Y', strtotime($g['created_at'])) ?></small></div>
                                <span class="badge <?= $bc ?>"><?= $g['grade'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
