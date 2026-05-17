<?php
session_start();
require_once '../protected/auth_guard.php';
if (!in_array($_SESSION['role'], ['student', 'admin'])) { header('Location: ../dashboard.php'); exit; }
$pageTitle = 'Преподаватели';
require_once '../includes/header.php';
$filterCampus = $_GET['campus'] ?? '';
$filterStrict = $_GET['strict'] ?? '';
$where = [];
$params = [];
if ($filterCampus) { $where[] = "campus = ?"; $params[] = $filterCampus; }
if ($filterStrict !== '') { $where[] = "is_strict = ?"; $params[] = $filterStrict ? 1 : 0; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $pdo->prepare("SELECT * FROM teachers $whereSql ORDER BY name");
$stmt->execute($params);
$teachers = $stmt->fetchAll();
$reviewsData = [];
foreach ($teachers as $teacher) {
    $stmt = $pdo->prepare("SELECT tr.*, u.name as user_name FROM teacher_reviews tr LEFT JOIN users u ON tr.user_id = u.id WHERE tr.teacher_id = ? ORDER BY tr.created_at DESC");
    $stmt->execute([$teacher['id']]);
    $reviewsData[$teacher['id']] = $stmt->fetchAll();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'review') {
    if (!isLoggedIn()) { redirect('map.php', 'Для добавления отзыва необходимо авторизоваться', 'warning'); }
    $teacherId = intval($_POST['teacher_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $reviewText = trim($_POST['review_text'] ?? '');
    if ($teacherId <= 0 || $rating < 1 || $rating > 5) { redirect('teachers.php', 'Некорректные данные отзыва', 'danger'); }
    $stmt = $pdo->prepare("INSERT INTO teacher_reviews (teacher_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->execute([$teacherId, $_SESSION['user_id'], $rating, $reviewText]);
    $stmt = $pdo->prepare("UPDATE teachers SET avg_rating = (SELECT AVG(rating) FROM teacher_reviews WHERE teacher_id = ?) WHERE id = ?");
    $stmt->execute([$teacherId, $teacherId]);
    redirect('teachers.php', 'Отзыв добавлен!', 'success');
}
$campuses = ['Брамса 9', 'Спортивная 6', 'Озерова 7'];
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
                            <h4><?= e($teacher['name']) ?></h4>
                            <div class="teacher-subject"><?= e($teacher['subject']) ?></div>
                            <div class="teacher-meta">
                                <span><i class="bi bi-geo-alt me-1"></i><?= e($teacher['campus']) ?></span>
                                <span><i class="bi bi-door-open me-1"></i>Каб. <?= e($teacher['cabinet']) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="teacher-body">
                        <div class="teacher-graduate"><i class="bi bi-chat-quote text-purple me-2"></i><?= e($teacher['description_graduate']) ?></div>
                        <div class="teacher-tags">
                            <?php if ($teacher['is_strict']): ?>
                            <span class="teacher-tag strict"><i class="bi bi-emoji-frown me-1"></i>Строгий</span>
                            <?php else: ?>
                            <span class="teacher-tag kind"><i class="bi bi-emoji-smile me-1"></i>Добрый</span>
                            <?php endif; ?>
                            <?php if ($teacher['auto_exam']): ?>
                            <span class="teacher-tag auto"><i class="bi bi-check-circle me-1"></i>Автоматы</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="teacher-footer">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rating-stars"><?= renderStars(floatval($teacher['avg_rating'])) ?></span>
                            <span class="text-muted small"><?= number_format($teacher['avg_rating'], 1) ?> (<?= count($reviews) ?>)</span>
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
                                    <?php if ($review['review_text']): ?>
                                    <p class="comment-text mb-0"><?= e($review['review_text']) ?></p>
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
                            <p class="text-muted small mb-0"><a href="auth/login.php">Войдите</a>, чтобы оставить отзыв</p>
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
