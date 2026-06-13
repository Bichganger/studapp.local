<?php
require_once '../config/db.php';
require_once '../protected/auth_guard.php';

$user = getCurrentUser();

// Обработка добавления отзыва - ДО подключения header.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'review' && $user) {
    $teacherId = intval($_POST['teacher_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $comment = trim($_POST['review_text'] ?? '');
    
    if ($teacherId > 0 && $rating >= 1 && $rating <= 5) {
        // Проверяем количество существующих отзывов на этого преподавателя
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM teacher_reviews WHERE teacher_id = ? AND is_approved = 1 AND is_hidden = 0");
        $stmt->execute([$teacherId]);
        $reviewCount = $stmt->fetchColumn();
        
        // Ограничиваем до 5 отзывов на преподавателя
        if ($reviewCount >= 5) {
            header('Location: teachers.php?error=limit_reached');
            exit;
        }
        
        // Создаём новый отзыв
        $stmt = $pdo->prepare("INSERT INTO teacher_reviews (teacher_id, student_id, rating, comment, is_approved) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$teacherId, $_SESSION['user_id'], $rating, $comment]);
        
        // Обновляем средний рейтинг (только одобренные и не скрытые отзывы)
        $stmt = $pdo->prepare("UPDATE teachers SET avg_rating = (SELECT ROUND(AVG(rating), 1) FROM teacher_reviews WHERE teacher_id = ? AND is_approved = 1 AND is_hidden = 0) WHERE id = ?");
        $stmt->execute([$teacherId, $teacherId]);
        
        header('Location: teachers.php?success=1');
        exit;
    }
}

$pageTitle = 'Преподаватели';
require_once '../includes/header.php';

// Отображение сообщений об успехе/ошибке
if (isset($_GET['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">';
    echo '<i class="bi bi-check-circle me-2"></i>Отзыв успешно добавлен!';
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
}

if (isset($_GET['error']) && $_GET['error'] === 'limit_reached') {
    echo '<div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">';
    echo '<i class="bi bi-exclamation-triangle me-2"></i>Достигнут лимит отзывов (5) для этого преподавателя.';
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
}

// Проверяем наличие колонок в таблице teachers
$hasCampus = $hasStrict = $hasAutoExam = false;
try {
    $cols = $pdo->query("SHOW COLUMNS FROM teachers")->fetchAll(PDO::FETCH_COLUMN);
    $hasCampus = in_array('campus', $cols);
    $hasStrict = in_array('is_strict', $cols);
    $hasAutoExam = in_array('auto_exam', $cols);
} catch (Exception $e) {}

// Получаем рейтинг через подзапрос если колонки нет
$hasAvgRating = false;
try {
    $hasAvgRating = in_array('avg_rating', $pdo->query("SHOW COLUMNS FROM teachers")->fetchAll(PDO::FETCH_COLUMN));
} catch (Exception $e) {}

$filterCampus = $_GET['campus'] ?? '';
$filterStrict = $_GET['strict'] ?? '';
$where = [];
$params = [];
if ($hasCampus && $filterCampus) { $where[] = "campus = ?"; $params[] = $filterCampus; }
if ($hasStrict && $filterStrict !== '' && $filterStrict !== null) { $where[] = "is_strict = ?"; $params[] = $filterStrict ? 1 : 0; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Если avg_rating нет — считаем из teacher_reviews (только одобренные и не скрытые)
$ratingExpr = $hasAvgRating ? 't.avg_rating' : 'COALESCE((SELECT ROUND(AVG(rating), 1) FROM teacher_reviews r WHERE r.teacher_id = t.id AND r.is_approved = 1 AND r.is_hidden = 0), 0)';
$stmt = $pdo->prepare("SELECT t.*, $ratingExpr as calculated_rating FROM teachers t $whereSql ORDER BY t.full_name");
$stmt->execute($params);
$teachers = $stmt->fetchAll();

$reviewsData = [];
foreach ($teachers as $teacher) {
    // Показываем только одобренные и не скрытые отзывы, максимум 5
    $stmt = $pdo->prepare("SELECT tr.*, u.full_name as user_name FROM teacher_reviews tr LEFT JOIN users u ON tr.student_id = u.id WHERE tr.teacher_id = ? AND tr.is_approved = 1 AND tr.is_hidden = 0 ORDER BY tr.created_at DESC LIMIT 5");
    $stmt->execute([$teacher['id']]);
    $reviewsData[$teacher['id']] = $stmt->fetchAll();
}

$campuses = [];
if ($hasCampus) {
    try {
        $campuses = $pdo->query("SELECT DISTINCT campus FROM teachers WHERE campus IS NOT NULL AND campus != '' ORDER BY campus")->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {}
}
if (empty($campuses)) $campuses = ['Брамса 9', 'Спортивная 6', 'Озерова 7'];

// Функция для отрисовки кругового индикатора рейтинга
function renderRatingCircle(float $rating, int $size = 40, $showNumber = true): string {
    $rating = max(0, min(5, $rating));
    $percentage = ($rating / 5) * 100;
    
    // Цвет в зависимости от рейтинга
    if ($rating < 2) $color = '#ff5252';      // красный
    elseif ($rating < 3.5) $color = '#ffd740'; // жёлтый
    else $color = '#00e676';                   // зелёный
    
    $circumference = 2 * M_PI * ($size / 2 - 4);
    $offset = $circumference - ($percentage / 100) * $circumference;
    
    $html = '<div class="rating-circle" style="width: ' . $size . 'px; height: ' . $size . 'px;" title="Рейтинг: ' . number_format($rating, 1) . '">';
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
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Справочник</span>
            <h1 class="section-title">Преподаватели</h1>
            <p class="section-subtitle">Реальные отзывы от выпускников</p>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-4">
                        <select name="campus" class="form-select">
                            <option value="">Все корпуса</option>
                            <?php foreach ($campuses as $c): ?>
                            <option value="<?= e($c) ?>" <?= $filterCampus === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="strict" class="form-select">
                            <option value="">Все преподаватели</option>
                            <option value="1" <?= $filterStrict === '1' ? 'selected' : '' ?>>Строгие</option>
                            <option value="0" <?= $filterStrict === '0' ? 'selected' : '' ?>>Добрые</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-accent w-100"><i class="bi bi-funnel me-1"></i>Применить фильтр</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($teachers as $teacher): ?>
            <?php $reviews = $reviewsData[$teacher['id']] ?? []; ?>
            <div class="col-lg-6">
                <div class="teacher-card">
                    <div class="teacher-header">
                        <div class="teacher-avatar"><i class="bi bi-person"></i></div>
                        <div class="teacher-info">
                            <h4><?= e($teacher['full_name']) ?></h4>
                            <div class="teacher-subject"><?= e($teacher['specialty'] ?? '') ?></div>
<div class="teacher-meta">
    <span><i class="bi bi-geo-alt me-1"></i><?= e($teacher['campus'] ?? $campuses[0] ?? '—') ?></span>
    <span><i class="bi bi-door-open me-1"></i>Каб. <?= e($teacher['office'] ?? '—') ?></span>
</div>
                        </div>
                    </div>
                    <div class="teacher-body">
                        <div class="teacher-graduate"><i class="bi bi-chat-quote text-accent me-2"></i><?= e($teacher['description'] ?? '') ?></div>
<div class="teacher-tags">
    <?php if ($hasStrict && !empty($teacher['is_strict'])): ?>
    <span class="teacher-tag strict"><i class="bi bi-emoji-frown me-1"></i>Строгий</span>
    <?php else: ?>
    <span class="teacher-tag kind"><i class="bi bi-emoji-smile me-1"></i>Добрый</span>
    <?php endif; ?>
    <?php if ($hasAutoExam && !empty($teacher['auto_exam'])): ?>
    <span class="teacher-tag auto"><i class="bi bi-check-circle me-1"></i>Автоматы</span>
    <?php endif; ?>
</div>
                    </div>
                    <div class="teacher-footer">
<div class="d-flex align-items-center gap-2">
    <?= renderRatingCircle(floatval($teacher['avg_rating'] ?? 0), 40) ?>
    <span class="text-muted small"><?= count($reviews) ?> отзыв<?= count($reviews) === 1 ? '' : (count($reviews) < 5 ? 'а' : 'ов') ?></span>
</div>
                        <button class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#reviews<?= $teacher['id'] ?>"><i class="bi bi-chat-left-text me-1"></i>Отзывы</button>
                    </div>
                    <div class="collapse" id="reviews<?= $teacher['id'] ?>">
                        <div class="p-3" style="border-top: 1px solid var(--border-color);">
                            <?php if (empty($reviews)): ?>
                            <p class="text-muted small mb-0">Пока нет отзывов. Будь первым!</p>
                            <?php else: ?>
                            <div class="mb-3">
                                <?php foreach ($reviews as $review): ?>
                                <div class="comment-item">
                                    <div class="comment-header">
                                        <span class="comment-author"><?= e($review['user_name'] ?? 'Аноним') ?></span>
                                        <div class="d-flex align-items-center gap-2">
                                            <?= renderRatingCircle($review['rating'], 28, false) ?>
                                            <small class="text-muted"><?= $review['rating'] ?>/5</small>
                                        </div>
                                    </div>
                                    <?php if ($review['comment']): ?>
                                    <p class="comment-text mb-0"><?= e($review['comment']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($user): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="review">
                                <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
                                <div class="row g-2 align-items-end">
                                    <div class="col-auto">
                                        <select name="rating" class="form-select form-select-sm rating-select" required style="min-width: 140px;">
                                            <option value="">Оценка</option>
                                            <option value="5">5 - Отлично</option>
                                            <option value="4">4 - Хорошо</option>
                                            <option value="3">3 - Удовлетворительно</option>
                                            <option value="2">2 - Плохо</option>
                                            <option value="1">1 - Очень плохо</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <div class="input-group">
                                            <input type="text" name="review_text" class="form-control form-control-sm" placeholder="Ваш отзыв...">
                                            <button type="submit" class="btn btn-accent btn-sm"><i class="bi bi-send"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php else: ?>
                            <p class="text-muted small mb-0"><a href="/dashboard.php">Войдите</a>, чтобы оставить отзыв</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (empty($teachers)): ?>
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <h4>Преподаватели не найдены</h4>
            <p>Попробуйте изменить фильтры</p>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>
