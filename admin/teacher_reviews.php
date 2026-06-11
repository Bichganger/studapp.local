<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

$user = getCurrentUser();
$pageTitle = 'Модерация отзывов';

// Обработка действий с отзывами
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewId = intval($_POST['review_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    
    if ($reviewId > 0) {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE teacher_reviews SET is_approved = 1, moderated_at = NOW(), moderated_by = ? WHERE id = ?");
            $stmt->execute([$_SESSION['user_id'], $reviewId]);
        } elseif ($action === 'hide') {
            $stmt = $pdo->prepare("UPDATE teacher_reviews SET is_hidden = 1, moderated_at = NOW(), moderated_by = ? WHERE id = ?");
            $stmt->execute([$_SESSION['user_id'], $reviewId]);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM teacher_reviews WHERE id = ?");
            $stmt->execute([$reviewId]);
            header('Location: teacher_reviews.php');
            exit;
        }
        
        // Пересчитываем рейтинг преподавателя
        $stmt = $pdo->prepare("SELECT teacher_id FROM teacher_reviews WHERE id = ?");
        $stmt->execute([$reviewId]);
        if ($teacher = $stmt->fetch()) {
            $stmt = $pdo->prepare("UPDATE teachers SET avg_rating = (SELECT ROUND(AVG(rating), 1) FROM teacher_reviews WHERE teacher_id = ? AND is_approved = 1 AND is_hidden = 0) WHERE id = ?");
            $stmt->execute([$teacher['teacher_id'], $teacher['teacher_id']]);
        }
        
        header('Location: teacher_reviews.php?success=1');
        exit;
    }
}

// Фильтрация
$filterStatus = $_GET['status'] ?? 'pending'; // pending, approved, hidden, all
$where = [];
$params = [];

if ($filterStatus === 'pending') {
    $where[] = "tr.is_approved = 0";
} elseif ($filterStatus === 'approved') {
    $where[] = "tr.is_approved = 1 AND tr.is_hidden = 0";
} elseif ($filterStatus === 'hidden') {
    $where[] = "tr.is_hidden = 1";
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Получение отзывов
$stmt = $pdo->prepare("
    SELECT tr.*, t.full_name as teacher_name, u.full_name as student_name, u.group_name
    FROM teacher_reviews tr
    JOIN teachers t ON tr.teacher_id = t.id
    JOIN users u ON tr.student_id = u.id
    $whereSql
    ORDER BY tr.created_at DESC
");
$stmt->execute($params);
$reviews = $stmt->fetchAll();

// Статистика
$stats = [
    'pending' => $pdo->query("SELECT COUNT(*) FROM teacher_reviews WHERE is_approved = 0")->fetchColumn(),
    'approved' => $pdo->query("SELECT COUNT(*) FROM teacher_reviews WHERE is_approved = 1 AND is_hidden = 0")->fetchColumn(),
    'hidden' => $pdo->query("SELECT COUNT(*) FROM teacher_reviews WHERE is_hidden = 1")->fetchColumn(),
];

require_once '../includes/header.php';

// Функция для отрисовки кругового индикатора рейтинга
function renderRatingCircle(float $rating, int $size = 36, $showNumber = true): string {
    $rating = max(0, min(5, $rating));
    $percentage = ($rating / 5) * 100;
    
    if ($rating < 2) $color = '#ff5252';
    elseif ($rating < 3.5) $color = '#ffd740';
    else $color = '#00e676';
    
    $circumference = 2 * M_PI * ($size / 2 - 4);
    $offset = $circumference - ($percentage / 100) * $circumference;
    
    $html = '<div class="rating-circle" style="width: ' . $size . 'px; height: ' . $size . 'px;">';
    $html .= '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '">';
    $html .= '<circle cx="' . ($size/2) . '" cy="' . ($size/2) . '" r="' . ($size/2 - 4) . '" stroke="var(--border-color)" stroke-width="4" fill="none"/>';
    $html .= '<circle cx="' . ($size/2) . '" cy="' . ($size/2) . '" r="' . ($size/2 - 4) . '" stroke="' . $color . '" stroke-width="4" fill="none" stroke-dasharray="' . $circumference . '" stroke-dashoffset="' . $offset . '" transform="rotate(-90 ' . ($size/2) . ' ' . ($size/2) . ')" stroke-linecap="round"/>';
    $html .= '</svg>';
    if ($showNumber) {
        $html .= '<span class="rating-circle-number" style="color: ' . $color . ';">' . number_format($rating, 1) . '</span>';
    }
    $html .= '</div>';
    
    return $html;
}
?>

<div class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Администрирование</span>
            <h1 class="section-title">Модерация отзывов</h1>
            <p class="section-subtitle">Проверка и управление отзывами о преподавателях</p>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Действие выполнено успешно!</div>
        <?php endif; ?>
        
        <!-- Фильтры -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="btn-group" role="group">
                    <a href="?status=pending" class="btn btn-sm <?= $filterStatus === 'pending' ? 'btn-accent' : 'btn-outline-light' ?>">
                        <i class="bi bi-hourglass-split me-1"></i>Ожидают (<?= $stats['pending'] ?>)
                    </a>
                    <a href="?status=approved" class="btn btn-sm <?= $filterStatus === 'approved' ? 'btn-accent' : 'btn-outline-light' ?>">
                        <i class="bi bi-check-circle me-1"></i>Одобренные (<?= $stats['approved'] ?>)
                    </a>
                    <a href="?status=hidden" class="btn btn-sm <?= $filterStatus === 'hidden' ? 'btn-accent' : 'btn-outline-light' ?>">
                        <i class="bi bi-eye-slash me-1"></i>Скрытые (<?= $stats['hidden'] ?>)
                    </a>
                    <a href="?status=all" class="btn btn-sm <?= $filterStatus === 'all' ? 'btn-accent' : 'btn-outline-light' ?>">
                        <i class="bi bi-list-ul me-1"></i>Все
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Список отзывов -->
        <?php if (empty($reviews)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle display-4 text-muted"></i>
                <h4 class="mt-3">Нет отзывов</h4>
                <p class="text-muted">Все отзывы обработаны</p>
            </div>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($reviews as $review): ?>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= e($review['teacher_name']) ?></strong>
                            <small class="text-muted d-block"><?= e($review['student_name']) ?> (<?= e($review['group_name']) ?>)</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <?= renderRatingCircle($review['rating']) ?>
                            <span class="badge bg-<?= $review['is_approved'] && !$review['is_hidden'] ? 'success' : ($review['is_hidden'] ? 'danger' : 'warning') ?>">
                                <?= $review['is_hidden'] ? 'Скрыт' : ($review['is_approved'] ? 'Одобрен' : 'На проверке') ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($review['comment']): ?>
                        <p class="mb-0"><?= e($review['comment']) ?></p>
                        <?php else: ?>
                        <p class="text-muted mb-0"><em>Текст отзыва отсутствует</em></p>
                        <?php endif; ?>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-clock me-1"></i><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?>
                        </small>
                    </div>
                    <div class="card-footer bg-transparent">
                        <?php if (!$review['is_approved']): ?>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-accent">
                                <i class="bi bi-check-lg me-1"></i>Одобрить
                            </button>
                            <button type="submit" name="action" value="hide" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-eye-slash me-1"></i>Скрыть
                            </button>
                            <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить отзыв?')">
                                <i class="bi bi-trash me-1"></i>Удалить
                            </button>
                        </form>
                        <?php elseif (!$review['is_hidden']): ?>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                            <button type="submit" name="action" value="hide" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-eye-slash me-1"></i>Скрыть
                            </button>
                            <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить отзыв?')">
                                <i class="bi bi-trash me-1"></i>Удалить
                            </button>
                        </form>
                        <?php else: ?>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-accent">
                                <i class="bi bi-eye me-1"></i>Показать
                            </button>
                            <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить отзыв?')">
                                <i class="bi bi-trash me-1"></i>Удалить
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
