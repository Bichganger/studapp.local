<?php
session_start();
require_once '../config/db.php';
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'student') { header('Location: ../dashboard.php'); exit; }

$pageTitle = 'Мои работы';
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM assignments WHERE student_id = ? ORDER BY submitted_at ASC");
$stmt->execute([$userId]);
$assignments = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Задания</span>
            <h1 class="section-title">Мои работы</h1>
        </div>

        <?php if (empty($assignments)): ?>
            <div class="empty-state text-center py-5"><i class="bi bi-file-text" style="font-size:3rem;"></i><h4 class="mt-3">Нет заданий</h4><p class="text-muted">Задания пока не назначены</p></div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($assignments as $a): ?>
                <?php 
                $statusClass = match($a['status'] ?? 'pending') {
                    'done' => 'border-success',
                    'overdue' => 'border-danger',
                    default => 'border-warning'
                };
                ?>
                <div class="col-md-6">
                    <div class="card <?= $statusClass ?>" style="border-left: 4px solid;">
                        <div class="card-body">
                            <h5><?= e($a['title']) ?></h5>
                            <p class="text-muted small"><?= e($a['description'] ?? '') ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="bi bi-clock me-1"></i>Подано: <?= $a['submitted_at'] ? date('d.m.Y H:i', strtotime($a['submitted_at'])) : '—' ?></span>
                                <span class="badge bg-<?= $a['status'] === 'done' ? 'success' : ($a['status'] === 'overdue' ? 'danger' : 'warning') ?>"><?= $a['status'] === 'done' ? 'Выполнено' : ($a['status'] === 'overdue' ? 'Просрочено' : 'В работе') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
