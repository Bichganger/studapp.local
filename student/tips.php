<?php
require_once '../config/db.php';
require_once '../protected/auth_guard.php';

$user = getCurrentUser();
$pageTitle = 'Советы';

// Обработка лайков (POST запрос) - ДО подключения header.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'vote' && $user) {
    $tipId = intval($_POST['tip_id'] ?? 0);
    if ($tipId > 0) {
        $stmt = $pdo->prepare("SELECT id FROM tips WHERE id = ?");
        $stmt->execute([$tipId]);
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("UPDATE tips SET votes = votes + 1 WHERE id = ?");
            $stmt->execute([$tipId]);
        }
    }
    header('Location: tips.php' . ($_GET['category'] ?? '' ? '?category=' . urlencode($_GET['category']) : ''));
    exit;
}

// Обработка добавления совета - ДО подключения header.php
$tipErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_tip' && $user) {
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

// Получение советов из таблицы tips
$filterCategory = $_GET['category'] ?? '';

$where = [];
$params = [];
if ($filterCategory) { $where[] = "category = ?"; $params[] = $filterCategory; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT t.*, u.full_name as author_name FROM tips t LEFT JOIN users u ON t.author_id = u.id $whereSql ORDER BY t.votes DESC, t.created_at DESC");
$stmt->execute($params);
$tips = $stmt->fetchAll();

// Получение категорий
$stmt = $pdo->query("SELECT DISTINCT category FROM tips WHERE category IS NOT NULL AND category != '' ORDER BY category");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
if (empty($categories)) $categories = ['exam', 'coursework', 'lab', 'general', 'lifehack'];

$categoryLabels = [
    'general' => 'Общее',
    'exam' => 'Экзамены',
    'coursework' => 'Курсовые',
    'lab' => 'Лабораторные',
    'lifehack' => 'Лайфхаки',
];

require_once '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Советы</span>
            <h1 class="section-title">Советы по обучению</h1>
            <p class="section-subtitle">Проверенные рекомендации от студентов и преподавателей</p>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Совет добавлен!</div>
        <?php endif; ?>
        <?php if (!empty($tipErrors)): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($tipErrors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="tips.php" class="btn btn-sm <?= $filterCategory === '' ? 'btn-accent' : 'btn-outline-light' ?>">Все</a>
                            <?php foreach ($categories as $cat): ?>
                            <a href="?category=<?= urlencode($cat) ?>" class="btn btn-sm <?= $filterCategory === $cat ? 'btn-accent' : 'btn-outline-light' ?>">
                                <?= e($categoryLabels[$cat] ?? $cat) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <?php if ($user): ?>
                        <button class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#addTipModal">
                            <i class="bi bi-plus-lg me-1"></i>Добавить совет
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (empty($tips)): ?>
        <div class="empty-state text-center py-5">
            <i class="bi bi-lightbulb" style="font-size: 3rem;"></i>
            <h4 class="mt-3">Советов пока нет</h4>
            <p class="text-muted">Будьте первым!</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($tips as $tip): ?>
            <div class="col-lg-6">
                <div class="tip-card">
                    <span class="tip-specialty" style="background: var(--accent); color: var(--bg-dark); padding: 2px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                        <?= e($categoryLabels[$tip['category']] ?? $tip['category']) ?>
                    </span>
                    <h5 class="mt-3 mb-2"><?= e($tip['title']) ?></h5>
                    <p class="tip-text"><?= nl2br(e($tip['content'])) ?></p>
                    <div class="tip-footer">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted small"><i class="bi bi-person me-1"></i><?= e($tip['author_name'] ?? 'Студент') ?></span>
                            <span class="text-muted small"><i class="bi bi-calendar me-1"></i><?= date('d.m.Y', strtotime($tip['created_at'])) ?></span>
                        </div>
                        <?php if ($user): ?>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="action" value="vote">
                            <input type="hidden" name="tip_id" value="<?= $tip['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-light" onclick="this.closest('.tip-card').querySelector('.vote-count').textContent = (parseInt(this.closest('.tip-card').querySelector('.vote-count').textContent) || 0) + 1;">
                                <i class="bi bi-hand-thumbs-up me-1"></i><span class="vote-count"><?= $tip['votes'] ?></span>
                            </button>
                        </form>
                        <?php else: ?>
                        <span class="text-muted small"><i class="bi bi-hand-thumbs-up me-1"></i><?= $tip['votes'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($user): ?>
<div class="modal fade" id="addTipModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Добавить совет</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="action" value="add_tip">
                <div class="mb-3">
                    <label class="form-label">Название *</label>
                    <input type="text" name="title" class="form-control" required placeholder="Например: Как подготовиться к экзамену за неделю">
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
                    <textarea name="content" class="form-control" rows="5" required placeholder="Поделитесь полезным советом..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" class="btn btn-accent">Добавить</button>
            </div>
        </form>
    </div></div>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
