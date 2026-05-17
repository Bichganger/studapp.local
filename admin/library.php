<?php
session_start();
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

$pageTitle = 'Библиотека';
$works = $pdo->query("SELECT w.*, u.full_name as author FROM works w LEFT JOIN users u ON w.uploaded_by = u.id ORDER BY w.created_at DESC")->fetchAll();

// Удаление
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT file_path FROM works WHERE id = ?");
    $stmt->execute([intval($_GET['delete'])]);
    $w = $stmt->fetch();
    if ($w && file_exists('../' . $w['file_path'])) unlink('../' . $w['file_path']);
    $pdo->prepare("DELETE FROM works WHERE id = ?")->execute([intval($_GET['delete'])]);
    header('Location: library.php?deleted=1');
    exit;
}

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Администрирование</span><h1 class="section-title">Библиотека</h1></div>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-info">Материал удалён</div><?php endif; ?>

    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Название</th><th>Тип</th><th>Автор</th><th>Скачиваний</th><th>Дата</th><th></th></tr></thead>
        <tbody>
            <?php if (empty($works)): ?>
            <tr><td colspan="6" class="text-center">Нет материалов</td></tr>
            <?php else: ?>
            <?php foreach ($works as $w): ?>
            <tr>
                <td><?= e($w['title']) ?></td>
                <td><span class="badge bg-info"><?= e($w['file_type'] ?? 'doc') ?></span></td>
                <td><?= e($w['author'] ?? '—') ?></td>
                <td><?= $w['downloads'] ?></td>
                <td><?= date('d.m.Y', strtotime($w['created_at'])) ?></td>
                <td><a href="?delete=<?= $w['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')"><i class="bi bi-trash"></i></a></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table></div></div></div>
</div></div>

<?php require_once '../includes/footer.php'; ?>
