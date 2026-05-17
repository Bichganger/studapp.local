<?php
session_start();
require_once '../config/db.php';
if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

$pageTitle = 'Преподаватели';

// Проверяем наличие колонки avg_rating
$hasAvgRating = false;
try {
    $colCheck = $pdo->query("SHOW COLUMNS FROM teachers LIKE 'avg_rating'");
    $hasAvgRating = $colCheck->rowCount() > 0;
} catch (Exception $e) {}

if ($hasAvgRating) {
    $teachers = $pdo->query("SELECT t.*, (SELECT COUNT(*) FROM teacher_reviews r WHERE r.teacher_id = t.id) as review_count FROM teachers t ORDER BY t.full_name")->fetchAll();
} else {
    $teachers = $pdo->query("SELECT t.*, COALESCE((SELECT ROUND(AVG(r.rating), 1) FROM teacher_reviews r WHERE r.teacher_id = t.id), 0) as avg_rating, (SELECT COUNT(*) FROM teacher_reviews r WHERE r.teacher_id = t.id) as review_count FROM teachers t ORDER BY t.full_name")->fetchAll();
}

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Администрирование</span><h1 class="section-title">Преподаватели</h1></div>
    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>ID</th><th>ФИО</th><th>Специализация</th><th>Корпус</th><th>Кабинет</th><th>Рейтинг</th><th>Отзывов</th></tr></thead>
        <tbody>
            <?php if (empty($teachers)): ?>
            <tr><td colspan="7" class="text-center py-3">Нет преподавателей</td></tr>
            <?php else: ?>
            <?php foreach ($teachers as $t): ?>
            <tr>
                <td><?= $t['id'] ?></td>
                <td><strong><?= e($t['full_name']) ?></strong></td>
                <td><?= e($t['specialty'] ?? '—') ?></td>
                <td><?= e($t['campus'] ?? $t['office'] ?? '—') ?></td>
                <td><?= e($t['office'] ?? '—') ?></td>
                <td><?= number_format(floatval($t['avg_rating'] ?? 0), 1) ?></td>
                <td><?= $t['review_count'] ?? 0 ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table></div></div></div>
</div></div>

<?php require_once '../includes/footer.php'; ?>
