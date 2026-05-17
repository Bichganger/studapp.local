<?php
session_start();
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'teacher') { header('Location: /dashboard.php'); exit; }

$pageTitle = 'Журнал оценок';
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT g.*, u.full_name as student_name, u.group_name FROM grades g LEFT JOIN users u ON g.student_id = u.id WHERE g.teacher_id = ? ORDER BY g.created_at DESC");
$stmt->execute([$userId]);
$grades = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Преподавание</span><h1 class="section-title">Журнал оценок</h1></div>
    
    <?php if (empty($grades)): ?>
        <div class="empty-state text-center py-5"><i class="bi bi-journal-x" style="font-size:3rem;"></i><h4 class="mt-3" style="color: #f0f2ff;">Журнал пуст</h4></div>
    <?php else: ?>
        <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
            <thead><tr><th style="color: #f0f2ff; font-weight: 600;">Дата</th><th style="color: #f0f2ff; font-weight: 600;">Студент</th><th style="color: #f0f2ff; font-weight: 600;">Группа</th><th style="color: #f0f2ff; font-weight: 600;">Предмет</th><th style="color: #f0f2ff; font-weight: 600;">Оценка</th><th style="color: #f0f2ff; font-weight: 600;">Комментарий</th></tr></thead>
            <tbody>
                <?php foreach ($grades as $g): ?>
                <?php $bc = $g['grade'] >= 4 ? 'bg-success' : ($g['grade'] == 3 ? 'bg-warning' : 'bg-danger'); ?>
                <tr>
                    <td style="color: #d0d5f0;"><?= date('d.m.Y', strtotime($g['created_at'])) ?></td>
                    <td style="color: #d0d5f0;"><?= e($g['student_name'] ?? '—') ?></td>
                    <td style="color: #d0d5f0;"><?= e($g['group_name'] ?? '—') ?></td>
                    <td style="color: #d0d5f0;"><?= e($g['subject']) ?></td>
                    <td><span class="badge <?= $bc ?>"><?= $g['grade'] ?></span></td>
                    <td style="color: #c5c9e8;" class="small"><?= e($g['comment'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div></div></div>
    <?php endif; ?>
</div></div>

<?php require_once '../includes/footer.php'; ?>
