<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'teacher') { header('Location: /dashboard.php'); exit; }

$pageTitle = 'Выставление оценок';
$userId = $_SESSION['user_id'];

// Выставление оценки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_grade') {
    $studentId = intval($_POST['student_id'] ?? 0);
    $subject = trim($_POST['subject'] ?? '');
    $grade = intval($_POST['grade'] ?? 0);
    
    if ($studentId > 0 && !empty($subject) && $grade >= 1 && $grade <= 5) {
        // Получаем имя студента и группу
        $stmt = $pdo->prepare("SELECT full_name, group_name FROM users WHERE id = ?");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();
        
        if ($student) {
            $stmt = $pdo->prepare("INSERT INTO grades (student_id, student_name, group_name, teacher_id, subject, grade, date) VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
            $stmt->execute([$studentId, $student['full_name'], $student['group_name'], $userId, $subject, $grade]);
            header('Location: grades.php?success=1');
            exit;
        }
    }
    header('Location: grades.php?error=1');
    exit;
}

$groups = $pdo->query("SELECT DISTINCT group_name FROM users WHERE role='student' AND group_name IS NOT NULL AND group_name != '' ORDER BY group_name")->fetchAll(PDO::FETCH_COLUMN);
$selectedGroup = $_GET['group'] ?? ($groups[0] ?? '');

$students = [];
if ($selectedGroup) {
    $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE role='student' AND group_name = ? ORDER BY full_name");
    $stmt->execute([$selectedGroup]);
    $students = $stmt->fetchAll();
}

$stmt = $pdo->prepare("SELECT g.*, u.full_name as student_name, u.group_name FROM grades g LEFT JOIN users u ON g.student_id = u.id WHERE g.teacher_id = ? ORDER BY g.created_at DESC LIMIT 50");
$stmt->execute([$userId]);
$grades = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Преподавание</span><h1 class="section-title">Выставление оценок</h1></div>

    <?php if (isset($_GET['success'])): ?>
    <div class="notification success">
        <i class="bi bi-check-circle"></i>
        <div class="notification-content">
            <p class="notification-text mb-0">Оценка выставлена!</p>
        </div>
    </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
    <div class="notification error">
        <i class="bi bi-exclamation-triangle"></i>
        <div class="notification-content">
            <p class="notification-text mb-0">Ошибка при выставлении оценки</p>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card"><div class="card-header"><i class="bi bi-plus-circle me-2"></i>Выставить оценку</div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <label class="form-label">Группа</label>
                        <select name="group" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($groups as $g): ?>
                            <option value="<?= e($g) ?>" <?= $selectedGroup === $g ? 'selected' : '' ?>><?= e($g) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <?php if ($selectedGroup): ?>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_grade">
                        <div class="mb-2"><label class="form-label">Студент</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Выберите...</option>
                                <?php foreach ($students as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= e($s['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2"><label class="form-label">Предмет</label><input type="text" name="subject" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Оценка</label>
                            <select name="grade" class="form-select" required>
                                <option value="5">5 (Отлично)</option><option value="4">4 (Хорошо)</option>
                                <option value="3">3 (Удовл.)</option><option value="2">2 (Неуд.)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-accent w-100">Сохранить</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card"><div class="card-header"><i class="bi bi-list-ul me-2"></i>Журнал оценок</div>
                <div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
                    <thead><tr><th>Дата</th><th>Студент</th><th>Группа</th><th>Предмет</th><th>Оценка</th></tr></thead>
                    <tbody>
                        <?php if (empty($grades)): ?>
                        <tr><td colspan="5" class="text-center">Нет оценок</td></tr>
                        <?php else: ?>
                        <?php foreach ($grades as $g): ?>
                        <?php $bc = $g['grade'] >= 4 ? 'bg-success' : ($g['grade'] == 3 ? 'bg-warning' : 'bg-danger'); ?>
                        <tr>
                            <td><?= date('d.m.Y', strtotime($g['created_at'])) ?></td>
                            <td><?= e($g['student_name'] ?? '—') ?></td>
                            <td><?= e($g['group_name'] ?? '—') ?></td>
                            <td><?= e($g['subject']) ?></td>
                            <td><span class="badge <?= $bc ?>"><?= $g['grade'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table></div></div>
            </div>
        </div>
    </div>
</div></div>

<?php require_once '../includes/footer.php'; ?>
