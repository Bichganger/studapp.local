<?php
session_start();
require_once '../config/db.php';
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'student') { header('Location: ../dashboard.php'); exit; }

$pageTitle = 'Оценки';
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT g.*, u.full_name as teacher_name FROM grades g LEFT JOIN users u ON g.teacher_id = u.id WHERE g.student_id = ? ORDER BY g.created_at DESC");
$stmt->execute([$userId]);
$grades = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Успеваемость</span>
            <h1 class="section-title">Мои оценки</h1>
        </div>

        <?php if (empty($grades)): ?>
            <div class="empty-state text-center py-5"><i class="bi bi-star" style="font-size:3rem;"></i><h4 class="mt-3">Нет оценок</h4><p class="text-muted">Оценки пока не выставлены</p></div>
        <?php else: ?>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>Дата</th><th>Предмет</th><th>Оценка</th><th>Комментарий</th><th>Преподаватель</th></tr></thead>
                            <tbody>
                                <?php foreach ($grades as $g): ?>
                                <?php $bc = $g['grade'] >= 4 ? 'bg-success' : ($g['grade'] == 3 ? 'bg-warning' : 'bg-danger'); ?>
                                <tr>
                                    <td><?= date('d.m.Y', strtotime($g['created_at'])) ?></td>
                                    <td><?= e($g['subject']) ?></td>
                                    <td><span class="badge <?= $bc ?>"><?= $g['grade'] ?></span></td>
                                    <td class="text-muted small"><?= e($g['comment'] ?? '—') ?></td>
                                    <td><?= e($g['teacher_name'] ?? '—') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
