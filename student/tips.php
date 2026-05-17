<?php
session_start();
require_once '../protected/auth_guard.php';
if (!in_array($_SESSION['role'], ['student', 'admin'])) { header('Location: ../dashboard.php'); exit; }
$pageTitle = 'Советы по обучению';
require_once '../includes/header.php';
$specialties = $pdo->query("SELECT DISTINCT specialty FROM tips ORDER BY specialty")->fetchAll(PDO::FETCH_COLUMN);
$filterSpecialty = $_GET['specialty'] ?? '';
if ($filterSpecialty) {
    $stmt = $pdo->prepare("SELECT t.*, u.name as author_name, (SELECT COUNT(*) FROM tip_likes WHERE tip_id = t.id) as like_count FROM tips t LEFT JOIN users u ON t.user_id = u.id WHERE t.specialty = ? ORDER BY t.likes DESC, t.created_at DESC");
    $stmt->execute([$filterSpecialty]);
} else {
    $stmt = $pdo->query("SELECT t.*, u.name as author_name, (SELECT COUNT(*) FROM tip_likes WHERE tip_id = t.id) as like_count FROM tips t LEFT JOIN users u ON t.user_id = u.id ORDER BY t.likes DESC, t.created_at DESC");
}
$tips = $stmt->fetchAll();
if (isset($_GET['like']) && is_numeric($_GET['like']) && $user) {
    $tipId = intval($_GET['like']);
    $stmt = $pdo->prepare("SELECT id FROM tip_likes WHERE tip_id = ? AND user_id = ?");
    $stmt->execute([$tipId, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        $pdo->prepare("INSERT INTO tip_likes (tip_id, user_id) VALUES (?, ?)")->execute([$tipId, $_SESSION['user_id']]);
        $pdo->prepare("UPDATE tips SET likes = likes + 1 WHERE id = ?")->execute([$tipId]);
    }
    redirect('tips.php' . ($filterSpecialty ? '?specialty=' . urlencode($filterSpecialty) : ''));
}
$tipErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'suggest_tip' && $user) {
    $specialty = trim($_POST['specialty'] ?? '');
    $tipText = trim($_POST['tip_text'] ?? '');
    if (empty($specialty)) $tipErrors[] = 'Укажите специальность';
    if (empty($tipText) || mb_strlen($tipText) < 20) $tipErrors[] = 'Совет должен содержать минимум 20 символов';
    if (empty($tipErrors)) {
        if (hasRole('admin')) {
            $stmt = $pdo->prepare("INSERT INTO tips (user_id, specialty, tip_text) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $specialty, $tipText]);
            redirect('tips.php', 'Совет добавлен!', 'success');
        } else {
            redirect('tips.php', 'Спасибо! Ваш совет отправлен на модерацию.', 'success');
        }
    }
}
?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Лайфхаки</span>
            <h1 class="section-title">Советы по обучению</h1>
            <p class="section-subtitle">Проверенные рекомендации от выпускников</p>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="tips.php" class="btn btn-sm <?= $filterSpecialty === '' ? 'btn-accent' : 'btn-outline-light' ?>">Все специальности</a>
                            <?php foreach ($specialties as $spec): ?>
                            <a href="?specialty=<?= urlencode($spec) ?>" class="btn btn-sm <?= $filterSpecialty === $spec ? 'btn-accent' : 'btn-outline-light' ?>"><?= e($spec) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <?php if ($user && hasRole('admin')): ?>
                        <button class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#addTipModal"><i class="bi bi-plus-lg me-1"></i>Добавить совет</button>
                        <?php elseif ($user): ?>
                        <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#suggestTipModal"><i class="bi bi-lightbulb me-1"></i>Предложить совет</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($tips as $tip): ?>
            <div class="col-lg-6">
                <div class="tip-card">
                    <span class="tip-specialty"><?= e($tip['specialty']) ?></span>
                    <p class="tip-text"><?= nl2br(e($tip['tip_text'])) ?></p>
                    <div class="tip-footer">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted small"><i class="bi bi-person me-1"></i><?= e($tip['author_name'] ?? 'Выпускник') ?></span>
                            <span class="text-muted small"><i class="bi bi-calendar me-1"></i><?= date('d.m.Y', strtotime($tip['created_at'])) ?></span>
                        </div>
                        <?php if ($user): ?>
                        <a href="?like=<?= $tip['id'] ?><?= $filterSpecialty ? '&specialty=' . urlencode($filterSpecialty) : '' ?>" class="btn btn-like"><i class="bi bi-hand-thumbs-up me-1"></i><?= $tip['likes'] + ($tip['like_count'] ?? 0) ?></a>
                        <?php else: ?>
                        <span class="text-muted small"><i class="bi bi-hand-thumbs-up me-1"></i><?= $tip['likes'] + ($tip['like_count'] ?? 0) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (empty($tips)): ?>
        <div class="empty-state"><i class="bi bi-lightbulb"></i><h4>Советов пока нет</h4><p>Будьте первым!</p></div>
        <?php endif; ?>
    </div>
</section>
<?php if ($user && hasRole('admin')): ?>
<div class="modal fade" id="addTipModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Добавить совет</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="suggest_tip">
                <div class="mb-3"><label class="form-label">Специальность *</label><input type="text" name="specialty" class="form-control" list="specList" required><datalist id="specList"><?php foreach ($specialties as $s): ?><option value="<?= e($s) ?>"><?php endforeach; ?><option value="Программирование"><option value="Дизайн"><option value="Экономика"><option value="Общие советы"></datalist></div>
                <div class="mb-3"><label class="form-label">Совет *</label><textarea name="tip_text" class="form-control" rows="5" required placeholder="Поделитесь полезным советом..."></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Отмена</button><button type="submit" class="btn btn-accent">Добавить</button></div>
        </form>
    </div></div>
</div>
<?php endif; ?>
<?php if ($user && !hasRole('admin')): ?>
<div class="modal fade" id="suggestTipModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-lightbulb me-2"></i>Предложить совет</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="">
            <div class="modal-body">
                <?php if (!empty($tipErrors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($tipErrors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
                <input type="hidden" name="action" value="suggest_tip">
                <div class="mb-3"><label class="form-label">Специальность *</label><input type="text" name="specialty" class="form-control" list="specList2" required><datalist id="specList2"><?php foreach ($specialties as $s): ?><option value="<?= e($s) ?>"><?php endforeach; ?></datalist></div>
                <div class="mb-3"><label class="form-label">Ваш совет *</label><textarea name="tip_text" class="form-control" rows="5" required placeholder="Расскажите, что помогло вам в учёбе..."></textarea></div>
                <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Совет будет проверен администратором.</p>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Отмена</button><button type="submit" class="btn btn-accent">Отправить</button></div>
        </form>
    </div></div>
</div>
<?php endif; ?>
<?php require_once '../includes/footer.php'; ?>
