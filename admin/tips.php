<?php
session_start();
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

$user = getCurrentUser();
$pageTitle = 'Советы';

// Добавление совета
$tipErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_tip') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? 'general');
    
    if (empty($title)) $tipErrors[] = 'Укажите название совета';
    if (empty($content) || mb_strlen($content) < 20) $tipErrors[] = 'Совет должен содержать минимум 20 символов';
    
    if (empty($tipErrors)) {
        $stmt = $pdo->prepare("INSERT INTO tips (title, content, category, author_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $category, $_SESSION['user_id']]);
        header('Location: tips.php?success=1');
        exit;
    }
}

$tips = $pdo->query("SELECT t.*, u.full_name as author FROM tips t LEFT JOIN users u ON t.author_id = u.id ORDER BY t.created_at DESC")->fetchAll();

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM tips WHERE id = ?")->execute([intval($_GET['delete'])]);
    header('Location: tips.php?deleted=1');
    exit;
}

$categoryLabels = [
    'general' => 'Общее',
    'exam' => 'Экзамены',
    'coursework' => 'Курсовые',
    'lab' => 'Лабораторные',
    'lifehack' => 'Лайфхаки',
];
require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Администрирование</span><h1 class="section-title">Советы</h1></div>
    <?php if (isset($_GET['success'])): ?><div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Совет добавлен!</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-info">Совет удалён</div><?php endif; ?>
    <?php if (!empty($tipErrors)): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($tipErrors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card"><div class="card-header"><i class="bi bi-plus-circle me-2"></i>Добавить совет</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add_tip">
                        <div class="mb-3">
                            <label class="form-label">Название *</label>
                            <input type="text" name="title" class="form-control" required placeholder="Название совета">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Категория *</label>
                            <select name="category" class="form-select" required>
                                <option value="general">Общее</option>
                                <option value="exam">Экзамены</option>
                                <option value="coursework">Курсовые</option>
                                <option value="lab">Лабораторные</option>
                                <option value="lifehack">Лайфхаки</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Совет * (мин. 20 символов)</label>
                            <textarea name="content" class="form-control" rows="5" required placeholder="Текст совета..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-accent w-100">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <?php if (empty($tips)): ?>
            <div class="card"><div class="card-body text-center py-5"><i class="bi bi-lightbulb" style="font-size:3rem;"></i><h4 class="mt-3">Нет советов</h4></div></div>
            <?php else: ?>
            <?php foreach ($tips as $t): ?>
            <div class="card mb-3"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-accent text-dark"><?= e($categoryLabels[$t['category']] ?? $t['category']) ?></span>
                    <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')"><i class="bi bi-trash"></i></a>
                </div>
                <h5><?= e($t['title']) ?></h5>
                <p class="text-muted small"><?= e($t['content']) ?></p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted"><?= e($t['author'] ?? '—') ?> · <?= date('d.m.Y', strtotime($t['created_at'])) ?></small>
                    <small><i class="bi bi-hand-thumbs-up me-1"></i><?= $t['votes'] ?></small>
                </div>
            </div></div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div></div>

<?php require_once '../includes/footer.php'; ?>
