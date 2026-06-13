<?php
require_once '../config/db.php';
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'student') { 
    header('Location: ../dashboard.php'); 
    exit; 
}

$pageTitle = 'Кабинет студента';
$userId = $_SESSION['user_id'];
$groupName = $_SESSION['group_name'] ?? '';

$todayRu = match(strtolower(date('l'))) {
    'monday' => 'понедельник', 'tuesday' => 'вторник', 'wednesday' => 'среда',
    'thursday' => 'четверг', 'friday' => 'пятница', 'saturday' => 'суббота',
    default => 'понедельник'
};

// Расписание на сегодня
$stmt = $pdo->prepare("SELECT * FROM schedule WHERE group_name = ? AND day_of_week = ? ORDER BY start_time");
$stmt->execute([$groupName, $todayRu]);
$todayClasses = $stmt->fetchAll();
$classesToday = count($todayClasses);

// Следующее занятие (если есть сегодня)
$nextClass = null;
$currentTime = date('H:i:s');
foreach ($todayClasses as $class) {
    if ($class['start_time'] > $currentTime) {
        $nextClass = $class;
        break;
    }
}

// Статистика оценок
$stmt = $pdo->prepare("SELECT AVG(grade) as avg_grade, COUNT(*) as total FROM grades WHERE student_id = ?");
$stmt->execute([$userId]);
$stats = $stmt->fetch();
$avgGrade = round($stats['avg_grade'] ?? 0, 1);
$gradesCount = $stats['total'] ?? 0;

// Оценки по предметам (для графика)
$stmt = $pdo->prepare("SELECT subject, AVG(grade) as avg FROM grades WHERE student_id = ? GROUP BY subject ORDER BY avg DESC LIMIT 5");
$stmt->execute([$userId]);
$topSubjects = $stmt->fetchAll();

// Задания
$stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending FROM assignments WHERE student_id = ?");
$stmt->execute([$userId]);
$assignStats = $stmt->fetch();
$pendingAssigns = $assignStats['pending'] ?? 0;
$totalAssigns = $assignStats['total'] ?? 0;

// Уведомления
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE is_read = 0 AND (target_type IN ('all', 'students') OR target_group = ?)");
$stmt->execute([$groupName]);
$unreadNotifs = $stmt->fetchColumn();

// Последние оценки
$stmt = $pdo->prepare("SELECT g.*, u.full_name as teacher_name FROM grades g LEFT JOIN users u ON g.teacher_id = u.id WHERE g.student_id = ? ORDER BY g.created_at DESC LIMIT 5");
$stmt->execute([$userId]);
$recentGrades = $stmt->fetchAll();

// Посещаемость (если есть журнал)
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present FROM journal WHERE student_id = ?");
    $stmt->execute([$userId]);
    $attendance = $stmt->fetch();
    $attendanceRate = $attendance['total'] > 0 ? round($attendance['present'] / $attendance['total'] * 100) : 0;
} catch (Exception $e) {
    $attendanceRate = 0;
}

require_once '../includes/header.php';
?>

<style>
    .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-icon { font-size: 1.8rem; }
    .grade-bar { height: 6px; border-radius: 3px; background: var(--border-color); overflow: hidden; margin-top: 8px; }
    .grade-fill { height: 100%; border-radius: 3px; }
    .next-class-card { background: linear-gradient(135deg, rgba(0,230,118,0.15) 0%, rgba(0,200,83,0.1) 100%); border: 1px solid var(--accent); }
</style>

<div class="section">
    <div class="container">
        <div class="welcome-card" style="background: linear-gradient(135deg, #00e676 0%, #00c853 100%); color: #0a0e27; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <h3><i class="bi bi-mortarboard me-2"></i>Добро пожаловать, <?= e($_SESSION['full_name']) ?>!</h3>
            <p class="mb-0 opacity-75">Группа <?= e($groupName) ?> | <?= $todayRu ?></p>
        </div>

        <!-- Статистика -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-calendar-event stat-icon text-primary mb-2"></i>
                    <h4 class="mb-0"><?= $classesToday ?></h4>
                    <small class="text-muted">Занятий сегодня</small>
                    <?php if ($nextClass): ?>
                    <small class="d-block mt-1 text-success">След: <?= substr($nextClass['start_time'], 0, 5) ?></small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-star stat-icon text-warning mb-2"></i>
                    <h4 class="mb-0"><?= $avgGrade ?></h4>
                    <small class="text-muted">Средний балл</small>
                    <div class="grade-bar">
                        <div class="grade-fill bg-success" style="width: <?= ($avgGrade / 5) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-file-earmark-check stat-icon text-info mb-2"></i>
                    <h4 class="mb-0"><?= $totalAssigns ?></h4>
                    <small class="text-muted">Заданий всего</small>
                    <?php if ($pendingAssigns > 0): ?>
                    <small class="d-block mt-1 text-warning"><?= $pendingAssigns ?> на сдаче</small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-person-check stat-icon text-success mb-2"></i>
                    <h4 class="mb-0"><?= $attendanceRate ?>%</h4>
                    <small class="text-muted">Посещаемость</small>
                    <div class="grade-bar">
                        <div class="grade-fill bg-success" style="width: <?= $attendanceRate ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Следующее занятие -->
        <?php if ($nextClass): ?>
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card next-class-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><i class="bi bi-alarm me-2"></i>Следующее занятие</h5>
                            <p class="mb-0"><strong><?= e($nextClass['subject']) ?></strong></p>
                            <small class="text-muted">Каб. <?= e($nextClass['classroom']) ?> | <?= e($nextClass['teacher_name'] ?? '—') ?></small>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0 text-accent"><?= substr($nextClass['start_time'], 0, 5) ?>–<?= substr($nextClass['end_time'], 0, 5) ?></h3>
                            <small class="text-muted">Сегодня</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Основная сетка -->
        <div class="row g-4">
            <!-- Последние оценки -->
            <div class="col-md-7">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-star me-2"></i>Последние оценки</span>
                        <a href="grades.php" class="btn btn-sm btn-outline-light">Все оценки</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentGrades)): ?>
                            <p class="text-muted mb-0">Оценок пока нет</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead><tr><th>Предмет</th><th>Оценка</th><th>Дата</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($recentGrades as $g): ?>
                                        <tr>
                                            <td>
                                                <strong><?= e($g['subject']) ?></strong>
                                                <br><small class="text-muted"><?= e($g['teacher_name'] ?? '') ?></small>
                                            </td>
                                            <td><span class="badge bg-<?= $g['grade'] >= 4 ? 'success' : ($g['grade'] == 3 ? 'warning' : 'danger') ?>"><?= $g['grade'] ?></span></td>
                                            <td><small><?= date('d.m.Y', strtotime($g['created_at'])) ?></small></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Уведомления и быстрые действия -->
            <div class="col-md-5">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bell me-2"></i>Уведомления</span>
                        <?php if ($unreadNotifs > 0): ?>
                        <span class="badge bg-danger"><?= $unreadNotifs ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <a href="notifications.php" class="btn btn-outline-light w-100">
                            <i class="bi bi-inbox me-2"></i>Все уведомления
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><i class="bi bi-lightning me-2"></i>Быстрые действия</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="library.php" class="btn btn-outline-light"><i class="bi bi-journal-bookmark me-2"></i>Библиотека</a>
                            <a href="schedule.php" class="btn btn-outline-light"><i class="bi bi-calendar-week me-2"></i>Расписание</a>
                            <a href="map.php" class="btn btn-outline-light"><i class="bi bi-geo-alt me-2"></i>Карта корпуса</a>
                            <a href="tips.php" class="btn btn-outline-light"><i class="bi bi-lightbulb me-2"></i>Советы</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
