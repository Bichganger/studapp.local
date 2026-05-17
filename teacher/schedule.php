<?php
session_start();
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'teacher') { header('Location: /dashboard.php'); exit; }

$pageTitle = 'Расписание';
$userId = $_SESSION['user_id'];
$fullName = $_SESSION['full_name'];

$stmt = $pdo->prepare("SELECT * FROM schedule WHERE teacher_name = ? ORDER BY FIELD(day_of_week, 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'), start_time");
$stmt->execute([$fullName]);
$schedule = $stmt->fetchAll();

$days = [];
foreach ($schedule as $s) {
    $days[$s['day_of_week']][] = $s;
}

require_once '../includes/header.php';
?>

<style>.day-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 16px; } .day-card h5 { color: var(--accent); }</style>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Преподавание</span><h1 class="section-title">Моё расписание</h1></div>
    
    <?php if (empty($days)): ?>
        <div class="empty-state text-center py-5"><i class="bi bi-calendar-x" style="font-size:3rem;"></i><h4 class="mt-3">Расписание пусто</h4></div>
    <?php else: ?>
        <?php foreach ($days as $day => $lessons): ?>
        <div class="day-card">
            <h5 class="text-capitalize"><i class="bi bi-calendar-date me-2"></i><?= e($day) ?></h5>
            <div class="table-responsive"><table class="table table-sm mb-0">
                <thead><tr><th>Время</th><th>Предмет</th><th>Группа</th><th>Кабинет</th></tr></thead>
                <tbody>
                    <?php foreach ($lessons as $l): ?>
                    <tr>
                        <td><?= substr($l['start_time'],0,5) ?>–<?= substr($l['end_time'],0,5) ?></td>
                        <td><?= e($l['subject']) ?></td>
                        <td><?= e($l['group_name']) ?></td>
                        <td><?= e($l['classroom'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table></div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div></div>

<?php require_once '../includes/footer.php'; ?>
