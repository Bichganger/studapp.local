<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

$user = getCurrentUser();
$pageTitle = 'Управление преподавателями';

// Проверка наличия колонок
$hasCampus = $hasStrict = $hasAutoExam = $hasAvgRating = false;
try {
    $cols = $pdo->query("SHOW COLUMNS FROM teachers")->fetchAll(PDO::FETCH_COLUMN);
    $hasCampus = in_array('campus', $cols);
    $hasStrict = in_array('is_strict', $cols);
    $hasAutoExam = in_array('auto_exam', $cols);
    $hasAvgRating = in_array('avg_rating', $cols);
} catch (Exception $e) {}

// Добавление преподавателя
$editErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_teacher') {
    $full_name = trim($_POST['full_name'] ?? '');
    $short_name = trim($_POST['short_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $office = trim($_POST['office'] ?? '');
    $specialty = trim($_POST['specialty'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $campus = $hasCampus ? trim($_POST['campus'] ?? '') : null;
    $is_strict = $hasStrict ? (int)($_POST['is_strict'] ?? 0) : 0;
    $auto_exam = $hasAutoExam ? (int)($_POST['auto_exam'] ?? 0) : 0;
    
    if (empty($full_name)) $editErrors[] = 'Укажите ФИО';
    if (empty($specialty)) $editErrors[] = 'Укажите специализацию';
    
    if (empty($editErrors)) {
        $fields = ['full_name', 'short_name', 'email', 'phone', 'office', 'specialty', 'description'];
        $values = [$full_name, $short_name, $email, $phone, $office, $specialty, $description];
        
        if ($hasCampus) { $fields[] = 'campus'; $values[] = $campus; }
        if ($hasStrict) { $fields[] = 'is_strict'; $values[] = $is_strict; }
        if ($hasAutoExam) { $fields[] = 'auto_exam'; $values[] = $auto_exam; }
        
        $sql = "INSERT INTO teachers (" . implode(',', $fields) . ") VALUES (" . str_repeat('?,', count($fields)-1) . "?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        header('Location: manage_teachers.php?success=1');
        exit;
    }
}

// Редактирование преподавателя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_teacher') {
    $id = intval($_POST['id'] ?? 0);
    $full_name = trim($_POST['full_name'] ?? '');
    $short_name = trim($_POST['short_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $office = trim($_POST['office'] ?? '');
    $specialty = trim($_POST['specialty'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $campus = $hasCampus ? trim($_POST['campus'] ?? '') : null;
    $is_strict = $hasStrict ? (int)($_POST['is_strict'] ?? 0) : 0;
    $auto_exam = $hasAutoExam ? (int)($_POST['auto_exam'] ?? 0) : 0;
    
    if ($id > 0 && !empty($full_name)) {
        $fields = ['full_name', 'short_name', 'email', 'phone', 'office', 'specialty', 'description'];
        $values = [$full_name, $short_name, $email, $phone, $office, $specialty, $description];
        
        if ($hasCampus) { $fields[] = 'campus'; $values[] = $campus; }
        if ($hasStrict) { $fields[] = 'is_strict'; $values[] = $is_strict; }
        if ($hasAutoExam) { $fields[] = 'auto_exam'; $values[] = $auto_exam; }
        
        $sql = "UPDATE teachers SET " . implode('=?, ', $fields) . "=? WHERE id = ?";
        $values[] = $id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        header('Location: manage_teachers.php?success=1');
        exit;
    }
}

// Удаление преподавателя
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM teachers WHERE id = ?")->execute([intval($_GET['delete'])]);
    header('Location: manage_teachers.php?deleted=1');
    exit;
}

// Получение списка преподавателей
if ($hasAvgRating) {
    $teachers = $pdo->query("SELECT t.*, (SELECT COUNT(*) FROM teacher_reviews r WHERE r.teacher_id = t.id AND r.is_approved = 1 AND r.is_hidden = 0) as review_count FROM teachers t ORDER BY t.full_name")->fetchAll();
} else {
    $teachers = $pdo->query("SELECT t.*, COALESCE((SELECT ROUND(AVG(r.rating), 1) FROM teacher_reviews r WHERE r.teacher_id = t.id AND r.is_approved = 1 AND r.is_hidden = 0), 0) as avg_rating, (SELECT COUNT(*) FROM teacher_reviews r WHERE r.teacher_id = t.id AND r.is_approved = 1 AND r.is_hidden = 0) as review_count FROM teachers t ORDER BY t.full_name")->fetchAll();
}

// Получение выбранного преподавателя для редактирования
$editingTeacher = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editingTeacher = $stmt->fetch();
}

$campuses = ['Брамса 9', 'Спортивная 6', 'Озерова 7'];

require_once '../includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Администрирование</span>
            <h1 class="section-title">Управление преподавателями</h1>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Данные сохранены!</div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-info"><i class="bi bi-trash me-2"></i>Преподаватель удалён</div>
        <?php endif; ?>
        <?php if (!empty($editErrors)): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($editErrors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-person-plus me-2"></i><?= $editingTeacher ? 'Редактировать' : 'Добавить' ?> преподавателя
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?= $editingTeacher ? 'edit_teacher' : 'add_teacher' ?>">
                            <?php if ($editingTeacher): ?><input type="hidden" name="id" value="<?= $editingTeacher['id'] ?>"><?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label">ФИО *</label>
                                <input type="text" name="full_name" class="form-control" required 
                                       value="<?= e($editingTeacher['full_name'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Краткое имя</label>
                                <input type="text" name="short_name" class="form-control" 
                                       placeholder="Иванов П.С." value="<?= e($editingTeacher['short_name'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= e($editingTeacher['email'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Телефон</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="<?= e($editingTeacher['phone'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Кабинет</label>
                                <input type="text" name="office" class="form-control" 
                                       value="<?= e($editingTeacher['office'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Специализация *</label>
                                <input type="text" name="specialty" class="form-control" required
                                       placeholder="Предметы" value="<?= e($editingTeacher['specialty'] ?? '') ?>">
                            </div>
                            
                            <?php if ($hasCampus): ?>
                            <div class="mb-3">
                                <label class="form-label">Корпус</label>
                                <select name="campus" class="form-select">
                                    <option value="">Выберите</option>
                                    <?php foreach ($campuses as $c): ?>
                                    <option value="<?= e($c) ?>" <?= ($editingTeacher['campus'] ?? '') === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label">Описание</label>
                                <textarea name="description" class="form-control" rows="3"><?= e($editingTeacher['description'] ?? '') ?></textarea>
                            </div>
                            
                            <?php if ($hasStrict): ?>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_strict" class="form-check-input" value="1" <?= !empty($editingTeacher['is_strict']) ? 'checked' : '' ?>>
                                <label class="form-check-label">Строгий преподаватель</label>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($hasAutoExam): ?>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="auto_exam" class="form-check-input" value="1" <?= !empty($editingTeacher['auto_exam']) ? 'checked' : '' ?>>
                                <label class="form-check-label">Даёт автоматы</label>
                            </div>
                            <?php endif; ?>
                            
                            <button type="submit" class="btn btn-accent w-100">
                                <?= $editingTeacher ? 'Сохранить' : 'Добавить' ?>
                            </button>
                            <?php if ($editingTeacher): ?>
                            <a href="manage_teachers.php" class="btn btn-outline-light w-100 mt-2">Отмена</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul me-2"></i>Список преподавателей (<?= count($teachers) ?>)
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                        <thead>
                                <tr>
                                        <th>ФИО</th>
                                        <th>Специализация</th>
                                        <th>Корпус</th>
                                        <th>Рейтинг</th>
                                        <th>Отзывов</th>
                                        <th>Действия</th>
                                </tr>
                        </thead>
                                <tbody>
                                    <?php if (empty($teachers)): ?>
                                    <tr><td colspan="6" class="text-center py-3">Нет преподавателей</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($teachers as $t): ?>
                                    <tr>
                                        <td><strong><?= e($t['full_name']) ?></strong></td>
                                        <td><?= e(mb_substr($t['specialty'] ?? '', 0, 40)) ?></td>
                                        <td><span class="badge bg-secondary"><?= e($t['campus'] ?? '—') ?></span></td>
                                        <td>
                                            <?php 
                                            $rating = floatval($t['avg_rating'] ?? 0);
                                            $percentage = ($rating / 5) * 100;
                                            $circumference = 2 * M_PI * 14;
                                            $offset = $circumference - ($percentage / 100) * $circumference;
                                            
                                            if ($rating < 2) $color = '#ff5252';
                                            elseif ($rating < 3.5) $color = '#ffd740';
                                            else $color = '#00e676';
                                            ?>
                                            <svg width="32" height="32" viewBox="0 0 32 32">
                                                <circle cx="16" cy="16" r="14" stroke="var(--border-color)" stroke-width="3" fill="none"/>
                                                <circle cx="16" cy="16" r="14" stroke="<?= $color ?>" stroke-width="3" fill="none" stroke-dasharray="<?= $circumference ?>" stroke-dashoffset="<?= $offset ?>" transform="rotate(-90 16 16)" stroke-linecap="round"/>
                                            </svg>
                                            <small><?= number_format($rating, 1) ?></small>
                                        </td>
                                        <td><?= $t['review_count'] ?? 0 ?></td>
                                        <td>
                                            <a href="?edit=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
