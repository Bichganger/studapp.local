<?php
session_start();
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

$pageTitle = 'Советы';
$tips = $pdo->query("SELECT t.*, u.full_name as author FROM tips t LEFT JOIN users u ON t.author_id = u.id ORDER BY t.created_at DESC")->fetchAll();

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM tips WHERE id = ?")->execute([intval($_GET['delete'])]);
    header('Location: tips.php?deleted=1');
    exit;
}

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Администрирование</span><h1 class="section-title">Советы</h1></div>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-info">Совет удалён</div><?php endif; ?>

    <div class="row g-4">
        <?php if (empty($tips)): ?>
        <div class="col-12 text-center py-5"><i class="bi bi-lightbulb" style="font-size:3rem;"></i><h4 class="mt-3">Нет советов</h4></div>
        <?php else: ?>
        <?php foreach ($tips as $t): ?>
        <div class="col-lg-6">
            <div class="card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-accent text-dark"><?= e($t['category']) ?></span>
                    <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')"><i class="bi bi-trash"></i></a>
                </div>
                <h5><?= e($t['title']) ?></h5>
                <p class="text-muted small"><?= e($t['content']) ?></p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted"><?= e($t['author'] ?? '—') ?> · <?= date('d.m.Y', strtotime($t['created_at'])) ?></small>
                    <small><i class="bi bi-hand-thumbs-up me-1"></i><?= $t['votes'] ?></small>
                </div>
            </div></div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div></div>

<?php require_once '../includes/footer.php'; ?>
