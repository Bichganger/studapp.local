<?php
session_start();
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'teacher') { header('Location: /dashboard.php'); exit; }

$pageTitle = 'Работы студентов';
$assignments = $pdo->query("SELECT a.*, u.full_name as student_name, u.group_name FROM assignments a LEFT JOIN users u ON a.student_id = u.id ORDER BY a.due_date DESC")->fetchAll();

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Преподавание</span><h1 class="section-title">Работы студентов</h1></div>
    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Студент</th><th>Группа</th><th>Название</th><th>Описание</th><th>Срок</th><th>Статус</th></tr></thead>
        <tbody>
            <?php if (empty($assignments)): ?>
            <tr><td colspan="6" class="text-center">Нет работ</td></tr>
            <?php else: ?>
            <?php foreach ($assignments as $a): ?>
            <?php $sb = match($a['status'] ?? 'pending') { 'done' => 'bg-success', 'overdue' => 'bg-danger', default => 'bg-warning' }; ?>
            <tr>
                <td><?= e($a['student_name'] ?? '—') ?></td>
                <td><?= e($a['group_name'] ?? '—') ?></td>
                <td><?= e($a['title']) ?></td>
                <td class="text-muted small"><?= e(mb_substr($a['description'] ?? '', 0, 60)) ?></td>
                <td><?= date('d.m.Y', strtotime($a['due_date'])) ?></td>
                <td><span class="badge <?= $sb ?>"><?= $a['status'] === 'done' ? 'Выполнено' : ($a['status'] === 'overdue' ? 'Просрочено' : 'В работе') ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table></div></div></div>
</div></div>

<?php require_once '../includes/footer.php'; ?>
