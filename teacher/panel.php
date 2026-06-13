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

$todayRu = match(strtolower(date('l'))) {
    'monday' => 'понедельник', 'tuesday' => 'вторник', 'wednesday' => 'среда',
    'thursday' => 'четверг', 'friday' => 'пятница', 'saturday' => 'суббота',
    default => 'понедельник'
};

// Получаем группы преподавателя
$stmt = $pdo->prepare("SELECT DISTINCT group_name FROM users WHERE role='student' AND group_name IS NOT NULL AND group_name != ''");
$stmt->execute();
$allGroups = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Для простоты берем все группы (в реальном проекте — фильтровать по преподавателю)
$groupsCount = count($allGroups);

// Статистика студентов
$studentsCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();

// Оценки за неделю
$weekStart = date('Y-m-d', strtotime('-6 days'));
$stmt = $pdo->prepare("SELECT COUNT(*) FROM grades WHERE teacher_id = ? AND DATE(created_at) >= ?");
$stmt->execute([$userId, $weekStart]);
$gradesWeek = $stmt->fetchColumn();

// Все оценки
$stmt = $pdo->prepare("SELECT COUNT(*) FROM grades WHERE teacher_id = ?");
$stmt->execute([$userId]);
$gradesTotal = $stmt->fetchColumn();

// Средний балл по предметам
$stmt = $pdo->prepare("SELECT subject, AVG(CAST(grade AS UNSIGNED)) as avg_grade, COUNT(*) as count FROM grades WHERE teacher_id = ? GROUP BY subject ORDER BY count DESC LIMIT 5");
$stmt->execute([$userId]);
$subjectStats = $stmt->fetchAll();

// Последние оценки
$stmt = $pdo->prepare("SELECT g.*, u.full_name as student_name FROM grades g JOIN users u ON g.student_id = u.id WHERE g.teacher_id = ? ORDER BY g.created_at DESC LIMIT 5");
$stmt->execute([$userId]);
$recentGrades = $stmt->fetchAll();

// Расписание на сегодня (нужно связать с группами преподавателя)
// Для упрощения — показываем все занятия сегодня
$stmt = $pdo->prepare("SELECT DISTINCT s.* FROM schedule s JOIN users u ON u.group_name = s.group_name WHERE u.id = ? AND s.day_of_week = ? ORDER BY s.start_time");
$stmt->execute([$userId, $todayRu]);
$todayClasses = $stmt->fetchAll();

// Неотмеченные работы (если есть)
try {
    $pendingWorks = $pdo->query("SELECT COUNT(*) FROM assignments WHERE status='pending'")->fetchColumn();
} catch (Exception $e) {
    $pendingWorks = 0;
}

require_once '../includes/header.php';
?>

<style>
    .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-icon { font-size: 1.8rem; }
    .grade-bar { height: 6px; border-radius: 3px; background: var(--border-color); overflow: hidden; margin-top: 8px; }
    .grade-fill { height: 100%; border-radius: 3px; }
    .next-class-card { background: linear-gradient(135deg, rgba(0,209,255,0.15) 0%, rgba(0,176,255,0.1) 100%); border: 1px solid #00d1ff; }
</style>

<div class="section">
    <div class="container">
        <div class="welcome-card" style="background: linear-gradient(135deg, #00d1ff 0%, #00b0ff 100%); color: #0a0e27; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <h3><i class="bi bi-person-badge me-2"></i>Добро пожаловать, <?= e($_SESSION['full_name']) ?>!</h3>
            <p class="mb-0 opacity-75">Кабинет преподавателя | <?= $todayRu ?></p>
        </div>

        <!-- Статистика -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-journal-text stat-icon text-primary mb-2"></i>
                    <h4 class="mb-0"><?= $gradesTotal ?></h4>
                    <small class="text-muted">Всего оценок</small>
                    <small class="d-block mt-1 text-success"><?= $gradesWeek ?> за неделю</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-mortarboard stat-icon text-info mb-2"></i>
                    <h4 class="mb-0"><?= $studentsCount ?></h4>
                    <small class="text-muted">Студентов</small>
                    <small class="d-block mt-1 text-muted"><?= $groupsCount ?> групп</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-calendar-event stat-icon text-warning mb-2"></i>
                    <h4 class="mb-0"><?= count($todayClasses) ?></h4>
                    <small class="text-muted">Занятий сегодня</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-file-earmark-check stat-icon text-success mb-2"></i>
                    <h4 class="mb-0"><?= $pendingWorks ?></h4>
                    <small class="text-muted">Работ на проверке</small>
                </div>
            </div>
        </div>

        <!-- Следующее занятие -->
        <?php if (!empty($todayClasses)): ?>
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card next-class-card">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="bi bi-calendar-day me-2"></i>Расписание на сегодня</h5>
                        <div class="row g-2">
                            <?php foreach ($todayClasses as $c): ?>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between align-items-center p-2" style="background: rgba(255,255,255,0.1); border-radius: 8px;">
                                    <div>
                                        <strong><?= e($c['subject']) ?></strong>
                                        <br><small class="opacity-75"><?= e($c['classroom']) ?></small>
                                    </div>
                                    <small class="fw-bold"><?= substr($c['start_time'], 0, 5) ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
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
                        <span><i class="bi bi-clock-history me-2"></i>Последние оценки</span>
                        <a href="journal.php" class="btn btn-sm btn-outline-light">Журнал</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentGrades)): ?>
                            <p class="text-muted mb-0">Оценок пока нет</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead><tr><th>Студент</th><th>Предмет</th><th>Оценка</th><th>Дата</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($recentGrades as $g): ?>
                                        <tr>
                                            <td><strong><?= e($g['student_name']) ?></strong></td>
                                            <td><?= e($g['subject']) ?></td>
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

            <!-- Статистика по предметам и быстрые действия -->
            <div class="col-md-5">
                <div class="card mb-3">
                    <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Статистика по предметам</div>
                    <div class="card-body">
                        <?php if (empty($subjectStats)): ?>
                            <p class="text-muted mb-0">Нет данных</p>
                        <?php else: ?>
                            <?php foreach ($subjectStats as $s): ?>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <small><?= e($s['subject']) ?></small>
                                    <small><strong><?= round($s['avg_grade'], 1) ?></strong> (<?= $s['count'] ?>)</small>
                                </div>
                                <div class="grade-bar">
                                    <div class="grade-fill bg-info" style="width: <?= ($s['avg_grade'] / 5) * 100 ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><i class="bi bi-lightning me-2"></i>Быстрые действия</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="grades.php" class="btn btn-outline-light"><i class="bi bi-star me-2"></i>Выставить оценку</a>
                            <a href="assignments.php" class="btn btn-outline-light"><i class="bi bi-journal-book me-2"></i>Задания</a>
                            <a href="schedule.php" class="btn btn-outline-light"><i class="bi bi-calendar-week me-2"></i>Расписание</a>
                            <a href="notifications.php" class="btn btn-outline-light"><i class="bi bi-bell me-2"></i>Рассылка</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
