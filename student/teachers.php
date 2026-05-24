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
        $stmt = $pdo->prepare("INSERT INTO teacher_reviews (teacher_id, student_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$teacherId, $_SESSION['user_id'], $rating, $comment]);
        
        // Обновляем средний рейтинг
        $stmt = $pdo->prepare("UPDATE teachers SET avg_rating = (SELECT ROUND(AVG(rating), 1) FROM teacher_reviews WHERE teacher_id = ?) WHERE id = ?");
        $stmt->execute([$teacherId, $teacherId]);
        
        header('Location: teachers.php?success=1');
        exit;
    }
}

$pageTitle = 'Преподаватели';
require_once '../includes/header.php';

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

// Если avg_rating нет — считаем из teacher_reviews
$ratingExpr = $hasAvgRating ? 't.avg_rating' : 'COALESCE((SELECT ROUND(AVG(rating), 1) FROM teacher_reviews r WHERE r.teacher_id = t.id), 0)';
$stmt = $pdo->prepare("SELECT t.*, $ratingExpr as calculated_rating FROM teachers t $whereSql ORDER BY t.full_name");
$stmt->execute($params);
$teachers = $stmt->fetchAll();

$reviewsData = [];
foreach ($teachers as $teacher) {
    $stmt = $pdo->prepare("SELECT tr.*, u.full_name as user_name FROM teacher_reviews tr LEFT JOIN users u ON tr.student_id = u.id WHERE tr.teacher_id = ? ORDER BY tr.created_at DESC");
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
function renderStars(float $rating): string {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) $html .= '<i class="bi bi-star-fill"></i>';
        elseif ($i - 0.5 <= $rating) $html .= '<i class="bi bi-star-half"></i>';
        else $html .= '<i class="bi bi-star"></i>';
    }
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
    <span class="rating-stars"><?= renderStars(floatval($teacher['avg_rating'] ?? $teacher['calculated_rating'] ?? 0)) ?></span>
    <span class="text-muted small"><?= number_format(floatval($teacher['avg_rating'] ?? $teacher['calculated_rating'] ?? 0), 1) ?> (<?= count($reviews) ?>)</span>
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
                                        <span class="rating-stars" style="font-size: 0.75rem;"><?= renderStars($review['rating']) ?></span>
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
                                <div class="row g-2">
                                    <div class="col-4">
                                        <select name="rating" class="form-select form-select-sm" required>
                                            <option value="">Оценка</option>
                                            <option value="5">5 ★</option><option value="4">4 ★</option><option value="3">3 ★</option><option value="2">2 ★</option><option value="1">1 ★</option>
                                        </select>
                                    </div>
                                    <div class="col-8">
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
