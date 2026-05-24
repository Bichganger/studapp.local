<?php
require_once '../config/db.php';
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'student') { header('Location: ../dashboard.php'); exit; }

$pageTitle = 'Уведомления';
$userId = $_SESSION['user_id'];
$groupName = $_SESSION['group_name'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE target_type IN ('all', 'students') OR target_group = ? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$groupName]);
$notifications = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Оповещения</span>
            <h1 class="section-title">Уведомления</h1>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="empty-state text-center py-5"><i class="bi bi-bell-slash" style="font-size:3rem;"></i><h4 class="mt-3">Нет уведомлений</h4><p class="text-muted">Уведомлений пока нет</p></div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($notifications as $n): ?>
                <div class="list-group-item bg-transparent border-secondary mb-2 rounded-3 <?= $n['is_read'] ? '' : 'border-accent' ?>" style="border: 1px solid var(--border-color);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong><?= e($n['title']) ?></strong>
                            <p class="mb-0 text-muted small"><?= e($n['message']) ?></p>
                        </div>
                        <small class="text-muted"><?= date('d.m.Y H:i', strtotime($n['created_at'])) ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
